<?php

namespace NativePHP\Think\Facades;

use think\Facade;

/**
 * @method static array|null checkForUpdates()
 * @method static string downloadUpdate(array $update)
 * @method static void installUpdate(string $filePath)
 * 
 * @see \NativePHP\Think\UpdateManager
 */
class Updater extends Facade
{
    protected static function getFacadeClass()
    {
        return 'native.updater';
    }
}