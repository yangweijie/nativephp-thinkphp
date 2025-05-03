<?php

namespace native\thinkphp\facade;

use Native\Laravel\Windows\PendingOpenWindow;
use native\thinkphp\contract\WindowManager as WindowManagerContract;
use native\thinkphp\fakes\WindowManagerFake;
use think\Facade;

/**
 * @method static PendingOpenWindow open(string $id = 'main')
 * @method static void close($id = null)
 * @method static object current()
 * @method static array all()
 * @method static void resize($width, $height, $id = null)
 * @method static void position($x, $y, $animated = false, $id = null)
 * @method static void alwaysOnTop($alwaysOnTop = true, $id = null)
 * @method static void reload($id = null)
 * @method static void maximize($id = null)
 * @method static void minimize($id = null)
 */
class Window extends Facade
{
    public static function fake()
    {
        return tap(app()->make(WindowManagerFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    public static function swap($instance)
    {
        if (property_exists(static::class, 'instance')) {
            static::$instance = $instance;
        } else {
            app()->bind(static::getFacadeClass(), function () use ($instance) {
                return $instance;
            });
        }
    }

    protected static function getFacadeAccessor()
    {
        return WindowManagerContract::class;
    }
}
