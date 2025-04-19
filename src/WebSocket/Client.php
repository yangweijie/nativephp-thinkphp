<?php

namespace Native\ThinkPHP\WebSocket;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client as HttpClient;

class Client
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * HTTP 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $httpClient;

    /**
     * WebSocket 连接
     *
     * @var resource|null
     */
    protected $connection = null;

    /**
     * 事件回调
     *
     * @var array
     */
    protected $callbacks = [
        'open' => [],
        'message' => [],
        'close' => [],
        'error' => [],
        'ping' => [],
        'pong' => [],
    ];

    /**
     * 是否正在运行事件循环
     *
     * @var bool
     */
    protected $running = false;

    /**
     * 事件循环线程
     *
     * @var resource|null
     */
    protected $eventLoopThread = null;

    /**
     * 心跳间隔（秒）
     *
     * @var int
     */
    protected $heartbeatInterval = 30;

    /**
     * 上次心跳时间
     *
     * @var int
     */
    protected $lastHeartbeat = 0;

    /**
     * 重连次数
     *
     * @var int
     */
    protected $reconnectAttempts = 0;

    /**
     * 最大重连次数
     *
     * @var int
     */
    protected $maxReconnectAttempts = 5;

    /**
     * 重连间隔（秒）
     *
     * @var int
     */
    protected $reconnectInterval = 3;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->httpClient = new HttpClient();
    }

    /**
     * 连接到 WebSocket 服务器
     *
     * @param string $url WebSocket URL，格式如：ws://example.com:8080/path
     * @param array $headers 请求头
     * @param int $timeout 连接超时时间（秒）
     * @return bool
     */
    public function connect(string $url, array $headers = [], int $timeout = 30): bool
    {
        try {
            // 解析 URL
            $parsedUrl = parse_url($url);

            if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['ws', 'wss'])) {
                // 如果 URL 协议无效，返回 false
                return false;
            }

            $scheme = $parsedUrl['scheme'] == 'wss' ? 'ssl' : 'tcp';
            $host = $parsedUrl['host'];
            $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : ($scheme == 'ssl' ? 443 : 80);
            $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
            if (isset($parsedUrl['query'])) {
                $path .= '?' . $parsedUrl['query'];
            }

            // 创建套接字连接
            $address = "$scheme://$host:$port";
            $this->connection = stream_socket_client($address, $errno, $errstr, $timeout);

            if (!$this->connection) {
                $this->triggerCallbacks('error', [$errstr]);
                return false;
            }

            // 如果是 SSL 连接，启用加密
            if ($scheme == 'ssl') {
                stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            }

            // 设置为非阻塞模式
            stream_set_blocking($this->connection, false);

            // 准备 WebSocket 握手请求
            $key = base64_encode(random_bytes(16));
            $handshake = "GET $path HTTP/1.1\r\n";
            $handshake .= "Host: $host:$port\r\n";
            $handshake .= "Upgrade: websocket\r\n";
            $handshake .= "Connection: Upgrade\r\n";
            $handshake .= "Sec-WebSocket-Key: $key\r\n";
            $handshake .= "Sec-WebSocket-Version: 13\r\n";

            // 添加自定义头
            foreach ($headers as $name => $value) {
                $handshake .= "$name: $value\r\n";
            }

            $handshake .= "\r\n";

            // 发送握手请求
            fwrite($this->connection, $handshake);

            // 读取握手响应
            $response = '';
            $startTime = time();

            do {
                $buffer = fread($this->connection, 8192);
                $response .= $buffer;

                // 检查是否收到完整的响应
                if (strpos($response, "\r\n\r\n") !== false) {
                    break;
                }

                // 检查超时
                if (time() - $startTime > $timeout) {
                    throw new \Exception('握手超时');
                }

                // 等待一小段时间
                usleep(100000); // 100ms
            } while (true);

            // 解析响应
            if (!preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $response, $matches)) {
                throw new \Exception('无效的 WebSocket 握手响应');
            }

            $keyAccept = trim($matches[1]);
            $expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

            if ($keyAccept !== $expectedResonse) {
                throw new \Exception('WebSocket 握手失败：密钥不匹配');
            }

            // 设置最后心跳时间
            $this->lastHeartbeat = time();

            // 触发打开事件
            $this->triggerCallbacks('open', []);

            // 启动事件循环
            $this->startEventLoop();

            return true;
        } catch (\Exception $e) {
            $this->triggerCallbacks('error', [$e->getMessage()]);
            return false;
        }
    }

    /**
     * 发送消息
     *
     * @param string|array $data 消息数据
     * @param int $opcode 操作码，默认为文本消息
     * @return bool
     */
    public function send($data, int $opcode = 0x1): bool
    {
        if (!$this->connection) {
            return false;
        }

        try {
            // 如果是数组，转换为 JSON
            $data = is_array($data) ? json_encode($data) : $data;

            // 编码 WebSocket 帧
            $frame = $this->encodeFrame($data, $opcode);

            // 发送帧
            fwrite($this->connection, $frame);

            return true;
        } catch (\Exception $e) {
            $this->triggerCallbacks('error', [$e->getMessage()]);
            return false;
        }
    }

    /**
     * 关闭连接
     *
     * @param int $code 关闭代码
     * @param string $reason 关闭原因
     * @return bool
     */
    public function close(int $code = 1000, string $reason = ''): bool
    {
        if (!$this->connection) {
            return false;
        }

        try {
            // 停止事件循环
            $this->stopEventLoop();

            // 发送关闭帧
            $payload = pack('n', $code) . $reason;
            $this->send($payload, 0x8); // 0x8 是关闭帧的操作码

            // 关闭连接
            fclose($this->connection);
            $this->connection = null;

            // 触发关闭事件
            $this->triggerCallbacks('close', [$code, $reason]);

            return true;
        } catch (\Exception $e) {
            $this->triggerCallbacks('error', [$e->getMessage()]);
            return false;
        }
    }

    /**
     * 检查连接是否打开
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connection !== null && is_resource($this->connection);
    }

    /**
     * 注册事件回调
     *
     * @param string $event
     * @param callable $callback
     * @return $this
     */
    public function on(string $event, callable $callback)
    {
        if (isset($this->callbacks[$event])) {
            $this->callbacks[$event][] = $callback;
        }

        return $this;
    }

    /**
     * 触发事件回调
     *
     * @param string $event
     * @param array $args
     * @return void
     */
    protected function triggerCallbacks(string $event, array $args = [])
    {
        if (isset($this->callbacks[$event])) {
            foreach ($this->callbacks[$event] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * 处理接收到的消息
     *
     * @return void
     */
    public function processMessages()
    {
        if (!$this->connection) {
            return;
        }

        // 读取数据
        $data = fread($this->connection, 8192);

        if ($data === false || strlen($data) === 0) {
            return;
        }

        // 解码 WebSocket 帧
        $frames = $this->decodeFrames($data);

        foreach ($frames as $frame) {
            $opcode = $frame['opcode'];
            $payload = $frame['payload'];

            switch ($opcode) {
                case 0x1: // 文本帧
                    $this->triggerCallbacks('message', [$payload]);
                    break;

                case 0x2: // 二进制帧
                    $this->triggerCallbacks('message', [$payload]);
                    break;

                case 0x8: // 关闭帧
                    $code = 1000;
                    $reason = '';

                    if (strlen($payload) >= 2) {
                        $code = unpack('n', substr($payload, 0, 2))[1];
                        $reason = substr($payload, 2);
                    }

                    $this->close($code, $reason);
                    break;

                case 0x9: // Ping 帧
                    // 响应 Pong
                    $this->send($payload, 0xA);
                    $this->triggerCallbacks('ping', [$payload]);
                    break;

                case 0xA: // Pong 帧
                    $this->lastHeartbeat = time();
                    $this->triggerCallbacks('pong', [$payload]);
                    break;
            }
        }
    }

    /**
     * 处理一次循环
     * 调用者应该定期调用该方法来处理消息和心跳
     *
     * @return void
     */
    public function tick()
    {
        if (!$this->running || !$this->isConnected()) {
            return;
        }

        // 处理消息
        $this->processMessages();

        // 发送心跳
        $this->sendHeartbeat();
    }

    /**
     * 启动事件循环
     *
     * @return void
     */
    protected function startEventLoop()
    {
        if ($this->running) {
            return;
        }

        $this->running = true;

        // 注册定时器，定期处理消息
        // 在实际应用中，可以使用异步事件循环或单独的线程
        // 这里使用一个简单的方法，让调用者手动调用 processMessages 方法

        // 初始化心跳时间
        $this->lastHeartbeat = time();
    }

    /**
     * 停止事件循环
     *
     * @return void
     */
    protected function stopEventLoop()
    {
        $this->running = false;
    }

    /**
     * 发送心跳
     *
     * @return void
     */
    protected function sendHeartbeat()
    {
        // 如果连接已关闭，不发送心跳
        if (!$this->isConnected()) {
            return;
        }

        // 检查是否需要发送心跳
        $now = time();
        if ($now - $this->lastHeartbeat >= $this->heartbeatInterval) {
            // 发送 Ping 帧
            $this->send('', 0x9);
            $this->lastHeartbeat = $now;
        }
    }

    /**
     * 编码 WebSocket 帧
     *
     * @param string $payload 载荷
     * @param int $opcode 操作码
     * @param bool $fin 是否是最后一帧
     * @return string
     */
    protected function encodeFrame(string $payload, int $opcode, bool $fin = true): string
    {
        $length = strlen($payload);
        $head = '';

        // 第一个字节：FIN + RSV1-3 + Opcode
        $head .= chr(($fin ? 0x80 : 0) | ($opcode & 0x0F));

        // 第二个字节：Mask + Payload length
        if ($length <= 125) {
            $head .= chr($length);
        } elseif ($length <= 65535) {
            $head .= chr(126) . pack('n', $length);
        } else {
            $head .= chr(127) . pack('J', $length);
        }

        // 返回帧
        return $head . $payload;
    }

    /**
     * 解码 WebSocket 帧
     *
     * @param string $data 原始数据
     * @return array 帧数组
     */
    protected function decodeFrames(string $data): array
    {
        $frames = [];
        $dataLength = strlen($data);
        $offset = 0;

        while ($offset < $dataLength) {
            // 检查数据长度
            if ($dataLength - $offset < 2) {
                break;
            }

            // 解析第一个字节
            $firstByte = ord($data[$offset]);
            $offset++;

            $fin = (bool) ($firstByte & 0x80);
            $opcode = $firstByte & 0x0F;

            // 解析第二个字节
            $secondByte = ord($data[$offset]);
            $offset++;

            $masked = (bool) ($secondByte & 0x80);
            $payloadLength = $secondByte & 0x7F;

            // 解析扩展长度
            if ($payloadLength === 126) {
                if ($dataLength - $offset < 2) {
                    break;
                }

                $payloadLength = unpack('n', substr($data, $offset, 2))[1];
                $offset += 2;
            } elseif ($payloadLength === 127) {
                if ($dataLength - $offset < 8) {
                    break;
                }

                $payloadLength = unpack('J', substr($data, $offset, 8))[1];
                $offset += 8;
            }

            // 解析掩码
            $mask = '';
            if ($masked) {
                if ($dataLength - $offset < 4) {
                    break;
                }

                $mask = substr($data, $offset, 4);
                $offset += 4;
            }

            // 检查数据是否完整
            if ($dataLength - $offset < $payloadLength) {
                break;
            }

            // 提取载荷
            $payload = substr($data, $offset, $payloadLength);
            $offset += $payloadLength;

            // 如果有掩码，解除掩码
            if ($masked) {
                $unmaskedPayload = '';
                for ($i = 0; $i < $payloadLength; $i++) {
                    $unmaskedPayload .= $payload[$i] ^ $mask[$i % 4];
                }
                $payload = $unmaskedPayload;
            }

            // 添加帧
            $frames[] = [
                'fin' => $fin,
                'opcode' => $opcode,
                'masked' => $masked,
                'payload' => $payload,
            ];
        }

        return $frames;
    }
}
