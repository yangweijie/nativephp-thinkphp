<?php

namespace Native\ThinkPHP;

use think\App;

class Shortcut
{
    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;
    
    /**
     * NativePHP 客户端
     *
     * @var \Native\ThinkPHP\Client
     */
    protected $client;
    
    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->client = $app->make('native.client');
    }
    
    /**
     * 创建桌面快捷方式
     *
     * @param array $options 选项
     * @return bool
     */
    public function createDesktopShortcut(array $options = [])
    {
        $response = $this->client->post('shortcut/create-desktop', $options);
        
        return (bool) $response->json('success');
    }
    
    /**
     * 创建开始菜单快捷方式
     *
     * @param array $options 选项
     * @return bool
     */
    public function createStartMenuShortcut(array $options = [])
    {
        $response = $this->client->post('shortcut/create-start-menu', $options);
        
        return (bool) $response->json('success');
    }
    
    /**
     * 创建应用程序快捷方式
     *
     * @param string $path 快捷方式路径
     * @param array $options 选项
     * @return bool
     */
    public function createShortcut($path, array $options = [])
    {
        $response = $this->client->post('shortcut/create', array_merge([
            'path' => $path,
        ], $options));
        
        return (bool) $response->json('success');
    }
    
    /**
     * 检查桌面快捷方式是否存在
     *
     * @return bool
     */
    public function existsOnDesktop()
    {
        $response = $this->client->get('shortcut/exists-on-desktop');
        
        return (bool) $response->json('exists');
    }
    
    /**
     * 检查开始菜单快捷方式是否存在
     *
     * @return bool
     */
    public function existsInStartMenu()
    {
        $response = $this->client->get('shortcut/exists-in-start-menu');
        
        return (bool) $response->json('exists');
    }
    
    /**
     * 检查快捷方式是否存在
     *
     * @param string $path 快捷方式路径
     * @return bool
     */
    public function exists($path)
    {
        $response = $this->client->post('shortcut/exists', [
            'path' => $path,
        ]);
        
        return (bool) $response->json('exists');
    }
    
    /**
     * 删除桌面快捷方式
     *
     * @return bool
     */
    public function removeFromDesktop()
    {
        $response = $this->client->post('shortcut/remove-from-desktop');
        
        return (bool) $response->json('success');
    }
    
    /**
     * 删除开始菜单快捷方式
     *
     * @return bool
     */
    public function removeFromStartMenu()
    {
        $response = $this->client->post('shortcut/remove-from-start-menu');
        
        return (bool) $response->json('success');
    }
    
    /**
     * 删除快捷方式
     *
     * @param string $path 快捷方式路径
     * @return bool
     */
    public function remove($path)
    {
        $response = $this->client->post('shortcut/remove', [
            'path' => $path,
        ]);
        
        return (bool) $response->json('success');
    }
    
    /**
     * 设置开机自启动
     *
     * @param bool $enabled 是否启用
     * @param array $options 选项
     * @return bool
     */
    public function setLoginItemSettings($enabled = true, array $options = [])
    {
        $response = $this->client->post('shortcut/set-login-item-settings', array_merge([
            'enabled' => $enabled,
        ], $options));
        
        return (bool) $response->json('success');
    }
    
    /**
     * 获取开机自启动设置
     *
     * @param array $options 选项
     * @return array
     */
    public function getLoginItemSettings(array $options = [])
    {
        $response = $this->client->post('shortcut/get-login-item-settings', $options);
        
        return $response->json('settings') ?? [];
    }
    
    /**
     * 获取桌面路径
     *
     * @return string
     */
    public function getDesktopPath()
    {
        $response = $this->client->get('shortcut/get-desktop-path');
        
        return $response->json('path');
    }
    
    /**
     * 获取开始菜单路径
     *
     * @return string
     */
    public function getStartMenuPath()
    {
        $response = $this->client->get('shortcut/get-start-menu-path');
        
        return $response->json('path');
    }
    
    /**
     * 获取应用程序路径
     *
     * @return string
     */
    public function getApplicationPath()
    {
        $response = $this->client->get('shortcut/get-application-path');
        
        return $response->json('path');
    }
    
    /**
     * 获取应用程序名称
     *
     * @return string
     */
    public function getApplicationName()
    {
        $response = $this->client->get('shortcut/get-application-name');
        
        return $response->json('name');
    }
}
