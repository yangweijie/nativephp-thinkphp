<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use Native\ThinkPHP\Testing\TestRunner;

class FunctionalTestCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:test:functional';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '运行 NativePHP for ThinkPHP 功能测试';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开始运行功能测试...');

        // 创建测试运行器
        $runner = new TestRunner(App::getInstance());

        // 添加功能测试套件
        $runner->addSuite('窗口管理', $this->getWindowManagementTests());
        $runner->addSuite('菜单管理', $this->getMenuManagementTests());
        $runner->addSuite('通知管理', $this->getNotificationManagementTests());
        $runner->addSuite('文件系统管理', $this->getFileSystemManagementTests());

        // 运行测试
        $results = $runner->run();

        // 显示测试结果
        $this->displayResults($output, $results);

        // 生成测试报告
        $reportPath = runtime_path() . 'tests/functional_test_report.html';
        $runner->generateReport('html', $reportPath);
        $output->writeln('测试报告已生成：' . $reportPath);

        return 0;
    }

    /**
     * 获取窗口管理测试
     *
     * @return array
     */
    protected function getWindowManagementTests()
    {
        return [
            '创建和关闭窗口' => function ($app) {
                // 创建窗口
                $windowId = \Native\ThinkPHP\Facades\Window::open('/test', [
                    'title' => '测试窗口',
                    'width' => 800,
                    'height' => 600,
                ]);

                // 断言窗口存在
                \Native\ThinkPHP\Facades\TestHelper::assertWindowExists($windowId);

                // 等待 1 秒
                sleep(1);

                // 关闭窗口
                \Native\ThinkPHP\Facades\Window::close($windowId);

                // 等待窗口关闭
                \Native\ThinkPHP\Facades\TestHelper::waitForWindowClose($windowId);
            },
            '最大化和最小化窗口' => function ($app) {
                // 创建窗口
                $windowId = \Native\ThinkPHP\Facades\Window::open('/test', [
                    'title' => '测试窗口',
                    'width' => 800,
                    'height' => 600,
                ]);

                // 断言窗口存在
                \Native\ThinkPHP\Facades\TestHelper::assertWindowExists($windowId);

                // 最大化窗口
                \Native\ThinkPHP\Facades\Window::maximize($windowId);

                // 等待 1 秒
                sleep(1);

                // 最小化窗口
                \Native\ThinkPHP\Facades\Window::minimize($windowId);

                // 等待 1 秒
                sleep(1);

                // 恢复窗口
                \Native\ThinkPHP\Facades\Window::restore($windowId);

                // 等待 1 秒
                sleep(1);

                // 关闭窗口
                \Native\ThinkPHP\Facades\Window::close($windowId);

                // 等待窗口关闭
                \Native\ThinkPHP\Facades\TestHelper::waitForWindowClose($windowId);
            },
            '设置窗口大小和位置' => function ($app) {
                // 创建窗口
                $windowId = \Native\ThinkPHP\Facades\Window::open('/test', [
                    'title' => '测试窗口',
                    'width' => 800,
                    'height' => 600,
                ]);

                // 断言窗口存在
                \Native\ThinkPHP\Facades\TestHelper::assertWindowExists($windowId);

                // 设置窗口大小
                \Native\ThinkPHP\Facades\Window::setSize($windowId, 1000, 800);

                // 等待 1 秒
                sleep(1);

                // 设置窗口位置
                \Native\ThinkPHP\Facades\Window::setPosition($windowId, 100, 100);

                // 等待 1 秒
                sleep(1);

                // 关闭窗口
                \Native\ThinkPHP\Facades\Window::close($windowId);

                // 等待窗口关闭
                \Native\ThinkPHP\Facades\TestHelper::waitForWindowClose($windowId);
            },
        ];
    }

    /**
     * 获取菜单管理测试
     *
     * @return array
     */
    protected function getMenuManagementTests()
    {
        return [
            '创建应用菜单' => function ($app) {
                // 创建应用菜单
                $menu = \Native\ThinkPHP\Facades\Menu::create()
                    ->submenu('文件', function ($menu) {
                        $menu->add('新建', function () {
                            // 处理新建操作
                        })
                            ->separator()
                            ->add('退出', function () {
                                // 退出应用
                            });
                    })
                    ->submenu('编辑', function ($menu) {
                        $menu->add('复制', function () {
                            // 处理复制操作
                        })
                            ->add('粘贴', function () {
                                // 处理粘贴操作
                            });
                    })
                    ->setApplicationMenu();

                // 断言菜单存在
                \Native\ThinkPHP\Facades\TestHelper::assertNotNull($menu);

                // 等待 1 秒
                sleep(1);
            },
            '创建上下文菜单' => function ($app) {
                // 创建上下文菜单
                $menu = \Native\ThinkPHP\Facades\Menu::create()
                    ->add('复制', function () {
                        // 处理复制操作
                    })
                    ->add('粘贴', function () {
                        // 处理粘贴操作
                    })
                    ->separator()
                    ->add('删除', function () {
                        // 处理删除操作
                    });

                // 断言菜单存在
                \Native\ThinkPHP\Facades\TestHelper::assertNotNull($menu);

                // 等待 1 秒
                sleep(1);
            },
        ];
    }

    /**
     * 获取通知管理测试
     *
     * @return array
     */
    protected function getNotificationManagementTests()
    {
        return [
            '发送通知' => function ($app) {
                // 发送通知
                $result = \Native\ThinkPHP\Facades\Notification::send('测试标题', '测试内容');

                // 断言通知发送成功
                \Native\ThinkPHP\Facades\TestHelper::assertTrue($result);

                // 等待 1 秒
                sleep(1);
            },
            '发送带图标的通知' => function ($app) {
                // 发送带图标的通知
                $result = \Native\ThinkPHP\Facades\Notification::sendWithIcon('测试标题', '测试内容', public_path() . 'static/images/icon.png');

                // 断言通知发送成功
                \Native\ThinkPHP\Facades\TestHelper::assertTrue($result);

                // 等待 1 秒
                sleep(1);
            },
            '使用链式调用发送通知' => function ($app) {
                // 使用链式调用发送通知
                $result = \Native\ThinkPHP\Facades\Notification::title('测试标题')
                    ->body('测试内容')
                    ->icon(public_path() . 'static/images/icon.png')
                    ->sound('default')
                    ->show();

                // 断言通知发送成功
                \Native\ThinkPHP\Facades\TestHelper::assertTrue($result);

                // 等待 1 秒
                sleep(1);
            },
        ];
    }

    /**
     * 获取文件系统管理测试
     *
     * @return array
     */
    protected function getFileSystemManagementTests()
    {
        return [
            '创建和删除文件' => function ($app) {
                $path = runtime_path() . 'tests/test.txt';
                $content = 'Hello, World!';

                // 写入文件
                \Native\ThinkPHP\Facades\FileSystem::write($path, $content);

                // 检查文件是否存在
                \Native\ThinkPHP\Facades\TestHelper::assertFileExists($path);

                // 读取文件
                $readContent = \Native\ThinkPHP\Facades\FileSystem::read($path);

                // 检查内容是否一致
                \Native\ThinkPHP\Facades\TestHelper::assertEquals($content, $readContent);

                // 删除文件
                \Native\ThinkPHP\Facades\FileSystem::delete($path);

                // 检查文件是否已删除
                \Native\ThinkPHP\Facades\TestHelper::assertFileNotExists($path);
            },
            '创建和删除目录' => function ($app) {
                $path = runtime_path() . 'tests/test_dir';

                // 创建目录
                \Native\ThinkPHP\Facades\FileSystem::makeDirectory($path);

                // 检查目录是否存在
                \Native\ThinkPHP\Facades\TestHelper::assertDirectoryExists($path);

                // 删除目录
                \Native\ThinkPHP\Facades\FileSystem::deleteDirectory($path);

                // 检查目录是否已删除
                \Native\ThinkPHP\Facades\TestHelper::assertDirectoryNotExists($path);
            },
            '复制和移动文件' => function ($app) {
                $sourcePath = runtime_path() . 'tests/source.txt';
                $destinationPath = runtime_path() . 'tests/destination.txt';
                $movedPath = runtime_path() . 'tests/moved.txt';
                $content = 'Hello, World!';

                // 写入源文件
                \Native\ThinkPHP\Facades\FileSystem::write($sourcePath, $content);

                // 复制文件
                \Native\ThinkPHP\Facades\FileSystem::copy($sourcePath, $destinationPath);

                // 检查目标文件是否存在
                \Native\ThinkPHP\Facades\TestHelper::assertFileExists($destinationPath);

                // 读取目标文件
                $readContent = \Native\ThinkPHP\Facades\FileSystem::read($destinationPath);

                // 检查内容是否一致
                \Native\ThinkPHP\Facades\TestHelper::assertEquals($content, $readContent);

                // 移动文件
                \Native\ThinkPHP\Facades\FileSystem::move($destinationPath, $movedPath);

                // 检查源文件是否已移动
                \Native\ThinkPHP\Facades\TestHelper::assertFileNotExists($destinationPath);

                // 检查目标文件是否存在
                \Native\ThinkPHP\Facades\TestHelper::assertFileExists($movedPath);

                // 删除文件
                \Native\ThinkPHP\Facades\FileSystem::delete($sourcePath);
                \Native\ThinkPHP\Facades\FileSystem::delete($movedPath);
            },
        ];
    }

    /**
     * 显示测试结果
     *
     * @param Output $output
     * @param array $results
     * @return void
     */
    protected function displayResults(Output $output, array $results)
    {
        $totalTests = 0;
        $totalPassed = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach ($results as $suite => $result) {
            $totalTests += $result['total'];
            $totalPassed += $result['passed'];
            $totalFailed += $result['failed'];
            $totalSkipped += $result['skipped'];

            $output->writeln('');
            $output->writeln("<info>套件：{$suite}</info>");
            $output->writeln("总共：{$result['total']}，通过：{$result['passed']}，失败：{$result['failed']}，跳过：{$result['skipped']}");

            foreach ($result['tests'] as $name => $test) {
                $status = $test['status'] === 'passed' ? '<info>通过</info>' : '<error>失败</error>';
                $output->writeln("  {$name}: {$status}");

                if ($test['status'] === 'failed') {
                    $output->writeln("    <error>{$test['message']}</error>");
                }
            }
        }

        $output->writeln('');
        $output->writeln("<info>总结：总共 {$totalTests} 个测试，通过 {$totalPassed} 个，失败 {$totalFailed} 个，跳过 {$totalSkipped} 个。</info>");
    }
}