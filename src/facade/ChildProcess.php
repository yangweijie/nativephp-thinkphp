<?php

namespace native\thinkphp\facade;


use native\thinkphp\contract\ChildProcess as ChildProcessContract;
use native\thinkphp\fakes\ChildProcessFake;
use think\Facade;

/**
 * @method static \native\thinkphp\ChildProcess[] all()
 * @method static \native\thinkphp\ChildProcess get(string $alias = null)
 * @method static \native\thinkphp\ChildProcess message(string $message, string $alias = null)
 * @method static \native\thinkphp\ChildProcess restart(string $alias = null)
 * @method static \native\thinkphp\ChildProcess start(string|array $cmd, string $alias, string $cwd = null, array $env = null, bool $persistent = false)
 * @method static \native\thinkphp\ChildProcess php(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static \native\thinkphp\ChildProcess artisan(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static void stop(string $alias = null)
 */
class ChildProcess extends Facade
{
    public static function fake()
    {
        return tap(app()->make(ChildProcessFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return ChildProcessContract::class;
    }
}
