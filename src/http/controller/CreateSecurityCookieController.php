<?php

namespace native\thinkphp\http\controller;

use think\facade\Request;

class CreateSecurityCookieController
{
    public function __invoke(Request $request)
    {
        abort_if($request->get('secret') !== config('native-php.secret'), 403);
        return redirect('/')->cookie('_php_native', config('native-php.secret'), [
            'domain'=>'localhost',
            'httponly' => true,
        ]);
    }
}
