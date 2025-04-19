<?php

namespace NativePHP\Think\Facades;

use think\Facade;

/**
 * @method static \NativePHP\Think\Window window(string $label = 'main')
 * @method static \NativePHP\Think\Menu menu()
 * @method static \NativePHP\Think\Tray tray()
 * @method static \NativePHP\Think\Ipc ipc()
 * @method static \NativePHP\Think\Hotkey hotkey()
 * @method static \NativePHP\Think\WindowManager windowManager()
 * @method static \NativePHP\Think\WindowState windowState()
 * @method static \NativePHP\Think\WindowPresets windowPresets()
 * @method static \NativePHP\Think\WindowLayoutPresets windowLayoutPresets()
 * @method static \NativePHP\Think\EventDispatcher events()
 */
class Native extends Facade
{
    protected static function getFacadeClass()
    {
        return \NativePHP\Think\Native::class;
    }
}