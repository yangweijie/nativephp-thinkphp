# 笔记应用示例

这个示例展示了如何使用 NativePHP for ThinkPHP 创建一个支持 Markdown 的笔记应用。

## 功能

- 创建、编辑和删除笔记
- Markdown 编辑和预览
- 笔记分类和标签
- 笔记搜索
- 导入和导出笔记
- 自动保存
- 快捷键支持

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Note.php` - 笔记控制器
- `app/model/Note.php` - 笔记模型
- `app/model/Category.php` - 分类模型
- `app/model/Tag.php` - 标签模型
- `app/service/NoteService.php` - 笔记服务
- `view/index/index.html` - 主页面
- `view/note/edit.html` - 笔记编辑页面
- `view/note/view.html` - 笔记查看页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式
- `public/static/js/markdown.js` - Markdown 解析库

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\Note;
use app\model\Category;
use app\model\Tag;
use app\service\NoteService;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\FileSystem;

class Note extends BaseController
{
    protected $noteService;
    
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
        
        // 注册全局快捷键
        $this->registerShortcuts();
    }
    
    protected function registerShortcuts()
    {
        // 新建笔记
        GlobalShortcut::register('CommandOrControl+N', function () {
            $this->create();
        });
        
        // 保存笔记
        GlobalShortcut::register('CommandOrControl+S', function () {
            $this->save();
        });
        
        // 搜索笔记
        GlobalShortcut::register('CommandOrControl+F', function () {
            $this->search();
        });
    }
    
    public function index()
    {
        $categoryId = input('category_id', 0);
        $tag = input('tag', '');
        $search = input('search', '');
        
        $notes = $this->noteService->getNotes($categoryId, $tag, $search);
        $categories = Category::select();
        $tags = Tag::select();
        
        return view('note/index', [
            'notes' => $notes,
            'categories' => $categories,
            'tags' => $tags,
            'currentCategory' => $categoryId,
            'currentTag' => $tag,
            'search' => $search,
        ]);
    }
    
    public function view()
    {
        $id = input('id');
        
        if (empty($id)) {
            return redirect('/note/index');
        }
        
        $note = Note::find($id);
        
        if (!$note) {
            Notification::send('错误', '笔记不存在');
            return redirect('/note/index');
        }
        
        return view('note/view', [
            'note' => $note,
        ]);
    }
    
    public function edit()
    {
        $id = input('id');
        
        if (empty($id)) {
            return redirect('/note/index');
        }
        
        $note = Note::find($id);
        
        if (!$note) {
            Notification::send('错误', '笔记不存在');
            return redirect('/note/index');
        }
        
        $categories = Category::select();
        $tags = Tag::select();
        
        return view('note/edit', [
            'note' => $note,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
    
    public function create()
    {
        $categories = Category::select();
        $tags = Tag::select();
        
        return view('note/create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
    
    public function save()
    {
        $id = input('id');
        $title = input('title');
        $content = input('content');
        $categoryId = input('category_id', 0);
        $tags = input('tags/a', []);
        
        if (empty($title)) {
            return json(['success' => false, 'message' => '标题不能为空']);
        }
        
        if ($id) {
            // 更新笔记
            $note = Note::find($id);
            
            if (!$note) {
                return json(['success' => false, 'message' => '笔记不存在']);
            }
            
            $note->title = $title;
            $note->content = $content;
            $note->category_id = $categoryId;
            $note->updated_at = date('Y-m-d H:i:s');
            $note->save();
            
            // 更新标签
            $this->noteService->updateNoteTags($note->id, $tags);
            
            Notification::send('保存成功', '笔记已更新');
            
            return json(['success' => true, 'id' => $note->id]);
        } else {
            // 创建笔记
            $note = new Note;
            $note->title = $title;
            $note->content = $content;
            $note->category_id = $categoryId;
            $note->created_at = date('Y-m-d H:i:s');
            $note->updated_at = date('Y-m-d H:i:s');
            $note->save();
            
            // 添加标签
            $this->noteService->updateNoteTags($note->id, $tags);
            
            Notification::send('创建成功', '笔记已创建');
            
            return json(['success' => true, 'id' => $note->id]);
        }
    }
    
    public function delete()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '笔记ID不能为空']);
        }
        
        $note = Note::find($id);
        
        if (!$note) {
            return json(['success' => false, 'message' => '笔记不存在']);
        }
        
        // 删除笔记标签关联
        $this->noteService->deleteNoteTags($id);
        
        // 删除笔记
        $note->delete();
        
        Notification::send('删除成功', '笔记已删除');
        
        return json(['success' => true]);
    }
    
    public function search()
    {
        $keyword = input('keyword');
        
        if (empty($keyword)) {
            return json(['success' => true, 'notes' => []]);
        }
        
        $notes = $this->noteService->searchNotes($keyword);
        
        return json(['success' => true, 'notes' => $notes]);
    }
    
    public function export()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '笔记ID不能为空']);
        }
        
        $note = Note::find($id);
        
        if (!$note) {
            return json(['success' => false, 'message' => '笔记不存在']);
        }
        
        $path = Dialog::saveFile([
            'title' => '导出笔记',
            'defaultPath' => $note->title . '.md',
            'filters' => [
                ['name' => 'Markdown', 'extensions' => ['md']],
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$path) {
            return json(['success' => false, 'message' => '导出取消']);
        }
        
        $content = "# {$note->title}\n\n{$note->content}";
        
        if (FileSystem::write($path, $content)) {
            Notification::send('导出成功', '笔记已导出到：' . $path);
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '导出失败']);
        }
    }
    
    public function import()
    {
        $path = Dialog::openFile([
            'title' => '导入笔记',
            'filters' => [
                ['name' => 'Markdown', 'extensions' => ['md']],
                ['name' => '文本文件', 'extensions' => ['txt']],
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$path) {
            return json(['success' => false, 'message' => '导入取消']);
        }
        
        $content = FileSystem::read($path);
        
        if ($content === false) {
            return json(['success' => false, 'message' => '读取文件失败']);
        }
        
        // 提取标题（假设第一行是标题）
        $lines = explode("\n", $content);
        $title = trim($lines[0], "# \t\n\r\0\x0B");
        
        if (empty($title)) {
            $title = basename($path, '.md');
        }
        
        // 移除标题行
        array_shift($lines);
        $content = implode("\n", $lines);
        
        // 创建笔记
        $note = new Note;
        $note->title = $title;
        $note->content = $content;
        $note->category_id = 0;
        $note->created_at = date('Y-m-d H:i:s');
        $note->updated_at = date('Y-m-d H:i:s');
        $note->save();
        
        Notification::send('导入成功', '笔记已导入');
        
        return json(['success' => true, 'id' => $note->id]);
    }
    
    public function createCategory()
    {
        $name = input('name');
        
        if (empty($name)) {
            return json(['success' => false, 'message' => '分类名称不能为空']);
        }
        
        $category = new Category;
        $category->name = $name;
        $category->save();
        
        return json(['success' => true, 'id' => $category->id, 'name' => $category->name]);
    }
    
    public function createTag()
    {
        $name = input('name');
        
        if (empty($name)) {
            return json(['success' => false, 'message' => '标签名称不能为空']);
        }
        
        $tag = new Tag;
        $tag->name = $name;
        $tag->save();
        
        return json(['success' => true, 'id' => $tag->id, 'name' => $tag->name]);
    }
    
    public function backup()
    {
        $path = Dialog::saveFile([
            'title' => '备份笔记',
            'defaultPath' => 'notes_backup_' . date('Ymd') . '.db',
            'filters' => [
                ['name' => '数据库文件', 'extensions' => ['db']],
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$path) {
            return json(['success' => false, 'message' => '备份取消']);
        }
        
        if (Database::backup($path)) {
            Notification::send('备份成功', '笔记已备份到：' . $path);
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '备份失败']);
        }
    }
    
    public function restore()
    {
        $path = Dialog::openFile([
            'title' => '恢复笔记',
            'filters' => [
                ['name' => '数据库文件', 'extensions' => ['db']],
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$path) {
            return json(['success' => false, 'message' => '恢复取消']);
        }
        
        if (Database::restore($path)) {
            Notification::send('恢复成功', '笔记已从备份恢复');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '恢复失败']);
        }
    }
}
```

### 模型

```php
<?php

namespace app\model;

use think\Model;

class Note extends Model
{
    protected $table = 'notes';
    
    // 获取笔记的标签
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'note_tags', 'note_id', 'tag_id');
    }
    
    // 获取笔记的分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 服务

```php
<?php

namespace app\service;

use app\model\Note;
use app\model\Tag;
use think\facade\Db;

class NoteService
{
    /**
     * 获取笔记列表
     *
     * @param int $categoryId
     * @param string $tag
     * @param string $search
     * @return array
     */
    public function getNotes($categoryId = 0, $tag = '', $search = '')
    {
        $query = Note::order('updated_at', 'desc');
        
        // 按分类筛选
        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }
        
        // 按标签筛选
        if (!empty($tag)) {
            $query->whereExists(function ($query) use ($tag) {
                $query->table('note_tags')
                    ->join('tags', 'tags.id = note_tags.tag_id')
                    ->where('tags.name', $tag)
                    ->whereRaw('note_tags.note_id = notes.id');
            });
        }
        
        // 按关键词搜索
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->whereOr('content', 'like', "%{$search}%");
            });
        }
        
        // 获取笔记及其关联数据
        $notes = $query->with(['category', 'tags'])->select();
        
        return $notes;
    }
    
    /**
     * 搜索笔记
     *
     * @param string $keyword
     * @return array
     */
    public function searchNotes($keyword)
    {
        return Note::where('title', 'like', "%{$keyword}%")
            ->whereOr('content', 'like', "%{$keyword}%")
            ->order('updated_at', 'desc')
            ->select();
    }
    
    /**
     * 更新笔记标签
     *
     * @param int $noteId
     * @param array $tagIds
     * @return bool
     */
    public function updateNoteTags($noteId, array $tagIds)
    {
        // 删除旧的标签关联
        $this->deleteNoteTags($noteId);
        
        // 添加新的标签关联
        foreach ($tagIds as $tagId) {
            Db::table('note_tags')->insert([
                'note_id' => $noteId,
                'tag_id' => $tagId,
            ]);
        }
        
        return true;
    }
    
    /**
     * 删除笔记标签关联
     *
     * @param int $noteId
     * @return bool
     */
    public function deleteNoteTags($noteId)
    {
        return Db::table('note_tags')->where('note_id', $noteId)->delete();
    }
    
    /**
     * 初始化数据库
     *
     * @return bool
     */
    public function initDatabase()
    {
        // 创建笔记表
        Db::execute("
            CREATE TABLE IF NOT EXISTS notes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT,
                category_id INTEGER DEFAULT 0,
                created_at DATETIME,
                updated_at DATETIME
            )
        ");
        
        // 创建分类表
        Db::execute("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // 创建标签表
        Db::execute("
            CREATE TABLE IF NOT EXISTS tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )
        ");
        
        // 创建笔记标签关联表
        Db::execute("
            CREATE TABLE IF NOT EXISTS note_tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                note_id INTEGER NOT NULL,
                tag_id INTEGER NOT NULL,
                UNIQUE(note_id, tag_id)
            )
        ");
        
        return true;
    }
}
```

### 视图

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>笔记应用</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>笔记应用</h2>
                <button onclick="createNote()">新建笔记</button>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="搜索笔记..." onkeyup="searchNotes()">
            </div>
            
            <div class="categories">
                <h3>分类</h3>
                <ul>
                    <li class="{$currentCategory == 0 ? 'active' : ''}">
                        <a href="/note/index">全部笔记</a>
                    </li>
                    {volist name="categories" id="category"}
                    <li class="{$currentCategory == $category.id ? 'active' : ''}">
                        <a href="/note/index?category_id={$category.id}">{$category.name}</a>
                    </li>
                    {/volist}
                    <li class="add-category">
                        <a href="javascript:void(0)" onclick="createCategory()">+ 添加分类</a>
                    </li>
                </ul>
            </div>
            
            <div class="tags">
                <h3>标签</h3>
                <div class="tag-list">
                    {volist name="tags" id="tag"}
                    <span class="tag {$currentTag == $tag.name ? 'active' : ''}">
                        <a href="/note/index?tag={$tag.name}">{$tag.name}</a>
                    </span>
                    {/volist}
                    <span class="tag add-tag">
                        <a href="javascript:void(0)" onclick="createTag()">+</a>
                    </span>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <button onclick="backup()">备份</button>
                <button onclick="restore()">恢复</button>
            </div>
        </div>
        
        <div class="main-content">
            <div class="note-list">
                <div class="note-list-header">
                    <h3>笔记列表</h3>
                    <span>{:count($notes)} 个笔记</span>
                </div>
                
                <div class="notes">
                    {volist name="notes" id="note"}
                    <div class="note-item" onclick="viewNote({$note.id})">
                        <div class="note-title">{$note.title}</div>
                        <div class="note-preview">{:mb_substr(strip_tags($note.content), 0, 100)}</div>
                        <div class="note-meta">
                            <span class="note-date">{:date('Y-m-d H:i', strtotime($note.updated_at))}</span>
                            {if $note.category}
                            <span class="note-category">{$note.category.name}</span>
                            {/if}
                            <div class="note-tags">
                                {volist name="note.tags" id="tag"}
                                <span class="tag">{$tag.name}</span>
                                {/volist}
                            </div>
                        </div>
                        <div class="note-actions">
                            <button onclick="event.stopPropagation(); editNote({$note.id})">编辑</button>
                            <button onclick="event.stopPropagation(); deleteNote({$note.id})">删除</button>
                            <button onclick="event.stopPropagation(); exportNote({$note.id})">导出</button>
                        </div>
                    </div>
                    {/volist}
                </div>
            </div>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```
