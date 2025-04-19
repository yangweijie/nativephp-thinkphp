<?php

namespace Native\ThinkPHP\Utils;

use think\App as ThinkApp;
use Native\ThinkPHP\Facades\FileSystem;

class Config
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 配置文件路径
     *
     * @var string
     */
    protected $configFile;

    /**
     * 配置数据
     *
     * @var array
     */
    protected $config = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param string|null $configFile
     */
    public function __construct(ThinkApp $app, $configFile = null)
    {
        $this->app = $app;
        $this->configFile = $configFile ?: $this->getDefaultConfigFile();
        $this->load();
    }

    /**
     * 获取默认配置文件路径
     *
     * @return string
     */
    protected function getDefaultConfigFile()
    {
        return $this->app->getRuntimePath() . 'config/native_config.json';
    }

    /**
     * 设置配置文件路径
     *
     * @param string $configFile
     * @return $this
     */
    public function setConfigFile($configFile)
    {
        $this->configFile = $configFile;
        $this->load();
        
        return $this;
    }

    /**
     * 获取配置文件路径
     *
     * @return string
     */
    public function getConfigFile()
    {
        return $this->configFile;
    }

    /**
     * 加载配置
     *
     * @return void
     */
    protected function load()
    {
        if (FileSystem::exists($this->configFile)) {
            $content = FileSystem::read($this->configFile);
            $this->config = json_decode($content, true) ?: [];
        } else {
            $this->config = [];
            $this->save();
        }
    }

    /**
     * 保存配置
     *
     * @return bool
     */
    protected function save()
    {
        // 确保目录存在
        $dir = dirname($this->configFile);
        if (!is_dir($dir)) {
            FileSystem::makeDirectory($dir, 0755, true);
        }
        
        return FileSystem::write($this->configFile, json_encode($this->config, JSON_PRETTY_PRINT));
    }

    /**
     * 获取配置值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            
            $value = $value[$segment];
        }
        
        return $value;
    }

    /**
     * 设置配置值
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment]) || !is_array($config[$segment])) {
                    $config[$segment] = [];
                }
                
                $config = &$config[$segment];
            }
        }
        
        return $this->save();
    }

    /**
     * 检查配置是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $keys = explode('.', $key);
        $config = $this->config;
        
        foreach ($keys as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return false;
            }
            
            $config = $config[$segment];
        }
        
        return true;
    }

    /**
     * 删除配置
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                unset($config[$segment]);
            } else {
                if (!isset($config[$segment]) || !is_array($config[$segment])) {
                    return false;
                }
                
                $config = &$config[$segment];
            }
        }
        
        return $this->save();
    }

    /**
     * 获取所有配置
     *
     * @return array
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * 清空所有配置
     *
     * @return bool
     */
    public function clear()
    {
        $this->config = [];
        return $this->save();
    }

    /**
     * 导出配置
     *
     * @param string|null $path
     * @return bool
     */
    public function export($path = null)
    {
        if (!$path) {
            $path = $this->configFile . '.backup-' . date('YmdHis');
        }
        
        // 确保目录存在
        $dir = dirname($path);
        if (!is_dir($dir)) {
            FileSystem::makeDirectory($dir, 0755, true);
        }
        
        return FileSystem::write($path, json_encode($this->config, JSON_PRETTY_PRINT));
    }

    /**
     * 导入配置
     *
     * @param string $path
     * @return bool
     */
    public function import($path)
    {
        if (!FileSystem::exists($path)) {
            return false;
        }
        
        $content = FileSystem::read($path);
        $config = json_decode($content, true);
        
        if ($config === null) {
            return false;
        }
        
        $this->config = $config;
        return $this->save();
    }

    /**
     * 合并配置
     *
     * @param array $config
     * @return bool
     */
    public function merge(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
        return $this->save();
    }

    /**
     * 替换配置
     *
     * @param array $config
     * @return bool
     */
    public function replace(array $config)
    {
        $this->config = array_replace_recursive($this->config, $config);
        return $this->save();
    }
}
