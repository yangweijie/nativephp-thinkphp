<?php

namespace NativePHP\Think\Tests\Pest;

/**
 * 模拟 Ipc 类，用于测试
 */
class MockIpc
{
    protected $handlers = [];
    protected $sentMessages = [];
    
    public function __construct($native) {}
    
    public function handle($channel, $handler) {
        $this->handlers[$channel] = $handler;
        return $this;
    }
    
    public function send($channel, $data = null) {
        $this->sentMessages[] = [
            'channel' => $channel,
            'data' => $data,
        ];
        
        if (isset($this->handlers[$channel])) {
            call_user_func($this->handlers[$channel], $data);
        }
        
        return $this;
    }
    
    public function getSentMessages() {
        return $this->sentMessages;
    }
    
    public function getHandlers() {
        return $this->handlers;
    }
}
