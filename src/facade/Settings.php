<?php

namespace native\thinkphp\facade;


use think\Facade;

/**
 * @method static void set(string $key, $value)
 * @method static mixed get(string $key, $default = null)
 * @method static void forget(string $key)
 * @method static void clear()
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \native\thinkphp\Settings::class;
    }
}
