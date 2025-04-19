<?php

namespace app\controller;

use app\BaseController;
use app\service\EditorService;
use app\service\ProjectService;
use app\service\SettingService;
use app\service\PluginService;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Settings;

class Index extends BaseController
{
    protected $editorService;
    protected $projectService;
    protected $settingService;
    protected $pluginService;
    
    public function __construct(
        EditorService $editorService,
        ProjectService $projectService,
        SettingService $settingService,
        PluginService $pluginService
    ) {
        $this->editorService = $editorService;
        $this->projectService = $projectService;
        $this->settingService = $settingService;
        $this->pluginService = $pluginService;
    }
    
    public function index()
    {
        // 创建应用菜单
        $this->createMenu();
        
        // 注册全局快捷键
        $this->registerShortcuts();
        
        // 获取设置
        $settings = $this->settingService->getSettings();
        
        // 获取最近项目
        $recentProjects = Settings::get('projects.recent', []);
        
        // 获取最近文件
        $recentFiles = Settings::get('files.recent', []);
        
        // 获取插件
        $plugins = $this->pluginService->getPlugins();
        
        return view('index/index', [
            'settings' => $settings,
            'recentProjects' => $recentProjects,
            'recentFiles' => $recentFiles,
            'plugins' => $plugins,
        ]);
    }
    
    protected function createMenu()
    {
        Menu::create()
            ->add('文件', [
                ['label' => '新建文件', 'accelerator' => 'CmdOrCtrl+N', 'click' => 'newFile'],
                ['label' => '打开文件...', 'accelerator' => 'CmdOrCtrl+O', 'click' => 'openFile'],
                ['label' => '打开文件夹...', 'accelerator' => 'CmdOrCtrl+Shift+O', 'click' => 'openFolder'],
                ['type' => 'separator'],
                ['label' => '保存', 'accelerator' => 'CmdOrCtrl+S', 'click' => 'saveFile'],
                ['label' => '另存为...', 'accelerator' => 'CmdOrCtrl+Shift+S', 'click' => 'saveFileAs'],
                ['type' => 'separator'],
                ['label' => '关闭编辑器', 'accelerator' => 'CmdOrCtrl+W', 'click' => 'closeEditor'],
                ['label' => '退出', 'accelerator' => 'CmdOrCtrl+Q', 'click' => 'quit'],
            ])
            ->submenu('编辑', function ($submenu) {
                $submenu->add('撤销', ['accelerator' => 'CmdOrCtrl+Z', 'click' => 'undo']);
                $submenu->add('重做', ['accelerator' => 'CmdOrCtrl+Shift+Z', 'click' => 'redo']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('剪切', ['accelerator' => 'CmdOrCtrl+X', 'click' => 'cut']);
                $submenu->add('复制', ['accelerator' => 'CmdOrCtrl+C', 'click' => 'copy']);
                $submenu->add('粘贴', ['accelerator' => 'CmdOrCtrl+V', 'click' => 'paste']);
                $submenu->add('全选', ['accelerator' => 'CmdOrCtrl+A', 'click' => 'selectAll']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('查找', ['accelerator' => 'CmdOrCtrl+F', 'click' => 'find']);
                $submenu->add('替换', ['accelerator' => 'CmdOrCtrl+H', 'click' => 'replace']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('格式化代码', ['accelerator' => 'Alt+Shift+F', 'click' => 'formatCode']);
            })
            ->submenu('视图', function ($submenu) {
                $submenu->add('外观', [
                    ['label' => '放大', 'accelerator' => 'CmdOrCtrl+Plus', 'click' => 'zoomIn'],
                    ['label' => '缩小', 'accelerator' => 'CmdOrCtrl+-', 'click' => 'zoomOut'],
                    ['label' => '重置缩放', 'accelerator' => 'CmdOrCtrl+0', 'click' => 'resetZoom'],
                    ['type' => 'separator'],
                    ['label' => '全屏', 'accelerator' => 'F11', 'click' => 'toggleFullScreen'],
                ]);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('显示资源管理器', ['accelerator' => 'CmdOrCtrl+Shift+E', 'click' => 'toggleExplorer']);
                $submenu->add('显示搜索', ['accelerator' => 'CmdOrCtrl+Shift+F', 'click' => 'toggleSearch']);
                $submenu->add('显示源代码管理', ['accelerator' => 'CmdOrCtrl+Shift+G', 'click' => 'toggleSCM']);
                $submenu->add('显示调试', ['accelerator' => 'CmdOrCtrl+Shift+D', 'click' => 'toggleDebug']);
                $submenu->add('显示扩展', ['accelerator' => 'CmdOrCtrl+Shift+X', 'click' => 'toggleExtensions']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('显示终端', ['accelerator' => 'CmdOrCtrl+`', 'click' => 'toggleTerminal']);
                $submenu->add('显示问题', ['accelerator' => 'CmdOrCtrl+Shift+M', 'click' => 'toggleProblems']);
                $submenu->add('显示输出', ['accelerator' => 'CmdOrCtrl+Shift+U', 'click' => 'toggleOutput']);
            })
            ->submenu('转到', function ($submenu) {
                $submenu->add('转到文件...', ['accelerator' => 'CmdOrCtrl+P', 'click' => 'goToFile']);
                $submenu->add('转到符号...', ['accelerator' => 'CmdOrCtrl+Shift+O', 'click' => 'goToSymbol']);
                $submenu->add('转到行...', ['accelerator' => 'CmdOrCtrl+G', 'click' => 'goToLine']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('后退', ['accelerator' => 'Alt+Left', 'click' => 'goBack']);
                $submenu->add('前进', ['accelerator' => 'Alt+Right', 'click' => 'goForward']);
            })
            ->submenu('终端', function ($submenu) {
                $submenu->add('新建终端', ['accelerator' => 'CmdOrCtrl+Shift+`', 'click' => 'newTerminal']);
                $submenu->add('分割终端', ['click' => 'splitTerminal']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('运行任务...', ['click' => 'runTask']);
                $submenu->add('配置任务...', ['click' => 'configureTask']);
            })
            ->submenu('帮助', function ($submenu) {
                $submenu->add('欢迎', ['click' => 'welcome']);
                $submenu->add('文档', ['click' => 'documentation']);
                $submenu->add('分隔线', ['type' => 'separator']);
                $submenu->add('检查更新...', ['click' => 'checkUpdate']);
                $submenu->add('关于', ['click' => 'about']);
            })
            ->setApplicationMenu();
    }
    
    protected function registerShortcuts()
    {
        GlobalShortcut::register('CommandOrControl+N', function () {
            $this->newFile();
        });
        
        GlobalShortcut::register('CommandOrControl+O', function () {
            $this->openFile();
        });
        
        GlobalShortcut::register('CommandOrControl+Shift+O', function () {
            $this->openFolder();
        });
        
        GlobalShortcut::register('CommandOrControl+S', function () {
            $this->saveFile();
        });
        
        GlobalShortcut::register('CommandOrControl+Shift+S', function () {
            $this->saveFileAs();
        });
        
        GlobalShortcut::register('CommandOrControl+W', function () {
            $this->closeEditor();
        });
        
        GlobalShortcut::register('CommandOrControl+Q', function () {
            $this->quit();
        });
        
        GlobalShortcut::register('CommandOrControl+,', function () {
            $this->openSettings();
        });
    }
    
    public function newFile()
    {
        $window = Window::current();
        
        if ($window) {
            $window->webContents->executeJavaScript('newFile()');
        }
        
        return json(['success' => true]);
    }
    
    public function openFile()
    {
        $file = $this->editorService->openFile();
        
        if (!$file) {
            return json(['success' => false, 'message' => '未选择文件']);
        }
        
        $window = Window::current();
        
        if ($window) {
            $window->webContents->executeJavaScript('openFile(' . json_encode($file) . ')');
        }
        
        return json(['success' => true, 'file' => $file]);
    }
    
    public function openFolder()
    {
        $project = $this->projectService->openProject();
        
        if (!$project) {
            return json(['success' => false, 'message' => '未选择文件夹']);
        }
        
        $window = Window::current();
        
        if ($window) {
            $window->webContents->executeJavaScript('openProject(' . json_encode($project) . ')');
        }
        
        return json(['success' => true, 'project' => $project]);
    }
    
    public function saveFile()
    {
        $path = input('path');
        $content = input('content');
        
        if (empty($content)) {
            return json(['success' => false, 'message' => '内容不能为空']);
        }
        
        $result = $this->editorService->saveFile($path, $content);
        
        if (!$result) {
            return json(['success' => false, 'message' => '保存失败']);
        }
        
        return json(['success' => true, 'file' => $result]);
    }
    
    public function saveFileAs()
    {
        $content = input('content');
        
        if (empty($content)) {
            return json(['success' => false, 'message' => '内容不能为空']);
        }
        
        $result = $this->editorService->saveFile(null, $content);
        
        if (!$result) {
            return json(['success' => false, 'message' => '保存失败']);
        }
        
        $window = Window::current();
        
        if ($window) {
            $window->webContents->executeJavaScript('updateFilePath(' . json_encode($result) . ')');
        }
        
        return json(['success' => true, 'file' => $result]);
    }
    
    public function closeEditor()
    {
        $window = Window::current();
        
        if ($window) {
            $window->close();
        }
        
        return json(['success' => true]);
    }
    
    public function openSettings()
    {
        Window::open('/setting', [
            'title' => '设置',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function quit()
    {
        App::quit();
        
        return json(['success' => true]);
    }
    
    public function about()
    {
        Window::open('/about', [
            'title' => '关于',
            'width' => 400,
            'height' => 300,
        ]);
        
        return json(['success' => true]);
    }
}
