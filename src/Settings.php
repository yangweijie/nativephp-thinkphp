<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;

class Settings
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 设置数据
     *
     * @var array
     */
    protected $settings = [];

    /**
     * 设置文件路径
     *
     * @var string|null
     */
    protected $settingsPath = null;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->settingsPath = $this->getDefaultSettingsPath();
        $this->load();
    }

    /**
     * 获取默认设置文件路径
     *
     * @return string
     */
    protected function getDefaultSettingsPath()
    {
        $appDataPath = $this->app->getRuntimePath() . 'settings/';
        
        if (!is_dir($appDataPath)) {
            mkdir($appDataPath, 0755, true);
        }
        
        return $appDataPath . 'settings.json';
    }

    /**
     * 设置设置文件路径
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->settingsPath = $path;
        $this->load();
        
        return $this;
    }

    /**
     * 获取设置文件路径
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->settingsPath;
    }

    /**
     * 加载设置
     *
     * @return void
     */
    protected function load()
    {
        if (file_exists($this->settingsPath)) {
            $content = file_get_contents($this->settingsPath);
            $this->settings = json_decode($content, true) ?: [];
        } else {
            $this->settings = [];
        }
    }

    /**
     * 保存设置
     *
     * @return bool
     */
    protected function save()
    {
        // 确保目录存在
        $dir = dirname($this->settingsPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($this->settingsPath, json_encode($this->settings, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * 获取设置值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->settings;
        
        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            
            $value = $value[$segment];
        }
        
        return $value;
    }

    /**
     * 设置设置值
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $settings = &$this->settings;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $settings[$segment] = $value;
            } else {
                if (!isset($settings[$segment]) || !is_array($settings[$segment])) {
                    $settings[$segment] = [];
                }
                
                $settings = &$settings[$segment];
            }
        }
        
        return $this->save();
    }

    /**
     * 检查设置是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $keys = explode('.', $key);
        $settings = $this->settings;
        
        foreach ($keys as $segment) {
            if (!is_array($settings) || !array_key_exists($segment, $settings)) {
                return false;
            }
            
            $settings = $settings[$segment];
        }
        
        return true;
    }

    /**
     * 删除设置
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $keys = explode('.', $key);
        $settings = &$this->settings;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                unset($settings[$segment]);
            } else {
                if (!isset($settings[$segment]) || !is_array($settings[$segment])) {
                    return false;
                }
                
                $settings = &$settings[$segment];
            }
        }
        
        return $this->save();
    }

    /**
     * 获取所有设置
     *
     * @return array
     */
    public function all()
    {
        return $this->settings;
    }

    /**
     * 清空所有设置
     *
     * @return bool
     */
    public function clear()
    {
        $this->settings = [];
        return $this->save();
    }

    /**
     * 导出设置
     *
     * @param string|null $path
     * @return bool
     */
    public function export($path = null)
    {
        if (!$path) {
            $path = $this->settingsPath . '.backup-' . date('YmdHis');
        }
        
        // 确保目录存在
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($path, json_encode($this->settings, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * 导入设置
     *
     * @param string $path
     * @return bool
     */
    public function import($path)
    {
        if (!file_exists($path)) {
            return false;
        }
        
        $content = file_get_contents($path);
        $settings = json_decode($content, true);
        
        if ($settings === null) {
            return false;
        }
        
        $this->settings = $settings;
        return $this->save();
    }

    /**
     * 监听设置变化
     *
     * @param string $key
     * @param callable $callback
     * @return void
     */
    public function watch($key, callable $callback)
    {
        // 这里将实现监听设置变化的逻辑
        // 在实际实现中，需要调用 Electron 的 API
    }
}
