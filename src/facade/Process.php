<?php

namespace native\thinkphp\facade;


use think\Facade;

/**
 * @method static string arch()
 * @method static string platform()
 * @method static float uptime()
 * @method static object fresh()
 */
class Process extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \native\thinkphp\Process::class;
    }
}
