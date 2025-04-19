<?php

namespace Native\ThinkPHP\Tests\Unit\Utils;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\Utils\Cache;

class CacheTest extends TestCase
{
    /**
     * 缓存目录
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * 设置测试环境
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDir = __DIR__ . '/../../runtime/cache/test';

        // 确保缓存目录存在
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        // 清空缓存目录
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * 测试创建缓存实例
     *
     * @return void
     */
    public function testCreateCache()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $this->assertInstanceOf(Cache::class, $cache);
        $this->assertEquals($this->cacheDir, $cache->getCacheDir());
    }

    /**
     * 测试设置和获取缓存
     *
     * @return void
     */
    public function testSetAndGetCache()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $key = 'test_key';
        $value = 'test_value';

        $result = $cache->set($key, $value);

        $this->assertTrue($result);
        $this->assertTrue($cache->has($key));
        $this->assertEquals($value, $cache->get($key));
    }

    /**
     * 测试设置带过期时间的缓存
     *
     * @return void
     */
    public function testSetCacheWithTtl()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $key = 'test_key_ttl';
        $value = 'test_value';

        $result = $cache->set($key, $value, 1); // 1 秒过期

        $this->assertTrue($result);
        $this->assertTrue($cache->has($key));
        $this->assertEquals($value, $cache->get($key));

        // 等待缓存过期
        sleep(2);

        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));
    }

    /**
     * 测试删除缓存
     *
     * @return void
     */
    public function testDeleteCache()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $key = 'test_key_delete';
        $value = 'test_value';

        $cache->set($key, $value);

        $this->assertTrue($cache->has($key));

        $result = $cache->delete($key);

        $this->assertTrue($result);
        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));
    }

    /**
     * 测试清空所有缓存
     *
     * @return void
     */
    public function testClearCache()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $this->assertTrue($cache->has('key1'));
        $this->assertTrue($cache->has('key2'));

        $result = $cache->clear();

        $this->assertTrue($result);
        $this->assertFalse($cache->has('key1'));
        $this->assertFalse($cache->has('key2'));
    }

    /**
     * 测试获取缓存信息
     *
     * @return void
     */
    public function testGetCacheInfo()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $key = 'test_key_info';
        $value = 'test_value';
        $ttl = 3600;

        $cache->set($key, $value, $ttl);

        $info = $cache->getInfo($key);

        $this->assertIsArray($info);
        $this->assertEquals($key, $info['key']);
        $this->assertEquals($ttl, $info['ttl']);
        $this->assertGreaterThan(0, $info['size']);
    }

    /**
     * 测试获取所有缓存信息
     *
     * @return void
     */
    public function testGetAllCacheInfo()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $info = $cache->getAllInfo();

        $this->assertIsArray($info);
        // 清空缓存目录中的所有文件
        $files = glob($this->cacheDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // 重新设置缓存
        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $info = $cache->getAllInfo();

        $this->assertIsArray($info);
        $this->assertCount(2, $info);

        // 验证缓存项的键
        $keys = array_column($info, 'key');
        $this->assertContains('key1', $keys);
        $this->assertContains('key2', $keys);
    }

    /**
     * 测试获取缓存总大小
     *
     * @return void
     */
    public function testGetCacheSize()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $size = $cache->getSize();

        $this->assertGreaterThan(0, $size);
    }

    /**
     * 测试清理过期缓存
     *
     * @return void
     */
    public function testGarbageCollection()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $cache->set('key1', 'value1', 1); // 1 秒过期
        $cache->set('key2', 'value2'); // 永不过期

        // 等待缓存过期
        sleep(2);

        $count = $cache->gc();

        $this->assertEquals(1, $count);
        $this->assertFalse($cache->has('key1'));
        $this->assertTrue($cache->has('key2'));
    }

    /**
     * 测试记住缓存
     *
     * @return void
     */
    public function testRememberCache()
    {
        $cache = new Cache($this->app, $this->cacheDir);

        $key = 'test_key_remember';
        $value = 'test_value';

        // 第一次调用，应该执行回调函数
        $result = $cache->remember($key, function () use ($value) {
            return $value;
        }, 3600);

        $this->assertEquals($value, $result);
        $this->assertTrue($cache->has($key));

        // 第二次调用，应该直接返回缓存值，不执行回调函数
        $result = $cache->remember($key, function () {
            return 'new_value';
        }, 3600);

        $this->assertEquals($value, $result);
    }

    /**
     * 清理测试环境
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // 清空缓存目录
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }

        // 删除缓存目录
        if (is_dir($this->cacheDir)) {
            rmdir($this->cacheDir);
        }

        parent::tearDown();
    }
}
