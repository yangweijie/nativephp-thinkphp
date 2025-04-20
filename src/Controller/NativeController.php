<?php

namespace NativePHP\Think\Controller;

use think\facade\Config;
use think\Response;

class NativeController
{
    /**
     * 获取 NativePHP 配置
     */
    public function config(): Response
    {
        $config = Config::get('native');
        
        // 处理路径
        if (isset($config['app']['icon'])) {
            $config['app']['icon'] = $this->normalizePath($config['app']['icon']);
        }
        
        if (isset($config['tray']['icon'])) {
            $config['tray']['icon'] = $this->normalizePath($config['tray']['icon']);
        }
        
        return Response::create($config, 'json');
    }
    
    /**
     * 规范化路径
     */
    protected function normalizePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        // 如果是相对路径，转换为绝对路径
        if (!preg_match('/^(https?:\/\/|\/)/i', $path)) {
            $path = public_path($path);
        }
        
        return $path;
    }
}
