<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// 使用默认的 PHPUnit\Framework\TestCase

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toImplementInterface', function (string $interface) {
    return $this->and($this->value instanceof $interface);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * 创建模拟的 App 实例
 */
function mockApp() {
    $app = Mockery::mock(\think\App::class);
    $config = Mockery::mock(\think\Config::class);

    // 模拟 config 方法
    $config->shouldReceive('get')->withAnyArgs()->andReturnUsing(function($key, $default = []) {
        if ($key === 'native') {
            return [
                'app' => [
                    'name' => 'Test App',
                    'version' => '1.0.0',
                ],
                'window' => [
                    'default' => [
                        'width' => 800,
                        'height' => 600,
                        'center' => true,
                    ],
                ],
            ];
        }
        return $default;
    });

    // 设置 config 属性
    $app->config = $config;

    // 设置 bind 方法的期望
    $app->shouldReceive('bind')->withAnyArgs()->andReturnSelf();
    $app->shouldReceive('instance')->withAnyArgs()->andReturnSelf();
    $app->shouldReceive('has')->withAnyArgs()->andReturn(false);
    $app->shouldReceive('__set')->withAnyArgs()->andReturnNull();

    // 模拟 Container 的其他方法
    $app->shouldReceive('offsetExists')->withAnyArgs()->andReturn(false);
    $app->shouldReceive('offsetGet')->withAnyArgs()->andReturnNull();
    $app->shouldReceive('offsetSet')->withAnyArgs()->andReturnNull();
    $app->shouldReceive('offsetUnset')->withAnyArgs()->andReturnNull();

    return $app;
}

/**
 * 创建模拟的 Native 实例
 */
function mockNative() {
    $native = Mockery::mock(\NativePHP\Think\Native::class);

    // 确保 getConfig 返回数组而不是 null
    $native->shouldReceive('getConfig')->withAnyArgs()->andReturnUsing(function($key, $default = []) {
        if ($key === 'window.default') {
            return [
                'width' => 800,
                'height' => 600,
                'center' => true,
            ];
        }
        return $default;
    });

    // 创建一个模拟的 EventDispatcher 实例
    $eventDispatcher = Mockery::mock(\NativePHP\Think\EventDispatcher::class);
    $eventDispatcher->shouldReceive('dispatch')->withAnyArgs()->andReturnNull();
    $eventDispatcher->shouldReceive('listen')->withAnyArgs()->andReturnSelf();

    // 确保 events() 返回 EventDispatcher 实例
    $native->shouldReceive('events')->andReturn($eventDispatcher);
    $native->shouldReceive('listen')->withAnyArgs()->andReturnSelf();

    return $native;
}
