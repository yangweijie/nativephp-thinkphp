<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string|false encrypt(mixed $data, string|null $key = null) 加密数据
 * @method static mixed|false decrypt(string $data, string|null $key = null) 解密数据
 * @method static bool store(string $key, mixed $value) 安全存储数据
 * @method static mixed retrieve(string $key, mixed $default = null) 获取安全存储的数据
 * @method static bool forget(string $key) 删除安全存储的数据
 * @method static bool has(string $key) 检查安全存储的数据是否存在
 * @method static string random(int $length = 16) 生成随机字符串
 * @method static string uuid() 生成 UUID
 * @method static string hash(string $data, string $algo = 'sha256') 生成哈希值
 * @method static bool verifyHash(string $data, string $hash, string $algo = 'sha256') 验证哈希值
 * @method static string hashPassword(string $password) 生成密码哈希
 * @method static bool verifyPassword(string $password, string $hash) 验证密码哈希
 *
 * @see \Native\ThinkPHP\Security
 */
class Security extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.security';
    }
}
