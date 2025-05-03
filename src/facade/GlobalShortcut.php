<?php

namespace native\thinkphp\facade;


use native\thinkphp\contract\GlobalShortcut as GlobalShortcutContract;
use native\thinkphp\fakes\GlobalShortcutFake;
use think\Facade;

/**
 * @method static \native\thinkphp\GlobalShortcut key(string $key)
 * @method static \native\thinkphp\GlobalShortcut event(string $event)
 * @method static void register()
 * @method static void unregister()
 */
class GlobalShortcut extends Facade
{
    public static function fake()
    {
        return tap(app()->make(GlobalShortcutFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return GlobalShortcutContract::class;
    }
}
