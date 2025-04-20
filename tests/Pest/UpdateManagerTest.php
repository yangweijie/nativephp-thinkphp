<?php

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\UpdateManager;
use think\App;

test('更新管理器可以正确解析版本号', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    $method = new ReflectionMethod($manager, 'parseVersion');
    $method->setAccessible(true);
    
    expect($method->invoke($manager, '1.2.3'))->toBe([
        'major' => 1,
        'minor' => 2,
        'patch' => 3
    ]);
    
    expect($method->invoke($manager, '1.2'))->toBe([
        'major' => 1,
        'minor' => 2,
        'patch' => 0
    ]);
    
    expect($method->invoke($manager, '1'))->toBe([
        'major' => 1,
        'minor' => 0,
        'patch' => 0
    ]);
});

test('更新管理器可以正确比较版本号', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    $method = new ReflectionMethod($manager, 'compareVersions');
    $method->setAccessible(true);
    
    $v1 = ['major' => 1, 'minor' => 2, 'patch' => 3];
    $v2 = ['major' => 1, 'minor' => 2, 'patch' => 3];
    $v3 = ['major' => 2, 'minor' => 0, 'patch' => 0];
    $v4 = ['major' => 1, 'minor' => 2, 'patch' => 4];
    
    expect($method->invoke($manager, $v1, $v2))->toBe(0);
    expect($method->invoke($manager, $v3, $v1))->toBe(1);
    expect($method->invoke($manager, $v1, $v3))->toBe(-1);
    expect($method->invoke($manager, $v4, $v1))->toBe(1);
});

test('更新管理器可以检测可用更新', function() {
    $app = getMockApp([
        'native.updater.enabled' => true,
        'native.updater.server' => 'https://example.com',
        'native.updater.channel' => 'stable',
        'native.app.version' => '1.0.0'
    ]);
    
    $manager = new UpdateManager($app);
    
    // 模拟更新服务器响应
    $updates = [
        'versions' => [
            [
                'version' => '1.1.0',
                'channel' => 'stable',
                'url' => 'https://example.com/updates/v1.1.0.zip',
                'notes' => '版本更新说明'
            ]
        ]
    ];
    
    // 使用 php://memory 来模拟文件操作
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, json_encode($updates));
    rewind($stream);
    
    // 替换 file_get_contents 函数
    uopz_set_return('file_get_contents', function($url) use ($stream) {
        if (strpos($url, 'updates.json') !== false) {
            return stream_get_contents($stream);
        }
        return false;
    }, true);
    
    $update = $manager->checkForUpdates();
    expect($update)->not->toBeNull();
    expect($update['version'])->toBe('1.1.0');
    
    // 清理
    uopz_unset_return('file_get_contents');
    fclose($stream);
});

test('更新管理器可以验证更新包签名', function() {
    $app = getMockApp([
        'native.updater.pubkey' => '-----BEGIN PUBLIC KEY-----\nMIIBIjANB...'
    ]);
    
    $manager = new UpdateManager($app);
    
    $method = new ReflectionMethod($manager, 'verifySignature');
    $method->setAccessible(true);
    
    // 创建测试数据
    $testData = 'test update data';
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $testData);
    rewind($stream);
    
    // 模拟签名
    $privKey = openssl_pkey_new();
    openssl_pkey_export($privKey, $privateKey);
    $pubKey = openssl_pkey_get_details($privKey)['key'];
    
    openssl_sign($testData, $signature, $privKey, OPENSSL_ALGO_SHA256);
    $signature = base64_encode($signature);
    
    // 替换函数
    uopz_set_return('file_get_contents', function() use ($stream) {
        rewind($stream);
        return stream_get_contents($stream);
    }, true);
    
    expect($method->invoke($manager, 'test.zip', $signature, $pubKey))->toBeTrue();
    
    // 清理
    uopz_unset_return('file_get_contents');
    fclose($stream);
});

test('更新管理器可以下载更新', function() {
    $app = getMockApp([
        'native.updater.download_dir' => sys_get_temp_dir()
    ]);
    
    $manager = new UpdateManager($app);
    
    $update = [
        'version' => '1.1.0',
        'url' => 'https://example.com/updates/v1.1.0.zip'
    ];
    
    // 模拟下载内容
    $content = 'fake update content';
    uopz_set_return('file_get_contents', function($url) use ($content) {
        if (strpos($url, 'v1.1.0.zip') !== false) {
            return $content;
        }
        return false;
    }, true);
    
    $filePath = $manager->downloadUpdate($update);
    expect($filePath)->toContain('v1.1.0.zip');
    expect(file_exists($filePath))->toBeTrue();
    expect(file_get_contents($filePath))->toBe($content);
    
    // 清理
    unlink($filePath);
    uopz_unset_return('file_get_contents');
});

test('更新管理器可以处理安装更新', function() {
    $app = getMockApp([
        'native.updater.install_mode' => 'prompt'
    ]);
    
    $manager = new UpdateManager($app);
    
    // 创建临时文件
    $tempFile = tempnam(sys_get_temp_dir(), 'update_');
    file_put_contents($tempFile, 'test update content');
    
    // 记录事件触发
    $eventTriggered = false;
    $eventData = null;
    
    // 模拟 Bridge
    $bridge = new class {
        public function emit($event, $data) {
            global $eventTriggered, $eventData;
            $eventTriggered = true;
            $eventData = $data;
        }
    };
    
    // 模拟 Native
    $native = new class($bridge) {
        protected $bridge;
        public function __construct($bridge) {
            $this->bridge = $bridge;
        }
        public function bridge() {
            return $this->bridge;
        }
    };
    
    allow($app)->toReceive('native')->andReturn($native);
    
    $manager->installUpdate($tempFile);
    
    expect($eventTriggered)->toBeTrue();
    expect($eventData)->toHaveKeys(['path', 'mode']);
    expect($eventData['path'])->toBe($tempFile);
    expect($eventData['mode'])->toBe('prompt');
    
    // 清理
    unlink($tempFile);
});

test('更新管理器可以检测版本冲突', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    // 测试不兼容的版本
    expect($manager->isCompatible('2.0.0', '^1.0.0'))->toBeFalse();
    expect($manager->isCompatible('1.0.0', '^2.0.0'))->toBeFalse();
    
    // 测试兼容的版本
    expect($manager->isCompatible('1.1.0', '^1.0.0'))->toBeTrue();
    expect($manager->isCompatible('1.0.1', '^1.0.0'))->toBeTrue();
});

test('更新管理器可以处理预发布版本', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    // 测试预发布版本解析
    expect($manager->parseVersion('1.0.0-beta.1'))->toBe([
        'major' => 1,
        'minor' => 0,
        'patch' => 0,
        'prerelease' => 'beta.1'
    ]);
    
    // 测试预发布版本比较
    expect($manager->isNewer('1.0.0', '1.0.0-beta.1'))->toBeTrue();
    expect($manager->isNewer('1.0.0-beta.2', '1.0.0-beta.1'))->toBeTrue();
});

test('更新管理器可以处理更新通道', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    // 设置测试配置
    Config::set([
        'native.updater.channel' => 'beta'
    ]);
    
    // 模拟更新检查
    $updates = [
        [
            'version' => '1.1.0',
            'channel' => 'stable'
        ],
        [
            'version' => '1.2.0-beta.1',
            'channel' => 'beta'
        ]
    ];
    
    expect($manager->filterUpdatesByChannel($updates))->toHaveCount(1)
        ->and($manager->filterUpdatesByChannel($updates)[0]['version'])->toBe('1.2.0-beta.1');
});

test('更新管理器可以处理增量更新', function() {
    $app = getMockApp();
    $manager = new UpdateManager($app);
    
    // 模拟当前版本
    allow($app->config)->toReceive('get')->with('app.version')->andReturn('1.0.0');
    
    // 模拟可用更新
    $updates = [
        ['version' => '1.0.1', 'type' => 'patch'],
        ['version' => '1.1.0', 'type' => 'minor'],
        ['version' => '2.0.0', 'type' => 'major']
    ];
    
    // 测试版本筛选
    $filtered = $manager->filterCompatibleUpdates($updates);
    expect($filtered)->toHaveCount(2)
        ->and($filtered[0]['version'])->toBe('1.0.1')
        ->and($filtered[1]['version'])->toBe('1.1.0');
});

/**
 * 获取模拟的 App 实例
 */
function getMockApp($config = [])
{
    $app = mock(App::class)->expect(
        config: fn() => new class($config) {
            protected $config;
            public function __construct($config) {
                $this->config = $config;
            }
            public function get($key, $default = null) {
                return $this->config[$key] ?? $default;
            }
        }
    );
    
    return $app;
}