<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Testing\TestRunner addSuite(string $name, array $tests) 添加测试套件
 * @method static array run(?string $suite = null) 运行测试
 * @method static array getResults() 获取测试结果
 * @method static string|bool generateReport(string $format = 'html', ?string $outputFile = null) 生成测试报告
 *
 * @see \Native\ThinkPHP\Testing\TestRunner
 */
class TestRunner extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.test_runner';
    }
}