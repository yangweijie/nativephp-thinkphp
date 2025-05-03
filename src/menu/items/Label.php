<?php

namespace native\thinkphp\menu\items;

class Label extends MenuItem
{
    public function __construct(
        protected ?string $label,
        protected ?string $accelerator = null
    ) {}
}
