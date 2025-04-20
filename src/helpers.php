<?php

if (!function_exists('array_get')) {
    /**
     * 从数组中获取值
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get(array $array, string $key, mixed $default = null): mixed
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
}

if (!function_exists('generate_update_signature')) {
    /**
     * 生成更新包签名
     *
     * @param string $filePath 更新包路径
     * @param string $privateKey 私钥内容
     * @return string 签名
     * @throws \RuntimeException
     */
    function generate_update_signature($filePath, $privateKey)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('更新包文件不存在');
        }

        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new \RuntimeException('读取更新包失败');
        }

        $key = openssl_pkey_get_private($privateKey);
        if (!$key) {
            throw new \RuntimeException('无效的私钥');
        }

        if (!openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('生成签名失败');
        }

        openssl_free_key($key);

        return base64_encode($signature);
    }
}

if (!function_exists('verify_update_signature')) {
    /**
     * 验证更新包签名
     *
     * @param string $filePath 更新包路径
     * @param string $signature Base64编码的签名
     * @param string $publicKey 公钥内容
     * @return bool
     * @throws \RuntimeException
     */
    function verify_update_signature($filePath, $signature, $publicKey)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('更新包文件不存在');
        }

        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new \RuntimeException('读取更新包失败');
        }

        $key = openssl_pkey_get_public($publicKey);
        if (!$key) {
            throw new \RuntimeException('无效的公钥');
        }

        $signature = base64_decode($signature);
        $result = openssl_verify($data, $signature, $key, OPENSSL_ALGO_SHA256);

        openssl_free_key($key);

        if ($result === -1) {
            throw new \RuntimeException('验证签名时发生错误');
        }

        return $result === 1;
    }
}
