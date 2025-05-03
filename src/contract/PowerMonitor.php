<?php

namespace native\thinkphp\contract;

use native\thinkphp\enums\SystemIdleStatesEnum;
use native\thinkphp\enums\ThermalStatesEnum;

interface PowerMonitor
{
    public function getSystemIdleState(int $threshold): SystemIdleStatesEnum;

    public function getSystemIdleTime(): int;

    public function getCurrentThermalState(): ThermalStatesEnum;

    public function isOnBatteryPower(): bool;
}
