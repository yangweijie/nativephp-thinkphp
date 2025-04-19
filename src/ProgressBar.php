<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use function Native\ThinkPHP\Plugins\app;

class ProgressBar
{
    /**
     * 当前进度百分比
     *
     * @var float
     */
    protected float $percent = 0;

    /**
     * 当前步骤
     *
     * @var int
     */
    protected int $step = 0;

    /**
     * 最后一次写入时间
     *
     * @var float
     */
    protected float $lastWriteTime = 0;

    /**
     * 两次重绘之间的最小秒数
     *
     * @var float
     */
    protected float $minSecondsBetweenRedraws = 0.1;

    /**
     * 两次重绘之间的最大秒数
     *
     * @var float
     */
    protected float $maxSecondsBetweenRedraws = 1;

    /**
     * 最大步骤数
     *
     * @var int
     */
    protected int $maxSteps;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param int $maxSteps
     */
    public function __construct(ThinkApp $app, int $maxSteps = 0)
    {
        $this->app = $app;
        $this->maxSteps = $maxSteps;
        $this->client = new Client();
    }

    /**
     * 创建新实例
     *
     * @param int $maxSteps
     * @return self
     */
    public static function create(int $maxSteps): \Native\ThinkPHP\ProgressBar
    {
        return new self(app(), $maxSteps);
    }

    /**
     * 开始进度条
     *
     * @return void
     */
    public function start()
    {
        $this->lastWriteTime = microtime(true);
        $this->setProgress(0);
    }

    /**
     * 前进步骤
     *
     * @param int $step
     * @return void
     */
    public function advance($step = 1)
    {
        $this->setProgress($this->step + $step);
    }

    /**
     * 设置进度
     *
     * @param int $step
     * @return void
     */
    public function setProgress(int $step)
    {
        if ($this->maxSteps && $step > $this->maxSteps) {
            $this->maxSteps = $step;
        } elseif ($step < 0) {
            $step = 0;
        }

        $redrawFreq = 1;
        $prevPeriod = (int) ($this->step / $redrawFreq);
        $currPeriod = (int) ($step / $redrawFreq);

        $this->step = $step;
        $this->percent = $this->maxSteps ? (float) $this->step / $this->maxSteps : 0;

        $timeInterval = microtime(true) - $this->lastWriteTime;

        // 无论其他限制如何，都绘制
        if ($this->maxSteps === $step) {
            $this->display();
            return;
        }

        // 节流
        if ($timeInterval < $this->minSecondsBetweenRedraws) {
            return;
        }

        // 每个步骤周期绘制，但不要太晚
        if ($prevPeriod !== $currPeriod || $timeInterval >= $this->maxSecondsBetweenRedraws) {
            $this->display();
        }
    }

    /**
     * 完成进度条
     *
     * @return void
     */
    public function finish()
    {
        $this->client->post('progress-bar/update', [
            'percent' => -1,
        ]);
    }

    /**
     * 显示进度条
     *
     * @return void
     */
    public function display()
    {
        $this->lastWriteTime = microtime(true);

        $this->client->post('progress-bar/update', [
            'percent' => $this->percent,
        ]);
    }
}
