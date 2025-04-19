<?php

namespace Native\ThinkPHP\Support;

class Environment
{
    /**
     * 检查是否是 Windows 系统
     *
     * @return bool
     */
    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * 检查是否是 Linux 系统
     *
     * @return bool
     */
    public static function isLinux(): bool
    {
        return PHP_OS_FAMILY === 'Linux';
    }

    /**
     * 检查是否是 macOS 系统
     *
     * @return bool
     */
    public static function isMac(): bool
    {
        return PHP_OS_FAMILY === 'Darwin';
    }

    /**
     * 获取操作系统类型
     *
     * @return string
     */
    public static function getOS(): string
    {
        if (static::isWindows()) {
            return 'windows';
        }

        if (static::isMac()) {
            return 'macos';
        }

        if (static::isLinux()) {
            return 'linux';
        }

        return strtolower(PHP_OS);
    }

    /**
     * 获取操作系统架构
     *
     * @return string
     */
    public static function getArch(): string
    {
        $arch = php_uname('m');

        if ($arch === 'x86_64' || $arch === 'amd64') {
            return 'x64';
        }

        if ($arch === 'aarch64' || $arch === 'arm64') {
            return 'arm64';
        }

        if (strpos($arch, 'arm') === 0) {
            return 'arm';
        }

        if ($arch === 'i386' || $arch === 'i686') {
            return 'ia32';
        }

        return $arch;
    }

    /**
     * 获取操作系统版本
     *
     * @return string
     */
    public static function getOSVersion(): string
    {
        return php_uname('r');
    }

    /**
     * 获取主机名
     *
     * @return string
     */
    public static function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * 获取用户主目录
     *
     * @return string
     */
    public static function getHomePath(): string
    {
        if (static::isWindows()) {
            return getenv('USERPROFILE');
        }

        return getenv('HOME');
    }

    /**
     * 获取临时目录
     *
     * @return string
     */
    public static function getTempPath(): string
    {
        return sys_get_temp_dir();
    }
}
