<?php

namespace native\thinkphp\http\controller;


class CreateSecurityCookieController
{
    public function __call($method, $args)
    {
        $request =  request();
        abort_if($request->get('secret') !== config('native-php.secret'), 403);
        return redirect('/')->cookie('_php_native', config('native-php.secret'), [
            'domain'=>'localhost',
            'httponly' => true,
        ]);
    }
}
