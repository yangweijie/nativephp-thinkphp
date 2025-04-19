# 缓存系统

NativePHP 提供了强大的缓存系统，可以帮助您提高应用程序的性能。缓存系统支持多种驱动，包括内存缓存、ThinkPHP 缓存和 Redis 缓存。

## 配置缓存

您可以在 `config/native.php` 文件中配置缓存系统：

```php
'cache' => [
    'driver' => env('NATIVEPHP_CACHE_DRIVER', 'memory'),
    'ttl' => env('NATIVEPHP_CACHE_TTL', 60),
    'prefix' => env('NATIVEPHP_CACHE_PREFIX', 'native:'),
    
    // Redis 配置
    'host' => env('NATIVEPHP_REDIS_HOST', '127.0.0.1'),
    'port' => env('NATIVEPHP_REDIS_PORT', 6379),
    'password' => env('NATIVEPHP_REDIS_PASSWORD', null),
    'database' => env('NATIVEPHP_REDIS_DATABASE', 0),
],
```

### 缓存驱动

NativePHP 支持以下缓存驱动：

- `memory`：内存缓存，数据存储在内存中，应用程序重启后数据会丢失
- `think`：ThinkPHP 缓存，使用 ThinkPHP 的缓存系统
- `redis`：Redis 缓存，使用 Redis 数据库存储缓存数据

### 缓存配置项

- `driver`：缓存驱动，可选值为 `memory`、`think` 或 `redis`
- `ttl`：缓存过期时间（秒）
- `prefix`：缓存键前缀

### Redis 配置项

如果您使用 Redis 缓存驱动，还需要配置以下选项：

- `host`：Redis 服务器主机名或 IP 地址
- `port`：Redis 服务器端口
- `password`：Redis 服务器密码
- `database`：Redis 数据库索引

## 使用缓存

NativePHP 的核心组件（如 ChildProcess 和 QueueWorker）已经集成了缓存系统，您无需手动管理缓存。但是，如果您需要在自己的代码中使用缓存，可以通过以下方式获取缓存适配器：

```php
use Native\ThinkPHP\Facades\App;

// 获取缓存适配器
$cache = App::make('native.cache');

// 设置缓存
$cache->set('key', 'value', 60); // 缓存 60 秒

// 获取缓存
$value = $cache->get('key', 'default'); // 如果缓存不存在，则返回默认值

// 检查缓存是否存在
$exists = $cache->has('key');

// 删除缓存
$cache->delete('key');

// 清除所有缓存
$cache->clear();
```

## 自定义缓存适配器

如果您需要使用自定义的缓存适配器，可以实现 `Native\ThinkPHP\Contracts\CacheAdapter` 接口：

```php
use Native\ThinkPHP\Contracts\CacheAdapter;

class MyCustomCacheAdapter implements CacheAdapter
{
    // 实现接口方法
}
```

然后，您可以在服务提供者中注册自定义缓存适配器：

```php
$this->app->bind('native.cache', function () {
    return new MyCustomCacheAdapter();
});
```

## 为组件设置缓存适配器

您可以为 ChildProcess 和 QueueWorker 组件设置自定义的缓存适配器：

```php
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Cache\RedisAdapter;

// 创建 Redis 缓存适配器
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$cacheAdapter = new RedisAdapter($redis, 'native:', 60);

// 为 ChildProcess 设置缓存适配器
ChildProcess::setCacheAdapter($cacheAdapter);

// 为 QueueWorker 设置缓存适配器
QueueWorker::setCacheAdapter($cacheAdapter);
```

## 缓存键

NativePHP 使用以下缓存键格式：

- ChildProcess：`process:{alias}`
- ChildProcess（所有进程）：`process:all`
- QueueWorker：`queue-worker:{alias}`
- QueueWorker（所有工作进程）：`queue-worker:all`

其中 `{alias}` 是进程或工作进程的别名。
