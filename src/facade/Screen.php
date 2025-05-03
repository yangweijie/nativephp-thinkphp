<?php

namespace native\thinkphp\facade;


use think\Facade;

/**
 * @method static object cursorPosition()
 * @method static array displays()
 * @method static array getCenterOfActiveScreen()
 * @method static array active()
 * @method static array primary()
 */
class Screen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \native\thinkphp\Screen::class;
    }
}
