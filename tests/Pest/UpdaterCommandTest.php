<?php

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\Commands\UpdaterCommand;
use think\console\Output;
use think\console\Input;

test('更新器命令可以正确配置', function() {
    $command = new UpdaterCommand();
    
    expect($command->getName())->toBe('native:updater');
    
    $definition = $command->getDefinition();
    expect($definition->hasOption('enable'))->toBeTrue();
    expect($definition->hasOption('disable'))->toBeTrue();
    expect($definition->hasOption('server'))->toBeTrue();
    expect($definition->hasOption('key'))->toBeTrue();
    expect($definition->hasOption('channel'))->toBeTrue();
});

test('更新器命令可以启用自动更新', function() {
    $app = mockApp();
    $command = new UpdaterCommand();
    $command->setApp($app);
    
    $input = mockInput([
        '--enable' => true
    ]);
    
    $output = mockOutput();
    
    // 模拟配置文件
    $tauriConfig = [
        'tauri' => [
            'updater' => [
                'active' => false
            ]
        ]
    ];
    
    // 创建临时配置文件
    $tempConfig = tempnam(sys_get_temp_dir(), 'tauri_');
    file_put_contents($tempConfig, json_encode($tauriConfig));
    
    // 模拟应用根目录
    allow($app)->toReceive('getRootPath')->andReturn(dirname($tempConfig));
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
    
    // 验证配置更新
    $updatedConfig = json_decode(file_get_contents($tempConfig), true);
    expect($updatedConfig['tauri']['updater']['active'])->toBeTrue();
    
    // 清理临时文件
    unlink($tempConfig);
});

test('更新器命令可以设置更新服务器', function() {
    $app = mockApp();
    $command = new UpdaterCommand();
    $command->setApp($app);
    
    $serverUrl = 'https://updates.example.com';
    $input = mockInput([
        '--server' => $serverUrl
    ]);
    
    $output = mockOutput();
    
    // 模拟配置文件
    $tauriConfig = [
        'tauri' => [
            'updater' => [
                'endpoints' => []
            ]
        ]
    ];
    
    // 创建临时配置文件
    $tempConfig = tempnam(sys_get_temp_dir(), 'tauri_');
    file_put_contents($tempConfig, json_encode($tauriConfig));
    
    // 模拟应用根目录
    allow($app)->toReceive('getRootPath')->andReturn(dirname($tempConfig));
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
    
    // 验证配置更新
    $updatedConfig = json_decode(file_get_contents($tempConfig), true);
    expect($updatedConfig['tauri']['updater']['endpoints'][0]['url'])->toBe($serverUrl . '/updates.json');
    
    // 清理临时文件
    unlink($tempConfig);
});

test('更新器命令可以设置更新密钥', function() {
    $app = mockApp();
    $command = new UpdaterCommand();
    $command->setApp($app);
    
    $publicKey = 'test-public-key';
    $input = mockInput([
        '--key' => $publicKey
    ]);
    
    $output = mockOutput();
    
    // 模拟配置文件
    $tauriConfig = [
        'tauri' => [
            'updater' => [
                'pubkey' => ''
            ]
        ]
    ];
    
    // 创建临时配置文件
    $tempConfig = tempnam(sys_get_temp_dir(), 'tauri_');
    file_put_contents($tempConfig, json_encode($tauriConfig));
    
    // 模拟应用根目录
    allow($app)->toReceive('getRootPath')->andReturn(dirname($tempConfig));
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
    
    // 验证配置更新
    $updatedConfig = json_decode(file_get_contents($tempConfig), true);
    expect($updatedConfig['tauri']['updater']['pubkey'])->toBe($publicKey);
    
    // 清理临时文件
    unlink($tempConfig);
});

test('更新器命令可以设置更新通道', function() {
    $app = mockApp();
    $command = new UpdaterCommand();
    $command->setApp($app);
    
    $channel = 'beta';
    $input = mockInput([
        '--channel' => $channel
    ]);
    
    $output = mockOutput();
    
    // 模拟应用配置
    allow($app->config)->toReceive('get')->with('native.updater', [])->andReturn([]);
    allow($app->config)->toReceive('set')->with(['updater' => ['channel' => $channel]], 'native');
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('更新器命令正确处理无效的配置文件', function() {
    $app = mockApp();
    $command = new UpdaterCommand();
    $command->setApp($app);
    
    $input = mockInput([
        '--enable' => true
    ]);
    
    $output = mockOutput();
    
    // 模拟不存在的配置文件
    allow($app)->toReceive('getRootPath')->andReturn('/invalid/path');
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0); // 命令应该正常退出，但会显示错误信息
});