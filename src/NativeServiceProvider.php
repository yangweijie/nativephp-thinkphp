<?php

namespace NativePHP\Think;

use think\Service;
use NativePHP\Think\Commands\InstallCommand;
use NativePHP\Think\Commands\ServeCommand;
use NativePHP\Think\Commands\UpdaterCommand;

class NativeServiceProvider extends Service
{
    public function register(): void
    {
        // 注册单例
        $this->app->bind('native', function () {
            return new Native($this->app);
        });

        // 注册更新管理器
        $this->app->bind('native.updater', function () {
            return new UpdateManager($this->app);
        });

        // 注册调试管理器
        $this->app->bind('native.debug', function () {
            return new DebugManager($this->app);
        });

        // 注册性能收集器
        $this->app->bind('native.performance', function () {
            return new Debug\Performance\PerformanceCollector();
        });

        // 注册全局错误处理器
        $this->registerErrorHandler();
    }

    public function boot(): void
    {
        // 注册命令
        $this->commands([
            InstallCommand::class,
            ServeCommand::class,
            UpdaterCommand::class,
        ]);

        // 注册更新检查中间件
        $this->registerMiddleware();

        // 注册性能监控
        if ($this->app->isDebug()) {
            $this->registerPerformanceMonitor();
        }

        // 初始化调试工具
        if ($this->app->isDebug()) {
            $this->initializeDebugTools();
        }
    }

    protected function registerMiddleware()
    {
        $this->app->middleware->add(Middleware\CheckForUpdates::class);
    }

    protected function registerPerformanceMonitor(): void
    {
        // 初始化性能收集器
        $collector = $this->app->make('native.performance');
        
        // 注册性能监听器
        $listener = new Debug\Performance\PerformanceListener($collector);
        $this->app->event->listen('app_init', [$listener, 'handle']);
        
        // 注册中间件
        $this->app->middleware->add(function ($request, \Closure $next) use ($collector) {
            $collector->startTimer('request');
            $collector->addMeasurementPoint('request_start', [
                'url' => $request->url(),
                'method' => $request->method()
            ]);
            
            $response = $next($request);
            
            $collector->stopTimer('request');
            $collector->addMeasurementPoint('request_end');
            $collector->sendToElectron();
            
            return $response;
        });
    }

    protected function registerErrorHandler(): void
    {
        // 设置全局错误处理器
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }

            $error = [
                'type' => $this->getErrorType($errno),
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline,
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            ];

            // 记录错误日志
            $this->logError($error);

            // 在开发环境显示错误
            if ($this->app->isDebug()) {
                $this->showError($error);
            }

            // 发送错误通知
            $this->notifyError($error);

            return true;
        });

        // 设置全局异常处理器
        set_exception_handler(function (\Throwable $e) {
            $error = [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ];

            $this->logError($error);
            
            if ($this->app->isDebug()) {
                $this->showError($error);
            }

            $this->notifyError($error);
        });
    }

    protected function initializeDebugTools(): void
    {
        $debug = $this->app->make('native.debug');

        // 注册调试相关的事件监听器
        $this->app->event->listen('native:error', function ($error) use ($debug) {
            $debug->error('Application error', $error);
        });

        $this->app->event->listen('native:log', function ($level, $message, $context = []) use ($debug) {
            $debug->log($level, $message, $context);
        });

        // 启动性能分析
        $this->app->event->listen('http:request:before', function () use ($debug) {
            $debug->startProfiler();
        });

        $this->app->event->listen('http:request:after', function () use ($debug) {
            $trace = $debug->stopProfiler();
            $debug->log('debug', 'Request profiling', ['trace' => $trace]);
        });
    }

    protected function getErrorType(int $errno): string
    {
        return match ($errno) {
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            default => 'UNKNOWN'
        };
    }

    protected function logError(array $error): void
    {
        $message = sprintf(
            '[%s] %s in %s:%d',
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );

        $this->app->log->error($message, [
            'trace' => $error['trace']
        ]);
    }

    protected function showError(array $error): void
    {
        if ($this->app->request->isAjax()) {
            $this->app->response->json([
                'error' => $error
            ])->send();
            exit;
        }

        // 显示错误页面
        $this->app->response->view('native::error', [
            'error' => $error
        ])->send();
        exit;
    }

    protected function notifyError(array $error): void
    {
        if (class_exists('\think\facade\Notice')) {
            \think\facade\Notice::error(
                sprintf('[%s] %s', $error['type'], $error['message'])
            );
        }

        // 通过 IPC 发送错误到主进程
        $this->app->make('native.ipc')->send('native:error', $error);
    }
}
