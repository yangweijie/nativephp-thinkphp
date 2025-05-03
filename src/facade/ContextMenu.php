<?php

namespace native\thinkphp\facade;

use native\thinkphp\menu\Menu;
use think\Facade;

/**
 * @method static void register(Menu $menu)
 * @method static void remove()
 */
class ContextMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ContextMenu::class;
    }
}
