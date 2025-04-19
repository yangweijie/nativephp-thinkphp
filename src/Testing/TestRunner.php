<?php

namespace Native\ThinkPHP\Testing;

use think\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;

class TestRunner
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 测试套件
     *
     * @var array
     */
    protected $suites = [];

    /**
     * 测试结果
     *
     * @var array
     */
    protected $results = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 添加测试套件
     *
     * @param string $name 套件名称
     * @param array $tests 测试列表
     * @return $this
     */
    public function addSuite(string $name, array $tests): self
    {
        $this->suites[$name] = $tests;
        return $this;
    }

    /**
     * 运行测试
     *
     * @param string|null $suite 套件名称，如果为 null 则运行所有套件
     * @return array 测试结果
     */
    public function run(?string $suite = null): array
    {
        // 清空测试结果
        $this->results = [];

        // 如果指定了套件，则只运行该套件
        if ($suite !== null) {
            if (!isset($this->suites[$suite])) {
                throw new \InvalidArgumentException("测试套件不存在：{$suite}");
            }

            $this->runSuite($suite, $this->suites[$suite]);
        }
        // 否则运行所有套件
        else {
            foreach ($this->suites as $name => $tests) {
                $this->runSuite($name, $tests);
            }
        }

        return $this->results;
    }

    /**
     * 运行测试套件
     *
     * @param string $name 套件名称
     * @param array $tests 测试列表
     * @return void
     */
    protected function runSuite(string $name, array $tests): void
    {
        // 初始化套件结果
        $this->results[$name] = [
            'total' => count($tests),
            'passed' => 0,
            'failed' => 0,
            'skipped' => 0,
            'time' => 0,
            'tests' => [],
        ];

        // 运行每个测试
        foreach ($tests as $testName => $test) {
            $this->runTest($name, $testName, $test);
        }
    }

    /**
     * 运行测试
     *
     * @param string $suite 套件名称
     * @param string $name 测试名称
     * @param callable $test 测试函数
     * @return void
     */
    protected function runTest(string $suite, string $name, callable $test): void
    {
        // 初始化测试结果
        $result = [
            'name' => $name,
            'status' => 'passed',
            'message' => '',
            'time' => 0,
        ];

        // 记录开始时间
        $startTime = microtime(true);

        try {
            // 运行测试
            $test($this->app);

            // 记录结束时间
            $endTime = microtime(true);
            $result['time'] = $endTime - $startTime;

            // 更新套件结果
            $this->results[$suite]['passed']++;
        } catch (\Exception $e) {
            // 记录结束时间
            $endTime = microtime(true);
            $result['time'] = $endTime - $startTime;

            // 更新测试结果
            $result['status'] = 'failed';
            $result['message'] = $e->getMessage();

            // 更新套件结果
            $this->results[$suite]['failed']++;
        }

        // 添加测试结果
        $this->results[$suite]['tests'][$name] = $result;

        // 更新套件时间
        $this->results[$suite]['time'] += $result['time'];
    }

    /**
     * 获取测试结果
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * 生成测试报告
     *
     * @param string $format 报告格式（html、json、xml）
     * @param string|null $outputFile 输出文件路径，如果为 null 则返回报告内容
     * @return string|bool 如果 $outputFile 为 null，则返回报告内容；否则返回是否成功
     */
    public function generateReport(string $format = 'html', ?string $outputFile = null)
    {
        // 生成报告内容
        $content = '';

        switch ($format) {
            case 'html':
                $content = $this->generateHtmlReport();
                break;
            case 'json':
                $content = json_encode($this->results, JSON_PRETTY_PRINT);
                break;
            case 'xml':
                $content = $this->generateXmlReport();
                break;
            default:
                throw new \InvalidArgumentException("不支持的报告格式：{$format}");
        }

        // 如果没有指定输出文件，则返回报告内容
        if ($outputFile === null) {
            return $content;
        }

        // 保存报告到文件
        return FileSystem::write($outputFile, $content);
    }

    /**
     * 生成 HTML 报告
     *
     * @return string
     */
    protected function generateHtmlReport(): string
    {
        // 生成 HTML 报告
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>测试报告</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            margin-top: 0;
        }
        .summary {
            margin-bottom: 20px;
        }
        .suite {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .suite-header {
            background-color: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .suite-body {
            padding: 10px;
        }
        .test {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .test-passed {
            background-color: #dff0d8;
        }
        .test-failed {
            background-color: #f2dede;
        }
        .test-skipped {
            background-color: #fcf8e3;
        }
    </style>
</head>
<body>
    <h1>测试报告</h1>
    <div class="summary">
        <p>总共运行了 ' . $this->getTotalTests() . ' 个测试，通过 ' . $this->getTotalPassed() . ' 个，失败 ' . $this->getTotalFailed() . ' 个，跳过 ' . $this->getTotalSkipped() . ' 个。</p>
    </div>';

        // 添加每个套件的结果
        foreach ($this->results as $suite => $result) {
            $html .= '
    <div class="suite">
        <div class="suite-header">
            <h2>' . $suite . '</h2>
            <p>总共运行了 ' . $result['total'] . ' 个测试，通过 ' . $result['passed'] . ' 个，失败 ' . $result['failed'] . ' 个，跳过 ' . $result['skipped'] . ' 个。耗时 ' . round($result['time'], 2) . ' 秒。</p>
        </div>
        <div class="suite-body">';

            // 添加每个测试的结果
            foreach ($result['tests'] as $test) {
                $statusClass = 'test-' . $test['status'];
                $html .= '
            <div class="test ' . $statusClass . '">
                <h3>' . $test['name'] . '</h3>
                <p>状态：' . $test['status'] . '</p>
                <p>耗时：' . round($test['time'], 2) . ' 秒</p>';

                if ($test['status'] === 'failed') {
                    $html .= '
                <p>错误信息：' . $test['message'] . '</p>';
                }

                $html .= '
            </div>';
            }

            $html .= '
        </div>
    </div>';
        }

        $html .= '
</body>
</html>';

        return $html;
    }

    /**
     * 生成 XML 报告
     *
     * @return string
     */
    protected function generateXmlReport(): string
    {
        // 创建 XML 文档
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><testsuites></testsuites>');

        // 添加每个套件的结果
        foreach ($this->results as $suite => $result) {
            $suiteElement = $xml->addChild('testsuite');
            $suiteElement->addAttribute('name', $suite);
            $suiteElement->addAttribute('tests', $result['total']);
            $suiteElement->addAttribute('failures', $result['failed']);
            $suiteElement->addAttribute('skipped', $result['skipped']);
            $suiteElement->addAttribute('time', (string)round($result['time'], 2));

            // 添加每个测试的结果
            foreach ($result['tests'] as $test) {
                $testElement = $suiteElement->addChild('testcase');
                $testElement->addAttribute('name', $test['name']);
                $testElement->addAttribute('time', (string)round($test['time'], 2));

                if ($test['status'] === 'failed') {
                    $failureElement = $testElement->addChild('failure');
                    $failureElement->addAttribute('message', $test['message']);
                } elseif ($test['status'] === 'skipped') {
                    $testElement->addChild('skipped');
                }
            }
        }

        // 返回 XML 字符串
        return $xml->asXML();
    }

    /**
     * 获取总测试数
     *
     * @return int
     */
    protected function getTotalTests(): int
    {
        $total = 0;
        foreach ($this->results as $result) {
            $total += $result['total'];
        }
        return $total;
    }

    /**
     * 获取总通过数
     *
     * @return int
     */
    protected function getTotalPassed(): int
    {
        $passed = 0;
        foreach ($this->results as $result) {
            $passed += $result['passed'];
        }
        return $passed;
    }

    /**
     * 获取总失败数
     *
     * @return int
     */
    protected function getTotalFailed(): int
    {
        $failed = 0;
        foreach ($this->results as $result) {
            $failed += $result['failed'];
        }
        return $failed;
    }

    /**
     * 获取总跳过数
     *
     * @return int
     */
    protected function getTotalSkipped(): int
    {
        $skipped = 0;
        foreach ($this->results as $result) {
            $skipped += $result['skipped'];
        }
        return $skipped;
    }
}