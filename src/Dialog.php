<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use Native\ThinkPHP\DTOs\DialogConfig;

class Dialog
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 显示打开文件对话框
     *
     * @param array $options 选项
     * @return string|array|null 文件路径或文件路径数组，如果取消则返回 null
     */
    public function openFile(array $options = [])
    {
        $response = $this->client->post('dialog/open-file', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示保存文件对话框
     *
     * @param array $options 选项
     * @return string|null 文件路径，如果取消则返回 null
     */
    public function saveFile(array $options = [])
    {
        $response = $this->client->post('dialog/save-file', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示选择文件夹对话框
     *
     * @param array $options 选项
     * @return string|null 文件夹路径，如果取消则返回 null
     */
    public function selectFolder(array $options = [])
    {
        $response = $this->client->post('dialog/select-folder', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示消息框
     *
     * @param string $message 消息内容
     * @param array $options 选项
     * @return int 点击的按钮索引
     */
    public function message($message, array $options = [])
    {
        $options['message'] = $message;
        $response = $this->client->post('dialog/message', $options);
        $data = json_decode($response->getContent(), true);
        return (int) ($data['result'] ?? 0);
    }

    /**
     * 显示错误消息框
     *
     * @param string $message 错误消息
     * @param array $options 选项
     * @return int
     */
    public function error($message, array $options = [])
    {
        $options['type'] = 'error';
        return $this->message($message, $options);
    }

    /**
     * 显示信息消息框
     *
     * @param string $message 信息消息
     * @param array $options 选项
     * @return int
     */
    public function info($message, array $options = [])
    {
        $options['type'] = 'info';
        return $this->message($message, $options);
    }

    /**
     * 显示警告消息框
     *
     * @param string $message 警告消息
     * @param array $options 选项
     * @return int
     */
    public function warning($message, array $options = [])
    {
        $options['type'] = 'warning';
        return $this->message($message, $options);
    }

    /**
     * 显示问题消息框
     *
     * @param string $message 问题消息
     * @param array $options 选项
     * @return int
     */
    public function question($message, array $options = [])
    {
        $options['type'] = 'question';
        return $this->message($message, $options);
    }

    /**
     * 显示确认消息框
     *
     * @param string $message 确认消息
     * @param array $options 选项
     * @return bool 如果点击确认返回 true，否则返回 false
     */
    public function confirm($message, array $options = [])
    {
        $options['type'] = 'question';
        $options['buttons'] = $options['buttons'] ?? ['取消', '确认'];
        $options['defaultId'] = $options['defaultId'] ?? 1;
        $options['cancelId'] = $options['cancelId'] ?? 0;

        $result = $this->message($message, $options);

        return $result === 1;
    }

    /**
     * 显示输入框
     *
     * @param string $message 消息
     * @param array $options 选项
     * @return string|null 输入的文本，如果取消则返回 null
     */
    public function prompt($message, array $options = [])
    {
        $options['message'] = $message;
        $response = $this->client->post('dialog/prompt', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示证书选择对话框
     *
     * @param array $options 选项
     * @return array|null 证书信息，如果取消则返回 null
     */
    public function certificate(array $options = [])
    {
        $response = $this->client->post('dialog/certificate', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示颜色选择对话框
     *
     * @param array $options 选项
     * @return string|null 颜色值，如果取消则返回 null
     */
    public function color(array $options = [])
    {
        $response = $this->client->post('dialog/color', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 显示字体选择对话框
     *
     * @param array $options 选项
     * @return array|null 字体信息，如果取消则返回 null
     */
    public function font(array $options = [])
    {
        $response = $this->client->post('dialog/font', $options);
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }

    /**
     * 使用配置对象显示对话框
     *
     * @param \Native\ThinkPHP\DTOs\DialogConfig $config 对话框配置
     * @return mixed 对话框结果
     */
    public function showWithConfig(DialogConfig $config)
    {
        $response = $this->client->post('dialog/show', $config->toArray());
        $data = json_decode($response->getContent(), true);
        return $data['result'] ?? null;
    }
}
