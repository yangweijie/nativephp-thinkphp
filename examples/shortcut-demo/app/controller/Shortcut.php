<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Shortcut as ShortcutFacade;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;
use think\facade\Config;

class Shortcut extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取桌面快捷方式状态
        $desktopShortcutExists = ShortcutFacade::existsOnDesktop();
        
        // 获取开始菜单快捷方式状态
        $startMenuShortcutExists = ShortcutFacade::existsInStartMenu();
        
        // 获取开机自启动设置
        $loginItemSettings = ShortcutFacade::getLoginItemSettings();
        
        // 获取桌面路径
        $desktopPath = ShortcutFacade::getDesktopPath();
        
        // 获取开始菜单路径
        $startMenuPath = ShortcutFacade::getStartMenuPath();
        
        // 获取应用程序路径
        $applicationPath = ShortcutFacade::getApplicationPath();
        
        // 获取应用程序名称
        $applicationName = ShortcutFacade::getApplicationName();
        
        return View::fetch('shortcut/index', [
            'desktopShortcutExists' => $desktopShortcutExists,
            'startMenuShortcutExists' => $startMenuShortcutExists,
            'loginItemSettings' => $loginItemSettings,
            'desktopPath' => $desktopPath,
            'startMenuPath' => $startMenuPath,
            'applicationPath' => $applicationPath,
            'applicationName' => $applicationName,
        ]);
    }
    
    /**
     * 创建桌面快捷方式
     *
     * @return \think\Response
     */
    public function createDesktopShortcut()
    {
        $options = [
            'arguments' => input('arguments', ''),
            'description' => input('description', ''),
            'icon' => input('icon', ''),
            'iconIndex' => input('icon_index/d', 0),
            'appUserModelId' => input('app_user_model_id', ''),
        ];
        
        // 过滤空选项
        $options = array_filter($options);
        
        // 创建桌面快捷方式
        $result = ShortcutFacade::createDesktopShortcut($options);
        
        if ($result) {
            return json(['success' => true, 'message' => '桌面快捷方式创建成功']);
        } else {
            return json(['success' => false, 'message' => '桌面快捷方式创建失败']);
        }
    }
    
    /**
     * 创建开始菜单快捷方式
     *
     * @return \think\Response
     */
    public function createStartMenuShortcut()
    {
        $options = [
            'arguments' => input('arguments', ''),
            'description' => input('description', ''),
            'icon' => input('icon', ''),
            'iconIndex' => input('icon_index/d', 0),
            'appUserModelId' => input('app_user_model_id', ''),
        ];
        
        // 过滤空选项
        $options = array_filter($options);
        
        // 创建开始菜单快捷方式
        $result = ShortcutFacade::createStartMenuShortcut($options);
        
        if ($result) {
            return json(['success' => true, 'message' => '开始菜单快捷方式创建成功']);
        } else {
            return json(['success' => false, 'message' => '开始菜单快捷方式创建失败']);
        }
    }
    
    /**
     * 创建自定义快捷方式
     *
     * @return \think\Response
     */
    public function createCustomShortcut()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '快捷方式路径不能为空']);
        }
        
        $options = [
            'arguments' => input('arguments', ''),
            'description' => input('description', ''),
            'icon' => input('icon', ''),
            'iconIndex' => input('icon_index/d', 0),
            'appUserModelId' => input('app_user_model_id', ''),
        ];
        
        // 过滤空选项
        $options = array_filter($options);
        
        // 创建自定义快捷方式
        $result = ShortcutFacade::createShortcut($path, $options);
        
        if ($result) {
            return json(['success' => true, 'message' => '自定义快捷方式创建成功']);
        } else {
            return json(['success' => false, 'message' => '自定义快捷方式创建失败']);
        }
    }
    
    /**
     * 删除桌面快捷方式
     *
     * @return \think\Response
     */
    public function removeDesktopShortcut()
    {
        // 删除桌面快捷方式
        $result = ShortcutFacade::removeFromDesktop();
        
        if ($result) {
            return json(['success' => true, 'message' => '桌面快捷方式删除成功']);
        } else {
            return json(['success' => false, 'message' => '桌面快捷方式删除失败']);
        }
    }
    
    /**
     * 删除开始菜单快捷方式
     *
     * @return \think\Response
     */
    public function removeStartMenuShortcut()
    {
        // 删除开始菜单快捷方式
        $result = ShortcutFacade::removeFromStartMenu();
        
        if ($result) {
            return json(['success' => true, 'message' => '开始菜单快捷方式删除成功']);
        } else {
            return json(['success' => false, 'message' => '开始菜单快捷方式删除失败']);
        }
    }
    
    /**
     * 删除自定义快捷方式
     *
     * @return \think\Response
     */
    public function removeCustomShortcut()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '快捷方式路径不能为空']);
        }
        
        // 删除自定义快捷方式
        $result = ShortcutFacade::remove($path);
        
        if ($result) {
            return json(['success' => true, 'message' => '自定义快捷方式删除成功']);
        } else {
            return json(['success' => false, 'message' => '自定义快捷方式删除失败']);
        }
    }
    
    /**
     * 设置开机自启动
     *
     * @return \think\Response
     */
    public function setLoginItemSettings()
    {
        $enabled = input('enabled/b', false);
        
        $options = [
            'openAtLogin' => $enabled,
            'openAsHidden' => input('open_as_hidden/b', false),
            'path' => input('path', ''),
            'args' => input('args/a', []),
        ];
        
        // 过滤空选项
        $options = array_filter($options);
        
        // 设置开机自启动
        $result = ShortcutFacade::setLoginItemSettings($enabled, $options);
        
        if ($result) {
            return json(['success' => true, 'message' => '开机自启动设置成功']);
        } else {
            return json(['success' => false, 'message' => '开机自启动设置失败']);
        }
    }
    
    /**
     * 获取开机自启动设置
     *
     * @return \think\Response
     */
    public function getLoginItemSettings()
    {
        $options = [
            'path' => input('path', ''),
        ];
        
        // 过滤空选项
        $options = array_filter($options);
        
        // 获取开机自启动设置
        $settings = ShortcutFacade::getLoginItemSettings($options);
        
        return json(['success' => true, 'settings' => $settings]);
    }
    
    /**
     * 检查快捷方式是否存在
     *
     * @return \think\Response
     */
    public function exists()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '快捷方式路径不能为空']);
        }
        
        // 检查快捷方式是否存在
        $exists = ShortcutFacade::exists($path);
        
        return json(['success' => true, 'exists' => $exists]);
    }
}
