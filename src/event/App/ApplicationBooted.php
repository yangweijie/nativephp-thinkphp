<?php

namespace native\thinkphp\event\App;

use Illuminate\Broadcasting\InteractsWithSockets;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationBooted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct() {}
}
