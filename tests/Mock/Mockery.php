<?php

/**
 * 简单的 Mockery 模拟类，用于测试
 */
class Mockery
{
    /**
     * 创建模拟对象
     *
     * @param string|null $class 类名
     * @return object
     */
    public static function mock($class = null)
    {
        return new MockObject($class);
    }

    /**
     * 关闭所有模拟对象
     *
     * @return void
     */
    public static function close()
    {
        // 空实现
    }
}

/**
 * 模拟对象类
 */
class MockObject
{
    /**
     * 类名
     *
     * @var string
     */
    protected $class;

    /**
     * 方法调用记录
     *
     * @var array
     */
    protected $calls = [];

    /**
     * 方法返回值
     *
     * @var array
     */
    protected $returns = [];

    /**
     * 当前方法
     *
     * @var string|null
     */
    protected $currentMethod = null;

    /**
     * 构造函数
     *
     * @param string|null $class 类名
     */
    public function __construct($class = null)
    {
        $this->class = $class;
    }

    /**
     * 设置方法应该接收的参数
     *
     * @param mixed ...$args 参数
     * @return $this
     */
    public function with(...$args)
    {
        return $this;
    }

    /**
     * 设置方法应该返回的值
     *
     * @param mixed $value 返回值
     * @return $this
     */
    public function andReturn($value)
    {
        $this->returns[$this->currentMethod] = $value;
        return $this;
    }

    /**
     * 设置参数匹配条件
     *
     * @param callable $callback 回调函数
     * @return mixed
     */
    public static function on($callback)
    {
        // 在测试环境中直接返回任意参数
        return true;
    }

    /**
     * 设置方法应该抛出的异常
     *
     * @param \Exception $exception 异常
     * @return $this
     */
    public function andThrow(\Exception $exception)
    {
        return $this;
    }

    /**
     * 设置方法应该执行的回调
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function andReturnUsing(callable $callback)
    {
        return $this;
    }

    /**
     * 记录方法调用
     *
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method, $args)
    {
        $this->currentMethod = $method;
        $this->calls[$method] = $args;

        if (isset($this->returns[$method])) {
            return $this->returns[$method];
        }

        // 特殊处理 json 方法
        if ($method === 'json') {
            // 模拟返回数组
            if ($args[0] === 'process') {
                return [
                    'alias' => 'test-process',
                    'cmd' => 'echo Hello',
                    'pid' => 1234,
                    'status' => 'running',
                ];
            } elseif ($args[0] === 'processes') {
                return [
                    'test-process-1' => [
                        'alias' => 'test-process-1',
                        'cmd' => 'echo Hello 1',
                        'pid' => 1234,
                        'status' => 'running',
                    ],
                    'test-process-2' => [
                        'alias' => 'test-process-2',
                        'cmd' => 'echo Hello 2',
                        'pid' => 5678,
                        'status' => 'stopped',
                    ],
                ];
            } elseif ($args[0] === 'success') {
                return true;
            } elseif ($args[0] === 'result') {
                return 'success';
            }
            return [];
        }

        return $this;
    }

    /**
     * 获取属性
     *
     * @param string $name 属性名
     * @return mixed
     */
    public function __get($name)
    {
        return $this;
    }
}
