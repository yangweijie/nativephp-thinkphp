<?php

namespace native\thinkphp\menu\items;

class Radio extends MenuItem
{
    protected string $type = 'radio';

    public function __construct(
        protected ?string $label,
        protected bool $isChecked = false,
        protected ?string $accelerator = null
    ) {}
}
