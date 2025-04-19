<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use Native\ThinkPHP\Facades\DeveloperTools;
use Native\ThinkPHP\Facades\Notification;

class DeveloperToolsController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('developer_tools/index');
    }
    
    /**
     * 切换开发者工具状态
     *
     * @return \think\Response
     */
    public function toggle()
    {
        // 切换开发者工具状态
        $isEnabled = DeveloperTools::toggle();
        
        // 发送通知
        $status = $isEnabled ? '已启用' : '已禁用';
        Notification::send('开发者工具', "开发者工具{$status}");
        
        return json(['success' => true, 'enabled' => $isEnabled]);
    }
    
    /**
     * 切换开发者工具面板
     *
     * @return \think\Response
     */
    public function toggleDevTools()
    {
        // 切换开发者工具面板
        $isOpened = DeveloperTools::toggleDevTools();
        
        return json(['success' => true, 'opened' => $isOpened]);
    }
    
    /**
     * 打印调试信息
     *
     * @return \think\Response
     */
    public function log()
    {
        $type = Request::param('type', 'log');
        $message = Request::param('message', '这是一条调试信息');
        $data = Request::param('data', null);
        
        // 解析数据
        if ($data && is_string($data)) {
            try {
                $data = json_decode($data, true);
            } catch (\Exception $e) {
                // 忽略解析错误
            }
        }
        
        // 根据类型打印调试信息
        switch ($type) {
            case 'info':
                DeveloperTools::info($message, $data);
                break;
                
            case 'warn':
                DeveloperTools::warn($message, $data);
                break;
                
            case 'error':
                DeveloperTools::error($message, $data);
                break;
                
            default:
                DeveloperTools::log($message, $data);
                break;
        }
        
        return json(['success' => true, 'type' => $type, 'message' => $message]);
    }
    
    /**
     * 分组打印
     *
     * @return \think\Response
     */
    public function group()
    {
        $groupName = Request::param('name', '调试分组');
        
        // 开始分组
        DeveloperTools::group($groupName);
        
        // 打印分组内的信息
        DeveloperTools::log('分组开始时间', date('Y-m-d H:i:s'));
        DeveloperTools::log('请求参数', Request::param());
        DeveloperTools::log('请求头', Request::header());
        
        // 结束分组
        DeveloperTools::groupEnd();
        
        return json(['success' => true, 'group' => $groupName]);
    }
    
    /**
     * 计时功能
     *
     * @return \think\Response
     */
    public function time()
    {
        $label = Request::param('label', '操作耗时');
        $duration = Request::param('duration', 2);
        
        // 开始计时
        DeveloperTools::time($label);
        
        // 模拟耗时操作
        sleep($duration);
        
        // 结束计时
        DeveloperTools::timeEnd($label);
        
        return json(['success' => true, 'label' => $label, 'duration' => $duration]);
    }
    
    /**
     * 性能分析
     *
     * @return \think\Response
     */
    public function profile()
    {
        $label = Request::param('label', '性能分析');
        $iterations = Request::param('iterations', 1000);
        
        // 开始性能分析
        DeveloperTools::startProfiling($label);
        
        // 执行一些操作
        $result = [];
        for ($i = 0; $i < $iterations; $i++) {
            $result[] = md5(uniqid($i, true));
        }
        
        // 结束性能分析
        DeveloperTools::stopProfiling($label);
        
        return json(['success' => true, 'label' => $label, 'iterations' => $iterations]);
    }
    
    /**
     * 内存分析
     *
     * @return \think\Response
     */
    public function memory()
    {
        $label = Request::param('label', '内存分析');
        $size = Request::param('size', 10000);
        
        // 获取当前内存使用情况
        $initialMemory = DeveloperTools::memory();
        DeveloperTools::log('初始内存使用', $initialMemory);
        
        // 开始监控内存使用
        DeveloperTools::startMemoryMonitor($label);
        
        // 执行一些内存密集型操作
        $data = [];
        for ($i = 0; $i < $size; $i++) {
            $data[] = str_repeat('x', 100);
        }
        
        // 结束监控并获取内存使用情况
        $memoryUsage = DeveloperTools::stopMemoryMonitor($label);
        
        // 获取内存使用峰值
        $peakMemory = DeveloperTools::memoryPeak();
        DeveloperTools::log('内存使用峰值', $peakMemory);
        
        // 清理内存
        $data = null;
        gc_collect_cycles();
        
        // 获取最终内存使用情况
        $finalMemory = DeveloperTools::memory();
        DeveloperTools::log('最终内存使用', $finalMemory);
        
        return json([
            'success' => true,
            'label' => $label,
            'size' => $size,
            'initial_memory' => $initialMemory,
            'memory_usage' => $memoryUsage,
            'peak_memory' => $peakMemory,
            'final_memory' => $finalMemory,
        ]);
    }
    
    /**
     * 数据库查询分析
     *
     * @return \think\Response
     */
    public function database()
    {
        // 创建测试表
        $this->createTestTable();
        
        // 插入测试数据
        $this->insertTestData();
        
        // 分析简单查询
        DeveloperTools::time('简单查询');
        $users = Db::table('test_users')->where('status', 1)->select();
        DeveloperTools::timeEnd('简单查询');
        DeveloperTools::log('简单查询结果数量', count($users));
        
        // 分析复杂查询
        DeveloperTools::time('复杂查询');
        $result = Db::table('test_users')
            ->alias('u')
            ->join('test_orders o', 'u.id = o.user_id')
            ->where('u.status', 1)
            ->field('u.id, u.name, COUNT(o.id) as order_count, SUM(o.total) as total_amount')
            ->group('u.id')
            ->having('order_count > 0')
            ->order('total_amount DESC')
            ->limit(10)
            ->select();
        DeveloperTools::timeEnd('复杂查询');
        DeveloperTools::log('复杂查询结果', $result);
        
        return json([
            'success' => true,
            'simple_query_count' => count($users),
            'complex_query_count' => count($result),
        ]);
    }
    
    /**
     * 创建测试表
     *
     * @return void
     */
    protected function createTestTable()
    {
        // 检查测试表是否存在
        $tables = Db::query('SHOW TABLES LIKE "test_users"');
        if (empty($tables)) {
            // 创建用户表
            Db::execute('CREATE TABLE test_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                status TINYINT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )');
        }
        
        // 检查订单表是否存在
        $tables = Db::query('SHOW TABLES LIKE "test_orders"');
        if (empty($tables)) {
            // 创建订单表
            Db::execute('CREATE TABLE test_orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                total DECIMAL(10, 2) NOT NULL,
                status VARCHAR(20) DEFAULT "pending",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES test_users(id)
            )');
        }
    }
    
    /**
     * 插入测试数据
     *
     * @return void
     */
    protected function insertTestData()
    {
        // 检查用户表是否有数据
        $count = Db::table('test_users')->count();
        if ($count === 0) {
            // 插入用户数据
            $users = [];
            for ($i = 1; $i <= 100; $i++) {
                $users[] = [
                    'name' => "User {$i}",
                    'email' => "user{$i}@example.com",
                    'status' => rand(0, 1),
                ];
            }
            Db::table('test_users')->insertAll($users);
        }
        
        // 检查订单表是否有数据
        $count = Db::table('test_orders')->count();
        if ($count === 0) {
            // 插入订单数据
            $orders = [];
            for ($i = 1; $i <= 500; $i++) {
                $orders[] = [
                    'user_id' => rand(1, 100),
                    'total' => rand(100, 10000) / 100,
                    'status' => ['pending', 'completed', 'cancelled'][rand(0, 2)],
                ];
            }
            Db::table('test_orders')->insertAll($orders);
        }
    }
    
    /**
     * 清理测试数据
     *
     * @return \think\Response
     */
    public function cleanup()
    {
        // 删除测试表
        Db::execute('DROP TABLE IF EXISTS test_orders');
        Db::execute('DROP TABLE IF EXISTS test_users');
        
        return json(['success' => true, 'message' => '测试数据已清理']);
    }
}
