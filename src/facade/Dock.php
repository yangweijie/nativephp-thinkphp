<?php

namespace native\thinkphp\facade;


use native\thinkphp\menu\Menu;
use think\Facade;

/**
 * @method static void bounce()
 * @method static void|string badge(string $type = null)
 * @method static void cancelBounce()
 * @method static void hide()
 * @method static void icon(string $Path)
 * @method static void menu(Menu $menu)
 * @method static void show()
 */
class Dock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \native\thinkphp\Dock::class;
    }
}
