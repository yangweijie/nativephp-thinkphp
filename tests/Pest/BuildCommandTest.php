<?php

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\Commands\BuildCommand;
use Symfony\Component\Process\Process;
use think\console\Output;
use think\console\Input;
use think\facade\Config;

beforeEach(function () {
    if (!is_dir(base_path('build'))) {
        mkdir(base_path('build'), 0755, true);
    }
});

afterEach(function () {
    if (is_dir(base_path('build'))) {
        array_map('unlink', glob(base_path('build/*.*')));
        rmdir(base_path('build'));
    }
});

test('构建命令可以正确配置', function () {
    $command = new BuildCommand();
    
    expect($command->getName())->toBe('native:build');
    
    $definition = $command->getDefinition();
    
    expect($definition->hasOption('platform'))->toBeTrue()
        ->and($definition->hasOption('arch'))->toBeTrue()
        ->and($definition->hasOption('target'))->toBeTrue()
        ->and($definition->hasOption('no-sign'))->toBeTrue()
        ->and($definition->hasOption('output'))->toBeTrue();
});

test('构建命令生成正确的构建配置', function () {
    $command = new BuildCommand();
    
    Config::set([
        'native' => [
            'app' => [
                'name' => 'Test App',
                'version' => '1.0.0',
            ],
            'window' => [
                'width' => 800,
                'height' => 600,
            ],
            'updater' => [
                'enabled' => true,
                'url' => 'http://example.com/updates',
            ],
        ],
    ]);
    
    $input = new \think\console\Input([
        '--platform' => 'macos',
        '--arch' => 'arm64',
        '--target' => 'production',
    ]);
    
    $output = new Output();
    $buildDir = base_path('build');
    
    $command->generateBuildConfig($input, $output, $buildDir);
    
    expect(file_exists($buildDir . '/build.json'))->toBeTrue();
    
    $config = json_decode(file_get_contents($buildDir . '/build.json'), true);
    
    expect($config)->toHaveKeys(['build', 'app', 'window', 'updater'])
        ->and($config['build'])->toHaveKeys(['platform', 'arch', 'target', 'sign'])
        ->and($config['build']['platform'])->toBe('macos')
        ->and($config['build']['arch'])->toBe('arm64')
        ->and($config['build']['target'])->toBe('production');
});

test('构建命令可以检测构建环境', function () {
    $command = new BuildCommand();
    $output = new Output();
    
    expect($command->checkBuildEnvironment($output))->toBeTrue();
});

test('构建命令可以复制资源文件', function () {
    $command = new BuildCommand();
    $buildDir = base_path('build');
    
    $command->copyResources($buildDir);
    
    expect(is_dir($buildDir . '/resources'))->toBeTrue();
});

test('构建命令可以验证平台参数', function() {
    $command = new BuildCommand();
    
    // 有效的平台
    expect(fn() => $command->getTargetTriple('windows'))->not->toThrow(InvalidArgumentException::class);
    expect(fn() => $command->getTargetTriple('macos'))->not->toThrow(InvalidArgumentException::class);
    expect(fn() => $command->getTargetTriple('linux'))->not->toThrow(InvalidArgumentException::class);
    
    // 无效的平台
    expect(fn() => $command->getTargetTriple('invalid'))->toThrow(InvalidArgumentException::class);
});

test('构建命令可以处理macOS通用二进制选项', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'macos',
        '--universal' => true
    ]);
    
    $output = mockOutput();
    
    // 模拟Process执行
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令正确处理调试和发布选项', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    // 测试同时使用调试和发布选项
    $input = mockInput([
        'platform' => 'windows',
        '--debug' => true,
        '--release' => true
    ]);
    
    $output = mockOutput();
    $result = $command->execute($input, $output);
    expect($result)->toBe(1);
    
    // 测试默认使用发布选项
    $input = mockInput([
        'platform' => 'windows'
    ]);
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令可以处理更新器选项', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'windows',
        '--updater' => true
    ]);
    
    $output = mockOutput();
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true,
        setEnv: function($env) {
            expect($env['TAURI_UPDATER_ENABLED'])->toBe('1');
            return $this;
        }
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令可以处理自定义图标', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'windows',
        '--icon' => '/path/to/custom/icon.png'
    ]);
    
    $output = mockOutput();
    
    // 模拟构建目录
    @mkdir('/path/to/app/runtime/build', 0755, true);
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
    
    // 清理测试目录
    @rmdir('/path/to/app/runtime/build');
});

test('构建命令可以处理额外资源', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'windows',
        '--resources' => '/path/to/resources'
    ]);
    
    $output = mockOutput();
    
    // 模拟构建目录
    @mkdir('/path/to/app/runtime/build', 0755, true);
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
    
    // 清理测试目录
    @rmdir('/path/to/app/runtime/build');
});

test('构建命令可以处理自定义输出路径', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $customOutput = '/path/to/custom/output';
    $input = mockInput([
        'platform' => 'windows',
        '--output' => $customOutput
    ]);
    
    $output = mockOutput();
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true,
        setEnv: fn($env) => $this
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令正确处理构建环境验证', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $output = mockOutput();
    
    // 模拟缺少必要工具的情况
    $mockFailedProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => false
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockFailedProcess);
    
    $result = $command->execute(
        mockInput(['platform' => 'windows']),
        $output
    );
    expect($result)->toBe(1);
    
    // 模拟所有工具都已安装的情况
    $mockSuccessProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockSuccessProcess);
    
    $result = $command->execute(
        mockInput(['platform' => 'windows']),
        $output
    );
    expect($result)->toBe(0);
});

test('构建命令可以正确显示构建产物信息', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'windows',
        '--output' => '/path/to/build/output'
    ]);
    
    $output = mockOutput();
    
    // 模拟构建成功
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令可以正确初始化', function () {
    $command = new \NativePHP\Think\Commands\BuildCommand();
    
    expect($command->getName())->toBe('native:build');
});

test('构建命令检查环境依赖', function () {
    $command = new \NativePHP\Think\Commands\BuildCommand();
    
    // 模拟输出对象
    $output = Mockery::mock('think\console\Output');
    $output->shouldReceive('writeln')->withAnyArgs()->times(0);
    
    expect($command->checkBuildEnvironment($output))->toBeTrue();
});

test('构建命令可以生成配置', function () {
    $command = new \NativePHP\Think\Commands\BuildCommand();
    
    // 设置测试配置
    Config::set([
        'native' => [
            'app' => ['name' => 'TestApp'],
            'window' => ['title' => 'Test Window'],
            'updater' => ['enabled' => true]
        ]
    ]);
    
    // 模拟输入输出对象
    $input = Mockery::mock('think\console\Input');
    $input->shouldReceive('getOption')->with('platform')->andReturn('macos');
    $input->shouldReceive('getOption')->with('arch')->andReturn('x64');
    $input->shouldReceive('getOption')->with('target')->andReturn('production');
    $input->shouldReceive('getOption')->with('no-sign')->andReturn(false);
    $input->shouldReceive('getOption')->with('output')->andReturn(null);
    
    $output = Mockery::mock('think\console\Output');
    
    // 创建临时构建目录
    $buildDir = sys_get_temp_dir() . '/build_test';
    @mkdir($buildDir, 0755, true);
    
    // 生成构建配置
    $command->generateBuildConfig($input, $output, $buildDir);
    
    // 验证生成的配置文件
    expect(file_exists($buildDir . '/build.json'))->toBeTrue();
    
    $config = json_decode(file_get_contents($buildDir . '/build.json'), true);
    expect($config)->toHaveKey('build');
    expect($config)->toHaveKey('app');
    expect($config)->toHaveKey('window');
    expect($config)->toHaveKey('updater');
    
    // 清理临时目录
    @unlink($buildDir . '/build.json');
    @rmdir($buildDir);
});

test('更新器命令可以管理配置', function () {
    $command = new \NativePHP\Think\Commands\UpdaterCommand();
    
    // 模拟输入输出对象
    $input = Mockery::mock('think\console\Input');
    $input->shouldReceive('getOption')->with('enable')->andReturn(true);
    $input->shouldReceive('getOption')->with('disable')->andReturn(false);
    $input->shouldReceive('getOption')->with('server')->andReturn(null);
    $input->shouldReceive('getOption')->with('channel')->andReturn(null);
    $input->shouldReceive('getOption')->with('key')->andReturn(null);
    $input->shouldReceive('getOption')->with('sign')->andReturn(null);
    $input->shouldReceive('getOption')->with('verify')->andReturn(null);
    
    $output = Mockery::mock('think\console\Output');
    $output->shouldReceive('writeln')->with('<info>已启用自动更新</info>');
    
    // 执行命令
    $result = $command->execute($input, $output);
    
    expect($result)->toBe(0);
    expect(Config::get('native.updater.enabled'))->toBeTrue();
});

test('更新器可以验证签名', function () {
    // 创建测试更新包
    $packagePath = sys_get_temp_dir() . '/test_update.zip';
    file_put_contents($packagePath, 'test update content');
    
    // 生成测试密钥对
    $config = [
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'private_key_bits' => 2048,
    ];
    
    $key = openssl_pkey_new($config);
    openssl_pkey_export($key, $privateKey);
    $publicKey = openssl_pkey_get_details($key)['key'];
    
    // 生成签名
    $signature = generate_update_signature($packagePath, $privateKey);
    
    // 验证签名
    expect(verify_update_signature($packagePath, $signature, $publicKey))->toBeTrue();
    
    // 清理测试文件
    @unlink($packagePath);
});

test('构建命令可以处理编译选项', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'windows',
        '--debug' => true,
        '--arch' => 'x64'
    ]);
    
    $output = mockOutput();
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true,
        setEnv: function($env) {
            expect($env['RUSTFLAGS'])->toBe('-C target-feature=+crt-static');
            return $this;
        }
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令可以处理环境变量', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'macos',
        '--target' => 'production'
    ]);
    
    $output = mockOutput();
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => true,
        setEnv: function($env) {
            expect($env)->toHaveKey('MACOSX_DEPLOYMENT_TARGET');
            expect($env)->toHaveKey('CARGO_BUILD_TARGET');
            return $this;
        }
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(0);
});

test('构建命令可以处理错误输出', function() {
    $app = mockApp();
    $command = new BuildCommand();
    $command->setApp($app);
    
    $input = mockInput([
        'platform' => 'linux'
    ]);
    
    $output = mockOutput();
    
    $mockProcess = mock(Process::class)->expect(
        run: fn() => null,
        isSuccessful: fn() => false,
        getErrorOutput: fn() => '构建失败: 缺少依赖项'
    );
    
    allow(Process::class)->toBeConstructed()->andReturn($mockProcess);
    
    $result = $command->execute($input, $output);
    expect($result)->toBe(1);
});

/**
 * 模拟 App 实例
 */
function mockApp() {
    return new class {
        public function getRootPath() {
            return '/path/to/app';
        }
        
        public function getRuntimePath() {
            return '/path/to/app/runtime/';
        }
    };
}

/**
 * 模拟输入
 */
function mockInput($args = []) {
    return new class($args) extends Input {
        protected $args;
        
        public function __construct($args) {
            $this->args = $args;
        }
        
        public function getArgument($name) {
            return $this->args[$name] ?? null;
        }
        
        public function getOption($name) {
            return $this->args['--'.$name] ?? false;
        }
    };
}

/**
 * 模拟输出
 */
function mockOutput() {
    return new class extends Output {
        public function write($messages, $newline = false, $options = 0) {}
        public function writeln($messages, $options = 0) {}
    };
}