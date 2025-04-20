<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error - {{ config('native.app.name') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8f9fa;
            color: #212529;
            line-height: 1.5;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            padding: 2rem;
        }
        .error-type {
            color: #dc3545;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .error-location {
            font-family: monospace;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        .stack-trace {
            font-family: monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
        }
        .frame {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            border-radius: 4px;
        }
        .frame:hover {
            background: #e9ecef;
        }
        .frame-file {
            color: #6c757d;
        }
        .frame-line {
            color: #007bff;
        }
        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }
        .button {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        .button-primary {
            background: #007bff;
            color: white;
        }
        .button-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-type">{{ $error['type'] }}</div>
        
        <div class="error-message">
            {{ $error['message'] }}
        </div>

        <div class="error-location">
            in {{ $error['file'] }}:{{ $error['line'] }}
        </div>

        <h3>Stack Trace:</h3>
        <div class="stack-trace">
            @foreach($error['trace'] as $frame)
                <div class="frame">
                    @if(isset($frame['file']))
                        <span class="frame-file">{{ $frame['file'] }}</span>
                        <span class="frame-line">:{{ $frame['line'] }}</span>
                    @endif
                    @if(isset($frame['class']))
                        <br>{{ $frame['class'] }}{{ $frame['type'] }}{{ $frame['function'] }}()
                    @elseif(isset($frame['function']))
                        <br>{{ $frame['function'] }}()
                    @endif
                </div>
            @endforeach
        </div>

        <div class="actions">
            <button onclick="window.history.back()" class="button button-primary">
                返回上一页
            </button>
            
            <button onclick="window.location.reload()" class="button button-secondary">
                刷新页面
            </button>
        </div>
    </div>

    @if(config('app.debug'))
    <script>
        // 在开发环境下添加错误报告功能
        window.onerror = function(msg, url, line, col, error) {
            window.native.ipc.send('native:error', {
                type: error?.name || 'Error',
                message: msg,
                file: url,
                line: line,
                column: col,
                stack: error?.stack
            });
        };

        // 捕获未处理的 Promise 错误
        window.addEventListener('unhandledrejection', function(event) {
            window.native.ipc.send('native:error', {
                type: 'UnhandledPromiseRejection',
                message: event.reason?.message || event.reason,
                stack: event.reason?.stack
            });
        });
    </script>
    @endif
</body>
</html>