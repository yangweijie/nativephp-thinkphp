<?php

namespace native\thinkphp\contract;
use native\thinkphp\windows\Window;

interface WindowManager
{
    public function open(string $id = 'main');

    public function close($id = null);

    public function hide($id = null);

    public function current(): Window;

    /**
     * @return array<int, Window>
     */
    public function all(): array;

    public function get(string $id): Window;
}
