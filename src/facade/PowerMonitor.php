<?php

namespace native\thinkphp\facade;

use native\thinkphp\facade\PowerMonitor as PowerMonitorContract;
use native\thinkphp\enums\ThermalStatesEnum;
use \native\thinkphp\fakes\PowerMonitorFake;
use native\thinkphp\enums\SystemIdleStatesEnum;
use think\Facade;

/**
 * @method static SystemIdleStatesEnum getSystemIdleState(int $threshold)
 * @method static int getSystemIdleTime()
 * @method static ThermalStatesEnum getCurrentThermalState()
 * @method static bool isOnBatteryPower()
 */
class PowerMonitor extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(PowerMonitorFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return PowerMonitorContract::class;
    }
}
