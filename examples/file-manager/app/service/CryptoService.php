<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;

class CryptoService
{
    /**
     * 支持的加密算法
     *
     * @var array
     */
    protected $supportedAlgorithms = [
        'aes-256-cbc' => '高级加密标准 (AES-256-CBC)',
        'aes-256-ctr' => '高级加密标准 (AES-256-CTR)',
        'aes-256-gcm' => '高级加密标准 (AES-256-GCM)',
        'chacha20' => 'ChaCha20',
        'chacha20-poly1305' => 'ChaCha20-Poly1305',
    ];

    /**
     * 获取支持的加密算法
     *
     * @return array
     */
    public function getSupportedAlgorithms()
    {
        $available = [];
        foreach ($this->supportedAlgorithms as $algo => $name) {
            if (in_array($algo, openssl_get_cipher_methods())) {
                $available[$algo] = $name;
            }
        }
        return $available;
    }

    /**
     * 加密文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @param string $password 密码
     * @param string $algorithm 加密算法
     * @return bool
     */
    public function encryptFile($source, $destination, $password, $algorithm = 'aes-256-cbc')
    {
        if (!file_exists($source)) {
            throw new \Exception('源文件不存在');
        }

        if (!in_array($algorithm, openssl_get_cipher_methods())) {
            throw new \Exception('不支持的加密算法: ' . $algorithm);
        }

        // 读取源文件内容
        $content = FileSystem::read($source);
        if ($content === false) {
            throw new \Exception('无法读取源文件');
        }

        // 生成随机初始化向量
        $ivlen = openssl_cipher_iv_length($algorithm);
        $iv = openssl_random_pseudo_bytes($ivlen);

        // 生成随机盐值
        $salt = openssl_random_pseudo_bytes(32);

        // 使用 PBKDF2 派生密钥
        $key = $this->deriveKey($password, $salt);

        // 加密内容
        $encrypted = openssl_encrypt($content, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new \Exception('加密失败: ' . openssl_error_string());
        }

        // 计算 HMAC 以验证完整性
        $hmac = hash_hmac('sha256', $encrypted, $key, true);

        // 构建加密文件格式
        $meta = [
            'algorithm' => $algorithm,
            'iv' => base64_encode($iv),
            'salt' => base64_encode($salt),
            'hmac' => base64_encode($hmac),
            'version' => 1,
        ];

        $metaJson = json_encode($meta);
        $metaLength = pack('N', strlen($metaJson));

        // 写入目标文件
        $result = FileSystem::write($destination, "ENCRYPTED" . $metaLength . $metaJson . $encrypted);
        if ($result === false) {
            throw new \Exception('无法写入目标文件');
        }

        return true;
    }

    /**
     * 解密文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @param string $password 密码
     * @return bool
     */
    public function decryptFile($source, $destination, $password)
    {
        if (!file_exists($source)) {
            throw new \Exception('源文件不存在');
        }

        // 读取源文件内容
        $content = FileSystem::read($source);
        if ($content === false) {
            throw new \Exception('无法读取源文件');
        }

        // 检查文件格式
        if (substr($content, 0, 9) !== "ENCRYPTED") {
            throw new \Exception('文件不是有效的加密文件');
        }

        // 解析元数据
        $metaLength = unpack('N', substr($content, 9, 4))[1];
        $metaJson = substr($content, 13, $metaLength);
        $meta = json_decode($metaJson, true);

        if (!$meta || !isset($meta['algorithm']) || !isset($meta['iv']) || !isset($meta['salt']) || !isset($meta['hmac'])) {
            throw new \Exception('无效的加密文件格式');
        }

        // 获取加密数据
        $encrypted = substr($content, 13 + $metaLength);

        // 解码元数据
        $algorithm = $meta['algorithm'];
        $iv = base64_decode($meta['iv']);
        $salt = base64_decode($meta['salt']);
        $hmac = base64_decode($meta['hmac']);

        // 检查算法是否支持
        if (!in_array($algorithm, openssl_get_cipher_methods())) {
            throw new \Exception('不支持的加密算法: ' . $algorithm);
        }

        // 使用 PBKDF2 派生密钥
        $key = $this->deriveKey($password, $salt);

        // 验证 HMAC
        $calculatedHmac = hash_hmac('sha256', $encrypted, $key, true);
        if (!hash_equals($hmac, $calculatedHmac)) {
            throw new \Exception('密码错误或文件已损坏');
        }

        // 解密内容
        $decrypted = openssl_decrypt($encrypted, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            throw new \Exception('解密失败: ' . openssl_error_string());
        }

        // 写入目标文件
        $result = FileSystem::write($destination, $decrypted);
        if ($result === false) {
            throw new \Exception('无法写入目标文件');
        }

        return true;
    }

    /**
     * 检查文件是否为加密文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function isEncrypted($path)
    {
        if (!file_exists($path) || is_dir($path)) {
            return false;
        }

        // 检查文件大小
        $size = FileSystem::size($path);
        if ($size < 20) { // 至少需要 "ENCRYPTED" + 元数据长度 + 最小元数据
            return false;
        }

        // 读取文件头
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 9);
        fclose($handle);

        return $header === "ENCRYPTED";
    }

    /**
     * 获取加密文件信息
     *
     * @param string $path 文件路径
     * @return array|null
     */
    public function getEncryptedFileInfo($path)
    {
        if (!$this->isEncrypted($path)) {
            return null;
        }

        // 读取文件头和元数据
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return null;
        }

        // 跳过 "ENCRYPTED" 标记
        fseek($handle, 9);

        // 读取元数据长度
        $metaLengthBin = fread($handle, 4);
        $metaLength = unpack('N', $metaLengthBin)[1];

        // 读取元数据
        $metaJson = fread($handle, $metaLength);
        fclose($handle);

        $meta = json_decode($metaJson, true);
        if (!$meta) {
            return null;
        }

        // 添加文件信息
        $meta['file'] = [
            'path' => $path,
            'size' => FileSystem::size($path),
            'lastModified' => FileSystem::lastModified($path),
        ];

        return $meta;
    }

    /**
     * 使用 PBKDF2 派生密钥
     *
     * @param string $password 密码
     * @param string $salt 盐值
     * @return string 派生的密钥
     */
    protected function deriveKey($password, $salt)
    {
        return hash_pbkdf2('sha256', $password, $salt, 10000, 32, true);
    }

    /**
     * 生成随机密码
     *
     * @param int $length 密码长度
     * @return string 随机密码
     */
    public function generateRandomPassword($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_=+';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    /**
     * 评估密码强度
     *
     * @param string $password 密码
     * @return array 密码强度评估结果
     */
    public function evaluatePasswordStrength($password)
    {
        $length = strlen($password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasDigit = preg_match('/\d/', $password);
        $hasSpecial = preg_match('/[^a-zA-Z\d]/', $password);
        
        $score = 0;
        $score += $length >= 8 ? min(($length / 2), 10) : 0;
        $score += $hasLower ? 5 : 0;
        $score += $hasUpper ? 5 : 0;
        $score += $hasDigit ? 5 : 0;
        $score += $hasSpecial ? 5 : 0;
        
        // 检查重复字符和连续字符
        $repeats = 0;
        $sequences = 0;
        
        for ($i = 0; $i < $length - 1; $i++) {
            if ($password[$i] === $password[$i + 1]) {
                $repeats++;
            }
            
            if (ord($password[$i]) + 1 === ord($password[$i + 1])) {
                $sequences++;
            }
        }
        
        $score -= min($repeats * 2, 10);
        $score -= min($sequences * 2, 10);
        
        // 确保分数在 0-100 之间
        $score = max(0, min(100, $score));
        
        // 确定强度级别
        $strength = 'weak';
        if ($score >= 80) {
            $strength = 'very-strong';
        } elseif ($score >= 60) {
            $strength = 'strong';
        } elseif ($score >= 40) {
            $strength = 'medium';
        }
        
        return [
            'score' => $score,
            'strength' => $strength,
            'suggestions' => $this->getPasswordSuggestions($password, $score),
        ];
    }
    
    /**
     * 获取密码改进建议
     *
     * @param string $password 密码
     * @param int $score 密码得分
     * @return array 改进建议
     */
    protected function getPasswordSuggestions($password, $score)
    {
        $suggestions = [];
        
        if (strlen($password) < 12) {
            $suggestions[] = '增加密码长度至少 12 个字符';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $suggestions[] = '添加小写字母';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $suggestions[] = '添加大写字母';
        }
        
        if (!preg_match('/\d/', $password)) {
            $suggestions[] = '添加数字';
        }
        
        if (!preg_match('/[^a-zA-Z\d]/', $password)) {
            $suggestions[] = '添加特殊字符';
        }
        
        if (preg_match('/(.)\1{2,}/', $password)) {
            $suggestions[] = '避免使用重复字符';
        }
        
        if (preg_match('/(?:abcdef|bcdefg|cdefgh|defghi|efghij|fghijk|ghijkl|hijklm|ijklmn|jklmno|klmnop|lmnopq|mnopqr|nopqrs|opqrst|pqrstu|qrstuv|rstuvw|stuvwx|tuvwxy|uvwxyz|0123456789|1234567890)/i', $password)) {
            $suggestions[] = '避免使用连续字符';
        }
        
        return $suggestions;
    }
}
