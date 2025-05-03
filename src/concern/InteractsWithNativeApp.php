<?php

namespace native\thinkphp\concern;

use Closure;
use native\thinkphp\ProgressBar;

trait InteractsWithNativeApp
{
    public function withProgressBar($totalSteps, Closure $callback)
    {
        $bar = ProgressBar::create(
            is_iterable($totalSteps) ? count($totalSteps) : $totalSteps
        );

        $bar->start();

        if (is_iterable($totalSteps)) {
            foreach ($totalSteps as $value) {
                $callback($value, $bar);

                $bar->advance();
            }
        } else {
            $callback($bar);
        }

        $bar->finish();

        if (is_iterable($totalSteps)) {
            return $totalSteps;
        }
    }
}
