<?php

namespace native\thinkphp\facade;


use think\Facade;

/**
 * @method static static title(string $title)
 * @method static static event(string $event)
 * @method static static message(string $body)
 * @method static void show()
 */
class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \native\thinkphp\Notification::class;
    }
}
