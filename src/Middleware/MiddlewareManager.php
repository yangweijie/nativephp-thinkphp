<?php

namespace Native\ThinkPHP\Middleware;

use think\App;
use think\Request;
use think\Response;

class MiddlewareManager
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 全局中间件
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 添加中间件
     *
     * @param string|array $middleware
     * @return $this
     */
    public function add($middleware)
    {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * 执行中间件
     *
     * @param \think\Request $request
     * @param \Closure $handler
     * @return \think\Response
     */
    public function run(Request $request, \Closure $handler): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            function ($carry, $middleware) {
                return function ($request) use ($carry, $middleware) {
                    $middleware = $this->app->make($middleware);
                    return $middleware->handle($request, $carry);
                };
            },
            $handler
        );

        return $pipeline($request);
    }
}