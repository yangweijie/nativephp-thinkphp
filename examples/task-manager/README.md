# 任务管理器示例

这个示例展示了如何使用 NativePHP for ThinkPHP 创建一个简单的任务管理器应用。

## 功能

- 创建、编辑和删除任务
- 任务分类和标签
- 任务优先级和截止日期
- 任务提醒和通知
- 任务统计和报表
- 数据导入和导出
- 快捷键支持

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Task.php` - 任务控制器
- `app/model/Task.php` - 任务模型
- `app/model/Category.php` - 分类模型
- `app/model/Tag.php` - 标签模型
- `app/service/TaskService.php` - 任务服务
- `view/index/index.html` - 主页面
- `view/task/edit.html` - 任务编辑页面
- `view/task/view.html` - 任务查看页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式

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
use app\model\Task;
use app\model\Category;
use app\model\Tag;
use app\service\TaskService;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\FileSystem;

class Task extends BaseController
{
    protected $taskService;
    
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    
    public function index()
    {
        $category_id = input('category_id', 0);
        $status = input('status', '');
        $priority = input('priority', '');
        
        $tasks = $this->taskService->getTasks($category_id, $status, $priority);
        $categories = Category::select();
        
        return view('task/index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'category_id' => $category_id,
            'status' => $status,
            'priority' => $priority,
        ]);
    }
    
    public function create()
    {
        $categories = Category::select();
        $tags = Tag::select();
        
        return view('task/create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
    
    public function save()
    {
        $data = input('post.');
        
        $result = $this->taskService->createTask($data);
        
        if ($result) {
            Notification::send('任务创建成功', '任务 "' . $data['title'] . '" 已创建');
            return json(['success' => true, 'message' => '任务创建成功']);
        } else {
            return json(['success' => false, 'message' => '任务创建失败']);
        }
    }
    
    public function edit($id)
    {
        $task = Task::find($id);
        
        if (!$task) {
            return redirect('/task/index');
        }
        
        $categories = Category::select();
        $tags = Tag::select();
        $taskTags = $task->tags()->column('id');
        
        return view('task/edit', [
            'task' => $task,
            'categories' => $categories,
            'tags' => $tags,
            'taskTags' => $taskTags,
        ]);
    }
    
    public function update()
    {
        $data = input('post.');
        
        $result = $this->taskService->updateTask($data);
        
        if ($result) {
            Notification::send('任务更新成功', '任务 "' . $data['title'] . '" 已更新');
            return json(['success' => true, 'message' => '任务更新成功']);
        } else {
            return json(['success' => false, 'message' => '任务更新失败']);
        }
    }
    
    public function delete($id)
    {
        $task = Task::find($id);
        
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在']);
        }
        
        $result = $task->delete();
        
        if ($result) {
            Notification::send('任务删除成功', '任务 "' . $task->title . '" 已删除');
            return json(['success' => true, 'message' => '任务删除成功']);
        } else {
            return json(['success' => false, 'message' => '任务删除失败']);
        }
    }
    
    public function complete($id)
    {
        $task = Task::find($id);
        
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在']);
        }
        
        $task->status = 'completed';
        $task->completed_at = date('Y-m-d H:i:s');
        $result = $task->save();
        
        if ($result) {
            Notification::send('任务完成', '任务 "' . $task->title . '" 已完成');
            return json(['success' => true, 'message' => '任务已完成']);
        } else {
            return json(['success' => false, 'message' => '操作失败']);
        }
    }
    
    public function export()
    {
        $format = input('format', 'json');
        
        $path = $this->taskService->exportTasks($format);
        
        if ($path) {
            return json(['success' => true, 'path' => $path]);
        } else {
            return json(['success' => false, 'message' => '导出失败']);
        }
    }
    
    public function import()
    {
        $file = Dialog::openFile([
            'title' => '选择导入文件',
            'filters' => [
                ['name' => 'JSON', 'extensions' => ['json']],
                ['name' => 'CSV', 'extensions' => ['csv']],
            ],
        ]);
        
        if (!$file) {
            return json(['success' => false, 'message' => '未选择文件']);
        }
        
        $result = $this->taskService->importTasks($file);
        
        if ($result) {
            Notification::send('导入成功', '任务已成功导入');
            return json(['success' => true, 'message' => '导入成功']);
        } else {
            return json(['success' => false, 'message' => '导入失败']);
        }
    }
    
    public function statistics()
    {
        $stats = $this->taskService->getStatistics();
        
        return view('task/statistics', [
            'stats' => $stats,
        ]);
    }
}
```

### 服务

```php
<?php

namespace app\service;

use app\model\Task;
use app\model\Category;
use app\model\Tag;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class TaskService
{
    /**
     * 获取任务列表
     *
     * @param int $category_id
     * @param string $status
     * @param string $priority
     * @return \think\Collection
     */
    public function getTasks($category_id = 0, $status = '', $priority = '')
    {
        $query = Task::order('created_at', 'desc');
        
        if ($category_id > 0) {
            $query->where('category_id', $category_id);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        return $query->select();
    }
    
    /**
     * 创建任务
     *
     * @param array $data
     * @return bool
     */
    public function createTask($data)
    {
        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'] ?? '';
        $task->category_id = $data['category_id'] ?? 0;
        $task->priority = $data['priority'] ?? 'normal';
        $task->status = 'pending';
        $task->due_date = $data['due_date'] ?? null;
        $task->created_at = date('Y-m-d H:i:s');
        $task->updated_at = date('Y-m-d H:i:s');
        
        $result = $task->save();
        
        if ($result && isset($data['tags'])) {
            $task->tags()->attach($data['tags']);
        }
        
        // 如果设置了截止日期，创建提醒
        if ($result && !empty($data['due_date'])) {
            $this->createReminder($task);
        }
        
        return $result;
    }
    
    /**
     * 更新任务
     *
     * @param array $data
     * @return bool
     */
    public function updateTask($data)
    {
        $task = Task::find($data['id']);
        
        if (!$task) {
            return false;
        }
        
        $task->title = $data['title'];
        $task->description = $data['description'] ?? '';
        $task->category_id = $data['category_id'] ?? 0;
        $task->priority = $data['priority'] ?? 'normal';
        $task->due_date = $data['due_date'] ?? null;
        $task->updated_at = date('Y-m-d H:i:s');
        
        $result = $task->save();
        
        if ($result && isset($data['tags'])) {
            $task->tags()->detach();
            $task->tags()->attach($data['tags']);
        }
        
        // 如果更新了截止日期，更新提醒
        if ($result && !empty($data['due_date'])) {
            $this->createReminder($task);
        }
        
        return $result;
    }
    
    /**
     * 创建任务提醒
     *
     * @param Task $task
     * @return void
     */
    protected function createReminder($task)
    {
        // 获取提醒设置
        $reminderSettings = Settings::get('task.reminders', [
            'enabled' => true,
            'time_before' => 30, // 分钟
        ]);
        
        if (!$reminderSettings['enabled'] || empty($task->due_date)) {
            return;
        }
        
        $dueDate = strtotime($task->due_date);
        $reminderTime = $dueDate - ($reminderSettings['time_before'] * 60);
        
        // 如果提醒时间已经过去，不创建提醒
        if ($reminderTime < time()) {
            return;
        }
        
        // 保存提醒信息
        $reminders = Settings::get('task.reminder_list', []);
        $reminders[] = [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'due_date' => $task->due_date,
            'reminder_time' => date('Y-m-d H:i:s', $reminderTime),
        ];
        
        Settings::set('task.reminder_list', $reminders);
    }
    
    /**
     * 导出任务
     *
     * @param string $format
     * @return string|bool
     */
    public function exportTasks($format = 'json')
    {
        $tasks = Task::with(['category', 'tags'])->select()->toArray();
        
        if (empty($tasks)) {
            return false;
        }
        
        $exportDir = runtime_path() . 'exports/';
        
        if (!is_dir($exportDir)) {
            FileSystem::makeDirectory($exportDir, 0755, true);
        }
        
        $filename = 'tasks_' . date('YmdHis');
        
        if ($format === 'json') {
            $path = $exportDir . $filename . '.json';
            $content = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            FileSystem::write($path, $content);
            return $path;
        } elseif ($format === 'csv') {
            $path = $exportDir . $filename . '.csv';
            $fp = fopen($path, 'w');
            
            // 写入表头
            fputcsv($fp, ['ID', '标题', '描述', '分类', '优先级', '状态', '截止日期', '创建时间', '更新时间', '完成时间', '标签']);
            
            // 写入数据
            foreach ($tasks as $task) {
                $tags = [];
                if (isset($task['tags'])) {
                    foreach ($task['tags'] as $tag) {
                        $tags[] = $tag['name'];
                    }
                }
                
                fputcsv($fp, [
                    $task['id'],
                    $task['title'],
                    $task['description'],
                    $task['category']['name'] ?? '',
                    $task['priority'],
                    $task['status'],
                    $task['due_date'],
                    $task['created_at'],
                    $task['updated_at'],
                    $task['completed_at'],
                    implode(', ', $tags),
                ]);
            }
            
            fclose($fp);
            return $path;
        }
        
        return false;
    }
    
    /**
     * 导入任务
     *
     * @param string $file
     * @return bool
     */
    public function importTasks($file)
    {
        if (!FileSystem::exists($file)) {
            return false;
        }
        
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        
        if ($extension === 'json') {
            $content = FileSystem::read($file);
            $tasks = json_decode($content, true);
            
            if (!is_array($tasks)) {
                return false;
            }
            
            foreach ($tasks as $taskData) {
                $task = new Task();
                $task->title = $taskData['title'];
                $task->description = $taskData['description'] ?? '';
                $task->priority = $taskData['priority'] ?? 'normal';
                $task->status = $taskData['status'] ?? 'pending';
                $task->due_date = $taskData['due_date'] ?? null;
                $task->created_at = date('Y-m-d H:i:s');
                $task->updated_at = date('Y-m-d H:i:s');
                
                // 处理分类
                if (isset($taskData['category']['name'])) {
                    $category = Category::where('name', $taskData['category']['name'])->find();
                    if ($category) {
                        $task->category_id = $category->id;
                    } else {
                        $newCategory = new Category();
                        $newCategory->name = $taskData['category']['name'];
                        $newCategory->save();
                        $task->category_id = $newCategory->id;
                    }
                }
                
                $task->save();
                
                // 处理标签
                if (isset($taskData['tags']) && is_array($taskData['tags'])) {
                    $tagIds = [];
                    foreach ($taskData['tags'] as $tagData) {
                        $tag = Tag::where('name', $tagData['name'])->find();
                        if ($tag) {
                            $tagIds[] = $tag->id;
                        } else {
                            $newTag = new Tag();
                            $newTag->name = $tagData['name'];
                            $newTag->save();
                            $tagIds[] = $newTag->id;
                        }
                    }
                    
                    if (!empty($tagIds)) {
                        $task->tags()->attach($tagIds);
                    }
                }
            }
            
            return true;
        } elseif ($extension === 'csv') {
            $fp = fopen($file, 'r');
            
            // 跳过表头
            fgetcsv($fp);
            
            while (($data = fgetcsv($fp)) !== false) {
                if (count($data) < 10) {
                    continue;
                }
                
                $task = new Task();
                $task->title = $data[1];
                $task->description = $data[2];
                $task->priority = $data[4];
                $task->status = $data[5];
                $task->due_date = $data[6];
                $task->created_at = date('Y-m-d H:i:s');
                $task->updated_at = date('Y-m-d H:i:s');
                
                // 处理分类
                if (!empty($data[3])) {
                    $category = Category::where('name', $data[3])->find();
                    if ($category) {
                        $task->category_id = $category->id;
                    } else {
                        $newCategory = new Category();
                        $newCategory->name = $data[3];
                        $newCategory->save();
                        $task->category_id = $newCategory->id;
                    }
                }
                
                $task->save();
                
                // 处理标签
                if (!empty($data[10])) {
                    $tagNames = explode(',', $data[10]);
                    $tagIds = [];
                    
                    foreach ($tagNames as $tagName) {
                        $tagName = trim($tagName);
                        if (empty($tagName)) {
                            continue;
                        }
                        
                        $tag = Tag::where('name', $tagName)->find();
                        if ($tag) {
                            $tagIds[] = $tag->id;
                        } else {
                            $newTag = new Tag();
                            $newTag->name = $tagName;
                            $newTag->save();
                            $tagIds[] = $newTag->id;
                        }
                    }
                    
                    if (!empty($tagIds)) {
                        $task->tags()->attach($tagIds);
                    }
                }
            }
            
            fclose($fp);
            return true;
        }
        
        return false;
    }
    
    /**
     * 获取任务统计信息
     *
     * @return array
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('status', 'pending')
                ->where('due_date', '<', date('Y-m-d'))
                ->count(),
            'by_priority' => [
                'high' => Task::where('priority', 'high')->count(),
                'normal' => Task::where('priority', 'normal')->count(),
                'low' => Task::where('priority', 'low')->count(),
            ],
            'by_category' => [],
        ];
        
        // 按分类统计
        $categories = Category::select();
        foreach ($categories as $category) {
            $stats['by_category'][$category->name] = Task::where('category_id', $category->id)->count();
        }
        
        return $stats;
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
    <title>任务管理器</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>任务管理器</h1>
            <nav>
                <ul>
                    <li><a href="/task/index">所有任务</a></li>
                    <li><a href="/task/index?status=pending">待办任务</a></li>
                    <li><a href="/task/index?status=completed">已完成任务</a></li>
                    <li><a href="/task/index?priority=high">高优先级</a></li>
                    <li><a href="/task/statistics">统计报表</a></li>
                </ul>
            </nav>
            <div class="categories">
                <h3>分类</h3>
                <ul>
                    {foreach $categories as $category}
                    <li><a href="/task/index?category_id={$category.id}">{$category.name}</a></li>
                    {/foreach}
                </ul>
            </div>
            <div class="actions">
                <button onclick="createTask()">新建任务</button>
                <button onclick="importTasks()">导入任务</button>
                <button onclick="exportTasks()">导出任务</button>
            </div>
        </div>
        <div class="content">
            <div class="header">
                <h2>任务列表</h2>
                <div class="filters">
                    <select id="statusFilter" onchange="filterTasks()">
                        <option value="">所有状态</option>
                        <option value="pending" {$status == 'pending' ? 'selected' : ''}>待办</option>
                        <option value="completed" {$status == 'completed' ? 'selected' : ''}>已完成</option>
                    </select>
                    <select id="priorityFilter" onchange="filterTasks()">
                        <option value="">所有优先级</option>
                        <option value="high" {$priority == 'high' ? 'selected' : ''}>高</option>
                        <option value="normal" {$priority == 'normal' ? 'selected' : ''}>中</option>
                        <option value="low" {$priority == 'low' ? 'selected' : ''}>低</option>
                    </select>
                </div>
            </div>
            <div class="task-list">
                {foreach $tasks as $task}
                <div class="task-item priority-{$task.priority}">
                    <div class="task-status">
                        <input type="checkbox" {$task.status == 'completed' ? 'checked' : ''} onchange="completeTask({$task.id})">
                    </div>
                    <div class="task-content">
                        <h3>{$task.title}</h3>
                        <p>{$task.description}</p>
                        <div class="task-meta">
                            {if $task.category_id > 0}
                            <span class="category">{$task.category.name}</span>
                            {/if}
                            <span class="priority">{$task.priority}</span>
                            {if $task.due_date}
                            <span class="due-date">截止: {$task.due_date}</span>
                            {/if}
                        </div>
                        <div class="task-tags">
                            {foreach $task.tags as $tag}
                            <span class="tag">{$tag.name}</span>
                            {/foreach}
                        </div>
                    </div>
                    <div class="task-actions">
                        <button onclick="editTask({$task.id})">编辑</button>
                        <button onclick="deleteTask({$task.id})">删除</button>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```

## 使用的 NativePHP 功能

- **Notification**: 用于发送任务提醒和操作通知
- **Dialog**: 用于文件选择和确认对话框
- **GlobalShortcut**: 用于注册全局快捷键
- **Database**: 用于任务数据存储
- **FileSystem**: 用于导入导出文件操作
- **Settings**: 用于存储应用设置和提醒信息
