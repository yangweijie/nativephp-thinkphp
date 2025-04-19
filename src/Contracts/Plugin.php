<?php

namespace Native\ThinkPHP\Contracts;

use think\App;

/**
 * 插件接口
 */
interface Plugin
{
    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app);

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void;

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void;

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array;
}
