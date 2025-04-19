# NativePHP for ThinkPHP 测试系统

本目录包含 NativePHP for ThinkPHP 的测试系统，用于确保代码质量和稳定性。

## 测试结构

测试系统分为以下几个部分：

- `Unit/` - 单元测试，测试各个类的独立功能
- `Feature/` - 功能测试，测试多个类协同工作的功能
- `Integration/` - 集成测试，测试与 Electron 的集成
- `Mock/` - 模拟类，用于模拟 Electron 的行为
- `TestCase.php` - 测试基类，提供通用的测试功能

## 运行测试

### 运行所有测试

```bash
composer test
```

### 运行单元测试

```bash
composer test:unit
```

### 运行功能测试

```bash
composer test:feature
```

### 运行集成测试

```bash
composer test:integration
```

## 编写测试

### 单元测试

单元测试应该测试类的独立功能，不依赖于其他类或外部资源。例如：

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Native\ThinkPHP\Alert;
use Native\ThinkPHP\Client\Client;
use Mockery;

class AlertTest extends TestCase
{
    protected $alert;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(Client::class);
        $this->alert = new Alert(app());
        $this->alert->setClient($this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShow()
    {
        $this->client->shouldReceive('post')
            ->once()
            ->with('alert/message', [
                'message' => 'Test message',
                'type' => null,
                'title' => null,
                'detail' => null,
                'buttons' => null,
                'defaultId' => null,
                'cancelId' => null,
            ])
            ->andReturn(json_response(['result' => 0]));

        $result = $this->alert->show('Test message');

        $this->assertEquals(0, $result);
    }

    public function testError()
    {
        $this->client->shouldReceive('post')
            ->once()
            ->with('alert/error', [
                'title' => 'Test title',
                'message' => 'Test message',
            ])
            ->andReturn(json_response(['result' => true]));

        $result = $this->alert->error('Test title', 'Test message');

        $this->assertTrue($result);
    }
}
```

### 功能测试

功能测试应该测试多个类协同工作的功能。例如：

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Native\ThinkPHP\Facades\Alert;
use Native\ThinkPHP\Facades\Window;
use Mockery;

class AlertWindowTest extends TestCase
{
    public function testAlertInWindow()
    {
        $this->mock(Alert::class)
            ->shouldReceive('show')
            ->once()
            ->with('Test message')
            ->andReturn(0);

        $this->mock(Window::class)
            ->shouldReceive('current')
            ->once()
            ->andReturn((object) ['id' => 1]);

        $controller = new \App\Controller\TestController();
        $result = $controller->showAlertInWindow('Test message');

        $this->assertEquals(['success' => true, 'result' => 0], $result);
    }
}
```

### 集成测试

集成测试应该测试与 Electron 的集成。例如：

```php
<?php

namespace Tests\Integration;

use Tests\TestCase;
use Native\ThinkPHP\Facades\Alert;
use Native\ThinkPHP\Facades\Window;

class ElectronTest extends TestCase
{
    public function testElectronIntegration()
    {
        // 这个测试需要在 Electron 环境中运行
        if (!$this->isRunningInElectron()) {
            $this->markTestSkipped('This test can only run in Electron environment');
        }

        $result = Alert::error('Test title', 'Test message');
        $this->assertTrue($result);

        $window = Window::open('https://example.com');
        $this->assertNotNull($window);
    }

    protected function isRunningInElectron()
    {
        return isset($_SERVER['ELECTRON_RUN_AS_NODE']);
    }
}
```

## 模拟类

模拟类用于模拟 Electron 的行为，使测试可以在没有 Electron 的环境中运行。例如：

```php
<?php

namespace Tests\Mock;

class ElectronMock
{
    public static function alert($options)
    {
        return ['result' => 0];
    }

    public static function window($options)
    {
        return ['id' => 1];
    }
}
```

## 测试覆盖率

测试覆盖率报告可以通过以下命令生成：

```bash
composer test:coverage
```

报告将生成在 `coverage/` 目录中。
