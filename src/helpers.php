<?php

/**
 * 获取应用实例
 *
 * @param string|null $name 应用名称
 * @return \think\App|mixed
 */
function app($name = null)
{
    // 在测试环境中返回模拟对象
    if (defined('PHPUNIT_RUNNING')) {
        static $app = null;
        if ($app === null) {
            $app = new class {
                public $config;

                public function __construct()
                {
                    $this->config = new class {
                        private $config = [];

                        public function get($key = null, $default = null)
                        {
                            if ($key === null) {
                                return $this->config;
                            }

                            return $this->config[$key] ?? $default;
                        }

                        public function set($key, $value = null)
                        {
                            if (is_array($key)) {
                                $this->config = array_merge($this->config, $key);
                            } else {
                                $this->config[$key] = $value;
                            }

                            return $this;
                        }
                    };
                }

                public function getRootPath()
                {
                    return dirname(__DIR__, 2);
                }

                public function make($name)
                {
                    return $this;
                }
            };
        }

        return $app;
    }

    try {
        if (is_null($name)) {
            return \think\Container::getInstance()->make('app');
        }

        return \think\Container::getInstance()->make($name);
    } catch (\Exception $e) {
        // 如果出错，返回空对象
        return new \stdClass();
    }
}

/**
 * 获取配置值
 *
 * @param string $key 配置键名
 * @param mixed $default 默认值
 * @return mixed
 */
function config($key = null, $default = null)
{
    static $config = [];

    if ($key === null) {
        return $config;
    }

    if (isset($config[$key])) {
        return $config[$key];
    }

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
