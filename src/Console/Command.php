<?php

namespace Native\ThinkPHP\Console;

use think\console\Command as ThinkCommand;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;

abstract class Command extends ThinkCommand
{
    /**
     * 配置命令
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description);
    }

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int
     */
    protected function execute(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this->handle();
    }

    /**
     * 处理命令
     *
     * @return int
     */
    abstract protected function handle();

    /**
     * 输出信息
     *
     * @param string $string
     * @return void
     */
    protected function info($string)
    {
        $this->output->info($string);
    }

    /**
     * 输出错误信息
     *
     * @param string $string
     * @return void
     */
    protected function error($string)
    {
        $this->output->error($string);
    }

    /**
     * 输出警告信息
     *
     * @param string $string
     * @return void
     */
    protected function warn($string)
    {
        $this->output->warning($string);
    }

    /**
     * 输出注释信息
     *
     * @param string $string
     * @return void
     */
    protected function comment($string)
    {
        $this->output->comment($string);
    }

    /**
     * 输出问题信息
     *
     * @param string $string
     * @return void
     */
    protected function question($string)
    {
        $this->output->question($string);
    }

    /**
     * 输出表格
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */
    protected function table(array $headers, array $rows)
    {
        $this->output->table($headers, $rows);
    }

    /**
     * 询问确认
     *
     * @param string $question
     * @param bool $default
     * @return bool
     */
    protected function confirm($question, $default = false)
    {
        return $this->output->confirm($question, $default);
    }

    /**
     * 询问选择
     *
     * @param string $question
     * @param array $choices
     * @param mixed $default
     * @return mixed
     */
    protected function choice($question, array $choices, $default = null)
    {
        return $this->output->choice($question, $choices, $default);
    }

    /**
     * 询问输入
     *
     * @param string $question
     * @param string $default
     * @return string
     */
    protected function ask($question, $default = null)
    {
        return $this->output->ask($question, $default);
    }

    /**
     * 询问密码输入
     *
     * @param string $question
     * @param bool $fallback
     * @return string
     */
    protected function secret($question, $fallback = true)
    {
        return $this->output->ask($question, null, true);
    }

    /**
     * 调用其他命令
     *
     * @param string $command
     * @param array $arguments
     * @return int
     */
    protected function call($command, array $arguments = [])
    {
        return $this->getApplication()->call($command, $arguments, $this->output);
    }

    /**
     * 获取参数
     *
     * @param string $key
     * @return mixed
     */
    protected function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * 获取选项
     *
     * @param string $key
     * @return mixed
     */
    protected function option($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }
}
