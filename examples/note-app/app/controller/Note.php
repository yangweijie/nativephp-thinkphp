<?php

namespace app\controller;

use app\BaseController;
use app\model\Note as NoteModel;
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
        
        $note = NoteModel::find($id);
        
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
        
        $note = NoteModel::find($id);
        
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
            $note = NoteModel::find($id);
            
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
            $note = new NoteModel;
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
        
        $note = NoteModel::find($id);
        
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
        
        $note = NoteModel::find($id);
        
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
        $note = new NoteModel;
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
