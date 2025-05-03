<?php

use native\thinkphp\support\HigherOrderTapProxy;

if (! function_exists('database_path')) {
    /**
     * Get the database path.
     *
     * @param  string  $path
     * @return string
     */
    function database_path($path = '')
    {
        return root_path($path?:'database');
    }
}

if (! function_exists('tap')) {
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @template TValue
     *
     * @param  TValue  $value
     * @param (callable(TValue): mixed)|null $callback
     * @return ($callback is null ? HigherOrderTapProxy : TValue)
     */
    function tap($value, callable $callback = null)
    {
        if (is_null($callback)) {
            return new HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }
}

if (! function_exists('abort_if')) {
    /**
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param bool $boolean
     * @param int $code
     * @param string $message
     * @param  array  $headers
     * @return void
     *
     */
    function abort_if(bool $boolean, int $code, string $message = '', array $headers = []): void
    {
        if ($boolean) {
            abort($code, $message, $headers);
        }
    }
}