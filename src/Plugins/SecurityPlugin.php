<?php

namespace Native\ThinkPHP\Plugins;

use Native\ThinkPHP\Facades\Settings;
use think\App;
use Native\ThinkPHP\Plugins\Plugin;

class SecurityPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'security';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '安全插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'window.create' => [$this, 'onWindowCreate'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 生成安全令牌
        $this->generateSecurityToken();

        // 设置安全策略
        $this->setSecurityPolicy();
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 检查安全令牌
        $this->checkSecurityToken();
    }

    /**
     * 窗口创建事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowCreate(array $window): void
    {
        // 设置窗口安全策略
        $this->setWindowSecurityPolicy($window);
    }

    /**
     * 生成安全令牌
     *
     * @return void
     */
    protected function generateSecurityToken(): void
    {
        // 检查是否已存在安全令牌
        $token = Settings::get('security_token');
        if (!$token) {
            // 生成新的安全令牌
            $token = bin2hex(random_bytes(32));
            Settings::set('security_token', $token);
        }

        // 设置安全令牌
        app('native.app')->setSecurityToken($token);
    }

    /**
     * 检查安全令牌
     *
     * @return void
     */
    protected function checkSecurityToken(): void
    {
        // 获取安全令牌
        $token = app('native.app')->getSecurityToken();
        $storedToken = Settings::get('security_token');

        // 检查安全令牌是否匹配
        if ($token !== $storedToken) {
            // 记录安全警告
            \Native\ThinkPHP\Facades\Logger::warning('Security token mismatch');

            // 重新生成安全令牌
            $this->generateSecurityToken();
        }
    }

    /**
     * 设置安全策略
     *
     * @return void
     */
    protected function setSecurityPolicy(): void
    {
        // 设置安全策略
        $policy = [
            'allowRunningInsecureContent' => false,
            'allowPopups' => false,
            'sandbox' => true,
            'webSecurity' => true,
            'contextIsolation' => true,
            'nodeIntegration' => false,
            'enableRemoteModule' => false,
        ];

        // 保存安全策略
        Settings::set('security_policy', $policy);
    }

    /**
     * 设置窗口安全策略
     *
     * @param array $window
     * @return void
     */
    protected function setWindowSecurityPolicy(array $window): void
    {
        // 获取安全策略
        $policy = Settings::get('security_policy', []);

        // 设置窗口安全策略
        /** @phpstan-ignore-next-line */
        \Native\ThinkPHP\Facades\Window::setWebPreferences($window['id'], $policy);
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 清除安全令牌
        Settings::set('security_token', null);

        // 清除安全策略
        Settings::set('security_policy', null);
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}