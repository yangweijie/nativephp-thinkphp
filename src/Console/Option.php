<?php

namespace Native\ThinkPHP\Console;

/**
 * 命令行选项
 */
class Option
{
    /**
     * 选项值是必需的
     */
    const VALUE_REQUIRED = 1;

    /**
     * 选项值是可选的
     */
    const VALUE_OPTIONAL = 2;

    /**
     * 选项值是数组
     */
    const VALUE_IS_ARRAY = 4;

    /**
     * 选项没有值
     */
    const VALUE_NONE = 8;

    /**
     * 选项名称
     *
     * @var string
     */
    protected $name;

    /**
     * 选项短名称
     *
     * @var string|null
     */
    protected $shortcut;

    /**
     * 选项模式
     *
     * @var int
     */
    protected $mode;

    /**
     * 选项描述
     *
     * @var string
     */
    protected $description;

    /**
     * 选项默认值
     *
     * @var mixed
     */
    protected $default;

    /**
     * 构造函数
     *
     * @param string $name 选项名称
     * @param string|null $shortcut 选项短名称
     * @param int $mode 选项模式
     * @param string $description 选项描述
     * @param mixed $default 选项默认值
     */
    public function __construct(string $name, ?string $shortcut = null, int $mode = self::VALUE_NONE, string $description = '', $default = null)
    {
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    /**
     * 获取选项名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取选项短名称
     *
     * @return string|null
     */
    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    /**
     * 获取选项模式
     *
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * 获取选项描述
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * 获取选项默认值
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 检查选项是否接受值
     *
     * @return bool
     */
    public function acceptValue(): bool
    {
        return $this->isValueRequired() || $this->isValueOptional();
    }

    /**
     * 检查选项是否必需值
     *
     * @return bool
     */
    public function isValueRequired(): bool
    {
        return self::VALUE_REQUIRED === ($this->mode & self::VALUE_REQUIRED);
    }

    /**
     * 检查选项是否可选值
     *
     * @return bool
     */
    public function isValueOptional(): bool
    {
        return self::VALUE_OPTIONAL === ($this->mode & self::VALUE_OPTIONAL);
    }

    /**
     * 检查选项是否接受数组值
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return self::VALUE_IS_ARRAY === ($this->mode & self::VALUE_IS_ARRAY);
    }
}
