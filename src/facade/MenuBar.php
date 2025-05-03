<?php

namespace native\thinkphp\facade;


use native\thinkphp\menu\Menu;
use native\thinkphp\menuBar\MenuBarManager;
use native\thinkphp\menuBar\PendingCreateMenuBar;
use think\Facade;

/**
 * @method static PendingCreateMenuBar create()
 * @method static void show()
 * @method static void hide()
 * @method static void label(string $label)
 * @method static void contextMenu(Menu $contextMenu)
 */
class MenuBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MenuBarManager::class;
    }
}
