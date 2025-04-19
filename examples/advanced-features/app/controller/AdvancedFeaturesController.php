<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\DTOs\WindowConfig;
use Native\ThinkPHP\DTOs\MenuConfig;
use Native\ThinkPHP\DTOs\DialogConfig;
use Native\ThinkPHP\Events\WindowEvent;
use Native\ThinkPHP\Events\MenuEvent;

class AdvancedFeaturesController extends BaseController
{
    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('advanced-features/index');
    }
    
    /**
     * 创建透明窗口
     *
     * @return \think\Response
     */
    public function createTransparentWindow()
    {
        // 创建窗口配置
        $config = new WindowConfig();
        $config->title = '透明窗口';
        $config->width = 800;
        $config->height = 600;
        $config->transparent = true;
        $config->frame = false;
        
        // 打开窗口
        $windowId = Window::open(url('advanced-features/transparent'), $config->toArray());
        
        return json(['success' => true, 'windowId' => $windowId]);
    }
    
    /**
     * 显示透明窗口内容
     *
     * @return \think\Response
     */
    public function transparent()
    {
        return view('advanced-features/transparent');
    }
    
    /**
     * 创建模态窗口
     *
     * @return \think\Response
     */
    public function createModalWindow()
    {
        // 创建窗口配置
        $config = new WindowConfig();
        $config->title = '模态窗口';
        $config->width = 500;
        $config->height = 300;
        $config->modal = true;
        $config->resizable = false;
        $config->parent = request()->param('parentId');
        
        // 打开窗口
        $windowId = Window::open(url('advanced-features/modal'), $config->toArray());
        
        return json(['success' => true, 'windowId' => $windowId]);
    }
    
    /**
     * 显示模态窗口内容
     *
     * @return \think\Response
     */
    public function modal()
    {
        return view('advanced-features/modal');
    }
    
    /**
     * 创建应用菜单
     *
     * @return \think\Response
     */
    public function createApplicationMenu()
    {
        // 创建菜单
        $menu = Menu::create();
        
        // 文件菜单
        $menu->submenu('文件', function ($submenu) {
            $submenu->add('新建', function () {
                Dialog::info('新建文件');
            });
            $submenu->add('打开', function () {
                $file = Dialog::openFile();
                if ($file) {
                    Dialog::info('打开文件: ' . $file);
                }
            });
            $submenu->separator();
            $submenu->add('保存', function () {
                Dialog::info('保存文件');
            });
            $submenu->add('另存为', function () {
                $file = Dialog::saveFile();
                if ($file) {
                    Dialog::info('另存为: ' . $file);
                }
            });
            $submenu->separator();
            $submenu->add('退出', function () {
                Window::closeAll();
            });
        });
        
        // 编辑菜单
        $menu->submenu('编辑', function ($submenu) {
            $submenu->add('撤销', function () {
                Dialog::info('撤销');
            });
            $submenu->add('重做', function () {
                Dialog::info('重做');
            });
            $submenu->separator();
            $submenu->add('剪切', function () {
                Dialog::info('剪切');
            });
            $submenu->add('复制', function () {
                Dialog::info('复制');
            });
            $submenu->add('粘贴', function () {
                Dialog::info('粘贴');
            });
        });
        
        // 视图菜单
        $menu->submenu('视图', function ($submenu) {
            $submenu->checkbox('工具栏', true, function ($checked) {
                Dialog::info('工具栏: ' . ($checked ? '显示' : '隐藏'));
            });
            $submenu->checkbox('状态栏', true, function ($checked) {
                Dialog::info('状态栏: ' . ($checked ? '显示' : '隐藏'));
            });
            $submenu->separator();
            $submenu->add('全屏', function () {
                Window::setFullscreen(true);
            });
        });
        
        // 帮助菜单
        $menu->submenu('帮助', function ($submenu) {
            $submenu->add('关于', function () {
                Dialog::info('NativePHP for ThinkPHP 示例应用');
            });
        });
        
        // 设置应用菜单
        $menu->setApplicationMenu();
        
        return json(['success' => true]);
    }
    
    /**
     * 创建上下文菜单
     *
     * @return \think\Response
     */
    public function createContextMenu()
    {
        // 创建菜单
        $menu = Menu::create();
        
        // 添加菜单项
        $menu->add('刷新', function () {
            Window::reload();
        });
        $menu->separator();
        $menu->add('复制', function () {
            Dialog::info('复制');
        });
        $menu->add('粘贴', function () {
            Dialog::info('粘贴');
        });
        $menu->separator();
        $menu->add('属性', function () {
            Dialog::info('属性');
        });
        
        // 设置上下文菜单
        $menu->setContextMenu();
        
        return json(['success' => true]);
    }
    
    /**
     * 上传文件
     *
     * @return \think\Response
     */
    public function uploadFile()
    {
        // 获取上传文件
        $file = request()->file('file');
        
        if (!$file) {
            return json(['success' => false, 'message' => '没有上传文件']);
        }
        
        // 保存文件
        $savePath = runtime_path() . 'uploads/';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }
        
        $info = $file->move($savePath);
        
        if ($info) {
            $filePath = $savePath . $info->getSaveName();
            return json(['success' => true, 'message' => '上传成功', 'filePath' => $filePath]);
        } else {
            return json(['success' => false, 'message' => $file->getError()]);
        }
    }
    
    /**
     * 下载文件
     *
     * @return \think\Response
     */
    public function downloadFile()
    {
        $url = request()->param('url');
        $savePath = request()->param('savePath');
        
        if (empty($url)) {
            return json(['success' => false, 'message' => 'URL 不能为空']);
        }
        
        if (empty($savePath)) {
            // 使用对话框选择保存路径
            $savePath = Dialog::saveFile([
                'title' => '保存文件',
                'defaultPath' => 'downloaded_file.txt',
            ]);
            
            if (!$savePath) {
                return json(['success' => false, 'message' => '未选择保存路径']);
            }
        }
        
        // 创建客户端
        $client = new \Native\ThinkPHP\Client\Client();
        
        try {
            // 下载文件
            $result = $client->download($url, $savePath);
            
            if ($result) {
                return json(['success' => true, 'message' => '下载成功', 'savePath' => $savePath]);
            } else {
                return json(['success' => false, 'message' => '下载失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '下载失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 显示对话框
     *
     * @return \think\Response
     */
    public function showDialog()
    {
        $type = request()->param('type');
        
        switch ($type) {
            case 'info':
                $result = Dialog::info('这是一条信息');
                break;
            case 'error':
                $result = Dialog::error('这是一条错误信息');
                break;
            case 'warning':
                $result = Dialog::warning('这是一条警告信息');
                break;
            case 'question':
                $result = Dialog::question('这是一个问题?');
                break;
            case 'confirm':
                $result = Dialog::confirm('确认执行此操作?');
                break;
            case 'prompt':
                $result = Dialog::prompt('请输入内容:');
                break;
            case 'openFile':
                $result = Dialog::openFile();
                break;
            case 'saveFile':
                $result = Dialog::saveFile();
                break;
            case 'selectFolder':
                $result = Dialog::selectFolder();
                break;
            case 'color':
                $result = Dialog::color();
                break;
            case 'font':
                $result = Dialog::font();
                break;
            case 'custom':
                // 创建自定义对话框配置
                $config = new DialogConfig();
                $config->title = '自定义对话框';
                $config->message = '这是一个使用 DialogConfig 创建的自定义对话框';
                $config->type = 'info';
                $config->buttons = ['确定', '取消', '更多选项'];
                $config->defaultId = 0;
                $config->cancelId = 1;
                
                $result = Dialog::showWithConfig($config);
                break;
            default:
                return json(['success' => false, 'message' => '未知对话框类型']);
        }
        
        return json(['success' => true, 'result' => $result]);
    }
    
    /**
     * 触发事件
     *
     * @return \think\Response
     */
    public function triggerEvent()
    {
        $type = request()->param('type');
        $data = request()->param('data', []);
        
        switch ($type) {
            case 'window':
                $windowId = request()->param('windowId');
                $eventType = request()->param('eventType');
                
                // 创建窗口事件
                $event = new WindowEvent($windowId, $eventType, $data);
                
                // 触发事件
                app('native.event')->emitEvent($event);
                break;
            case 'menu':
                $menuId = request()->param('menuId');
                $eventType = request()->param('eventType');
                
                // 创建菜单事件
                $event = new MenuEvent($menuId, $eventType, $data);
                
                // 触发事件
                app('native.event')->emitEvent($event);
                break;
            default:
                return json(['success' => false, 'message' => '未知事件类型']);
        }
        
        return json(['success' => true]);
    }
}
