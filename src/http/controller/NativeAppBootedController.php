<?php

namespace native\thinkphp\http\controller;

use Native\Laravel\Events\App\ApplicationBooted;
use think\facade\Request;

class NativeAppBootedController
{
    public function __invoke(Request $request): \think\response\Json
    {
        $provider = app(config('nativephp.provider'));
        $provider->boot();

        event(new ApplicationBooted);

        return json([
            'success' => true,
        ]);
    }
}
