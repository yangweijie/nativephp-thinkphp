<?php

namespace native\thinkphp\http\controller;


use think\facade\Request;

class DispatchEventFromAppController
{
    public function __invoke(Request $request)
    {
        $event = $request->get('event');
        $payload = $request->get('payload', []);

        if (class_exists($event)) {
            $event = new $event(...$payload);
            event($event);
        } else {
            event($event, $payload);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
