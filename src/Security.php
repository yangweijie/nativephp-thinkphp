<?php

namespace Native\ThinkPHP;

use think\App;
use think\facade\Config;

class Security
{
    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 安全配置
     *
     * @var array
     */
    protected $config;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = Config::get('native.security', []);
    }

    /**
     * 加密数据
     *
     * @param mixed $data
     * @param string|null $key
     * @return string|false
     */
    public function encrypt($data, ?string $key = null)
    {
        // 获取加密密钥
        $key = $key ?? $this->getEncryptionKey();

        // 如果没有密钥，返回 false
        if (empty($key)) {
            return false;
        }

        // 序列化数据
        $data = serialize($data);

        // 生成初始化向量
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // 加密数据
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

        // 如果加密失败，返回 false
        if ($encrypted === false) {
            return false;
        }

        // 组合加密数据和初始化向量
        $result = base64_encode($encrypted . '::' . $iv);

        return $result;
    }

    /**
     * 解密数据
     *
     * @param string $data
     * @param string|null $key
     * @return mixed|false
     */
    public function decrypt(string $data, ?string $key = null)
    {
        // 获取加密密钥
        $key = $key ?? $this->getEncryptionKey();

        // 如果没有密钥，返回 false
        if (empty($key)) {
            return false;
        }

        // 解码数据
        $data = base64_decode($data);

        // 分离加密数据和初始化向量
        $parts = explode('::', $data, 2);
        if (count($parts) !== 2) {
            return false;
        }

        $encrypted = $parts[0];
        $iv = $parts[1];

        // 解密数据
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);

        // 如果解密失败，返回 false
        if ($decrypted === false) {
            return false;
        }

        // 反序列化数据
        $data = @unserialize($decrypted);

        // 如果反序列化失败，返回 false
        if ($data === false && $decrypted !== 'b:0;') {
            return false;
        }

        return $data;
    }

    /**
     * 安全存储数据
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function store(string $key, $value): bool
    {
        // 获取存储路径
        $path = $this->getStoragePath($key);

        // 加密数据
        $encrypted = $this->encrypt($value);

        // 如果加密失败，返回 false
        if ($encrypted === false) {
            return false;
        }

        // 确保目录存在
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // 存储数据
        return file_put_contents($path, $encrypted) !== false;
    }

    /**
     * 获取安全存储的数据
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function retrieve(string $key, $default = null)
    {
        // 获取存储路径
        $path = $this->getStoragePath($key);

        // 如果文件不存在，返回默认值
        if (!file_exists($path)) {
            return $default;
        }

        // 读取数据
        $data = file_get_contents($path);

        // 解密数据
        $decrypted = $this->decrypt($data);

        // 如果解密失败，返回默认值
        if ($decrypted === false) {
            return $default;
        }

        return $decrypted;
    }

    /**
     * 删除安全存储的数据
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        // 获取存储路径
        $path = $this->getStoragePath($key);

        // 如果文件不存在，返回 true
        if (!file_exists($path)) {
            return true;
        }

        // 删除文件
        return unlink($path);
    }

    /**
     * 检查安全存储的数据是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        // 获取存储路径
        $path = $this->getStoragePath($key);

        // 检查文件是否存在
        return file_exists($path);
    }

    /**
     * 生成随机字符串
     *
     * @param int $length
     * @return string
     */
    public function random(int $length = 16): string
    {
        // 生成随机字节
        $bytes = random_bytes($length);

        // 转换为十六进制字符串
        return bin2hex($bytes);
    }

    /**
     * 生成 UUID
     *
     * @return string
     */
    public function uuid(): string
    {
        // 生成随机字节
        $bytes = random_bytes(16);

        // 设置版本和变体
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

        // 转换为 UUID 格式
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /**
     * 生成哈希值
     *
     * @param string $data
     * @param string $algo
     * @return string
     */
    public function hash(string $data, string $algo = 'sha256'): string
    {
        return hash($algo, $data);
    }

    /**
     * 验证哈希值
     *
     * @param string $data
     * @param string $hash
     * @param string $algo
     * @return bool
     */
    public function verifyHash(string $data, string $hash, string $algo = 'sha256'): bool
    {
        return hash_equals($hash, $this->hash($data, $algo));
    }

    /**
     * 生成密码哈希
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 验证密码哈希
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 获取加密密钥
     *
     * @return string|null
     */
    protected function getEncryptionKey(): ?string
    {
        // 从配置中获取密钥
        $key = $this->config['encryption_key'] ?? null;

        // 如果配置中没有密钥，尝试从环境变量中获取
        if (empty($key)) {
            $key = env('NATIVEPHP_ENCRYPTION_KEY');
        }

        // 如果环境变量中没有密钥，尝试从应用密钥中获取
        if (empty($key)) {
            $key = Config::get('app.key');
        }

        // 如果应用密钥中没有密钥，返回 null
        if (empty($key)) {
            return null;
        }

        // 如果密钥是 base64 编码的，解码它
        if (strpos($key, 'base64:') === 0) {
            $key = base64_decode(substr($key, 7));
        }

        return $key;
    }

    /**
     * 获取存储路径
     *
     * @param string $key
     * @return string
     */
    protected function getStoragePath(string $key): string
    {
        // 获取存储目录
        $directory = $this->config['storage_path'] ?? $this->app->getRuntimePath() . 'security';

        // 确保目录存在
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // 生成文件名
        $filename = $this->hash($key) . '.dat';

        return $directory . '/' . $filename;
    }
}
