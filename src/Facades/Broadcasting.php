<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool broadcast(string $channel, string $event, array $data = []) 广播事件
 * @method static string listen(string $channel, string $event, callable $callback) 监听事件
 * @method static bool unlisten(string $id) 取消监听事件
 * @method static array getChannels() 获取所有频道
 * @method static array getEvents(string $channel) 获取频道中的事件
 * @method static bool createChannel(string $channel) 创建频道
 * @method static bool deleteChannel(string $channel) 删除频道
 * @method static bool clearChannel(string $channel) 清空频道
 * @method static bool channelExists(string $channel) 检查频道是否存在
 * @method static int getListenerCount(string $channel) 获取频道中的监听器数量
 * @method static array getListeners() 获取所有监听器
 *
 * @see \Native\ThinkPHP\Broadcasting
 */
class Broadcasting extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.broadcasting';
    }
}
