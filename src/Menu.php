<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Menu
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 菜单项
     *
     * @var array
     */
    protected $items = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 创建一个新菜单
     *
     * @return $this
     */
    public function create()
    {
        $this->items = [];

        return $this;
    }

    /**
     * 添加菜单项
     *
     * @param string $label 菜单项标签
     * @param callable|array|null $action 点击时的回调函数或配置数组
     * @param array $options 选项
     * @return $this
     */
    public function add($label, $action = null, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'action' => $action,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加复选框菜单项
     *
     * @param string $label 菜单项标签
     * @param bool $checked 是否选中
     * @param callable|null $action 点击时的回调函数
     * @param array $options 选项
     * @return $this
     */
    public function checkbox($label, $checked = false, $action = null, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'type' => 'checkbox',
            'checked' => $checked,
            'action' => $action,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加单选框菜单项
     *
     * @param string $label 菜单项标签
     * @param bool $checked 是否选中
     * @param callable|null $action 点击时的回调函数
     * @param array $options 选项
     * @return $this
     */
    public function radio($label, $checked = false, $action = null, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'type' => 'radio',
            'checked' => $checked,
            'action' => $action,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加分隔线
     *
     * @return $this
     */
    public function separator()
    {
        $this->items[] = [
            'type' => 'separator',
        ];

        return $this;
    }

    /**
     * 添加子菜单
     *
     * @param string $label
     * @param callable $callback
     * @return $this
     */
    public function submenu($label, callable $callback)
    {
        $submenu = new self($this->app);
        $callback($submenu);

        $this->items[] = [
            'label' => $label,
            'submenu' => $submenu->items,
        ];

        return $this;
    }

    /**
     * 添加快捷键菜单项
     *
     * @param string $label 菜单项标签
     * @param string $accelerator 快捷键组合，如 'CommandOrControl+Z'
     * @param callable|null $action 点击时的回调函数
     * @param array $options 选项
     * @return $this
     */
    public function accelerator($label, $accelerator, $action = null, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'accelerator' => $accelerator,
            'action' => $action,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加图标菜单项
     *
     * @param string $label 菜单项标签
     * @param string $icon 图标路径或URL
     * @param callable|null $action 点击时的回调函数
     * @param array $options 选项
     * @return $this
     */
    public function icon($label, $icon, $action = null, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'icon' => $icon,
            'action' => $action,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加禁用的菜单项
     *
     * @param string $label 菜单项标签
     * @param array $options 选项
     * @return $this
     */
    public function disabled($label, array $options = [])
    {
        $item = array_merge([
            'label' => $label,
            'enabled' => false,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 添加角色菜单项（macOS特有）
     *
     * @param string $role macOS 角色，如 'about', 'hide', 'unhide', 'front', 'window', 'help', 'services'
     * @param array $options 选项
     * @return $this
     */
    public function role($role, array $options = [])
    {
        $item = array_merge([
            'role' => $role,
        ], $options);

        $this->items[] = $item;

        return $this;
    }

    /**
     * 设置应用菜单
     *
     * @return bool
     */
    public function setApplicationMenu()
    {
        $response = $this->client->post('menu/application', [
            'menu' => $this->items,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置上下文菜单
     *
     * @return bool
     */
    public function setContextMenu()
    {
        $response = $this->client->post('menu/context', [
            'menu' => $this->items,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 弹出上下文菜单
     *
     * @param int|null $x X 坐标，如果为 null，则使用当前鼠标位置
     * @param int|null $y Y 坐标，如果为 null，则使用当前鼠标位置
     * @return bool
     */
    public function popup($x = null, $y = null)
    {
        $response = $this->client->post('menu/popup', [
            'menu' => $this->items,
            'x' => $x,
            'y' => $y,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 清除应用菜单
     *
     * @return bool
     */
    public function clearApplicationMenu()
    {
        $response = $this->client->post('menu/clear-application');

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 清除上下文菜单
     *
     * @return bool
     */
    public function clearContextMenu()
    {
        $response = $this->client->post('menu/clear-context');

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 获取菜单项
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
