<?php

namespace NativePHP\Think\Contract;

interface WindowContract
{
    public function title(string $title): self;
    public function width(int $width): self;
    public function height(int $height): self;
    public function minWidth(int $width): self;
    public function minHeight(int $height): self;
    public function maxWidth(int $width): self;
    public function maxHeight(int $height): self;
    public function x(int $x): self;
    public function y(int $y): self;
    public function center(): self;
    public function fullscreen(bool $fullscreen = true): self;
    public function resizable(bool $resizable = true): self;
    public function minimizable(bool $minimizable = true): self;
    public function maximizable(bool $maximizable = true): self;
    public function closable(bool $closable = true): self;
    public function alwaysOnTop(bool $alwaysOnTop = true): self;
    public function skipTaskbar(bool $skip = true): self;
    public function decorations(bool $decorations = true): self;
    public function focused(bool $focused = true): self;
    public function blur(): self;
    public function show(): self;
    public function hide(): self;
    public function configure(array $options): self;
    public function getOptions(): array;
    public function create(): self;
    public function close(): self;
}