<?php

use think\facade\Route;

// NativePHP 路由
Route::group('native-php', function () {
    // 配置路由
    Route::get('config', 'NativePHP\Think\Controller\ElectronController@config');

    // IPC 路由
    Route::post('ipc', 'NativePHP\Think\Controller\ElectronController@ipc');

    // 调用路由
    Route::post('invoke', 'NativePHP\Think\Controller\ElectronController@invoke');
});
