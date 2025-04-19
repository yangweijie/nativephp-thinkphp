<?php

require_once __DIR__ . '/../vendor/autoload.php';

// 定义全局 config 函数用于测试
if (!function_exists('config')) {
    function config($key = null, $default = null) {
        return $default;
    }
}

// 定义全局 app 函数用于测试
if (!function_exists('app')) {
    function app($abstract = null) {
        static $app = null;
        
        if ($app === null) {
            $app = new class {
                public function make($abstract) {
                    return new $abstract();
                }
                
                public function get($abstract) {
                    return $this->make($abstract);
                }
                
                public function call($callback, array $parameters = []) {
                    return call_user_func_array($callback, $parameters);
                }
            };
        }
        
        if ($abstract !== null) {
            return $app->make($abstract);
        }
        
        return $app;
    }
}
