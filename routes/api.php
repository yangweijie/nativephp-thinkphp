<?php

use think\middleware\FormTokenCheck;
use Illuminate\Support\Facades\Route;
use native\thinkphp\http\controller\CreateSecurityCookieController;
use native\thinkphp\http\controller\DispatchEventFromAppController;
use native\thinkphp\http\controller\NativeAppBootedController;
use native\thinkphp\http\middleware\PreventRegularBrowserAccess;

Route::group(['middleware' => PreventRegularBrowserAccess::class], function () {
    Route::post('_native/api/booted', NativeAppBootedController::class);
    Route::post('_native/api/events', DispatchEventFromAppController::class);
})->withoutMiddleware(FormTokenCheck::class);

Route::get('_native/api/cookie', CreateSecurityCookieController::class);
