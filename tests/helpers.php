<?php

/**
 * 获取环境变量值
 *
 * @param string $key 环境变量名
 * @param mixed $default 默认值
 * @return mixed
 */
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

/**
 * 获取配置值
 *
 * @param string $key 配置键名
 * @param mixed $default 默认值
 * @return mixed
 */
if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        static $config = [];

        if ($key === null) {
            return $config;
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        // 简单模拟配置，实际应用中应该从配置文件中读取
        $config['native.name'] = env('APP_NAME', 'NativePHP Test');
        $config['native.app_id'] = env('NATIVEPHP_APP_ID', 'com.nativephp.test');
        $config['native.version'] = env('NATIVEPHP_APP_VERSION', '1.0.0');

        // 支持点语法获取嵌套配置
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $configKey = $parts[0];
            $subKey = $parts[1];

            if (isset($config[$configKey . '.' . $subKey])) {
                return $config[$configKey . '.' . $subKey];
            }
        }

        return $default;
    }
}

/**
 * 获取运行时路径
 *
 * @param string $path 路径
 * @return string
 */
if (!function_exists('runtime_path')) {
    function runtime_path($path = '')
    {
        $runtimePath = dirname(__DIR__) . '/runtime/';
        if (!is_dir($runtimePath)) {
            mkdir($runtimePath, 0755, true);
        }
        return $runtimePath . ($path ? ltrim($path, '/\\') : '');
    }
}

/**
 * 获取公共路径
 *
 * @param string $path 路径
 * @return string
 */
if (!function_exists('public_path')) {
    function public_path($path = '')
    {
        $publicPath = dirname(__DIR__) . '/public/';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }
        return $publicPath . ($path ? ltrim($path, '/\\') : '');
    }
}

/**
 * 获取资源路径
 *
 * @param string $path 路径
 * @return string
 */
if (!function_exists('resource_path')) {
    function resource_path($path = '')
    {
        $resourcePath = dirname(__DIR__) . '/resources/';
        if (!is_dir($resourcePath)) {
            mkdir($resourcePath, 0755, true);
        }
        return $resourcePath . ($path ? ltrim($path, '/\\') : '');
    }
}
