<?php

namespace Native\ThinkPHP\Documentation;

use think\App;
use think\facade\Filesystem;

class DocumentationGenerator
{
    /**
     * ThinkPHP 应用实例
     *
     * @var App
     */
    protected $app;

    /**
     * 文档输出目录
     *
     * @var string
     */
    protected $outputDir;

    /**
     * 文档模板目录
     *
     * @var string
     */
    protected $templateDir;

    /**
     * 构造函数
     *
     * @param App $app
     * @param string|null $outputDir 文档输出目录
     * @param string|null $templateDir 文档模板目录
     */
    public function __construct(App $app, string $outputDir = null, string $templateDir = null)
    {
        $this->app = $app;
        $this->outputDir = $outputDir ?? $app->getRootPath() . 'docs';
        $this->templateDir = $templateDir ?? __DIR__ . '/templates';
    }

    /**
     * 生成 API 文档
     *
     * @param array $classes 要生成文档的类
     * @return bool
     */
    public function generateApiDocs(array $classes): bool
    {
        // 确保输出目录存在
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        // 生成每个类的文档
        foreach ($classes as $class) {
            $this->generateClassDoc($class);
        }

        // 生成索引文件
        $this->generateApiIndex($classes);

        return true;
    }

    /**
     * 生成类文档
     *
     * @param string $class 类名
     * @return bool
     */
    protected function generateClassDoc(string $class): bool
    {
        // 获取类的反射信息
        $reflection = new \ReflectionClass($class);

        // 获取类的注释
        $docComment = $reflection->getDocComment();
        $description = $this->parseDocComment($docComment);

        // 获取类的方法
        $methods = [];
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            // 跳过魔术方法
            if (strpos($method->getName(), '__') === 0) {
                continue;
            }

            // 获取方法的注释
            $methodDocComment = $method->getDocComment();
            $methodDescription = $this->parseDocComment($methodDocComment);

            // 获取方法的参数
            $parameters = [];
            foreach ($method->getParameters() as $parameter) {
                $parameters[] = [
                    'name' => $parameter->getName(),
                    /** @phpstan-ignore-next-line */
                    'type' => $parameter->getType() ? $parameter->getType()->getName() : 'mixed',
                    'isOptional' => $parameter->isOptional(),
                    'defaultValue' => $parameter->isOptional() ? $parameter->getDefaultValue() : null,
                ];
            }

            // 获取方法的返回类型
            /** @phpstan-ignore-next-line */
            $returnType = $method->getReturnType() ? $method->getReturnType()->getName() : 'mixed';

            // 添加方法信息
            $methods[] = [
                'name' => $method->getName(),
                'description' => $methodDescription,
                'parameters' => $parameters,
                'returnType' => $returnType,
            ];
        }

        // 生成文档内容
        $content = $this->renderTemplate('class', [
            'class' => $class,
            'description' => $description,
            'methods' => $methods,
        ]);

        // 保存文档
        $filename = str_replace('\\', '_', $class) . '.md';
        file_put_contents($this->outputDir . '/' . $filename, $content);

        return true;
    }

    /**
     * 生成 API 索引
     *
     * @param array $classes 类列表
     * @return bool
     */
    protected function generateApiIndex(array $classes): bool
    {
        // 生成索引内容
        $content = $this->renderTemplate('api_index', [
            'classes' => $classes,
        ]);

        // 保存索引
        file_put_contents($this->outputDir . '/api_index.md', $content);

        return true;
    }

    /**
     * 生成用户手册
     *
     * @param array $sections 手册章节
     * @return bool
     */
    public function generateUserManual(array $sections): bool
    {
        // 确保输出目录存在
        if (!is_dir($this->outputDir . '/manual')) {
            mkdir($this->outputDir . '/manual', 0755, true);
        }

        // 生成每个章节的文档
        foreach ($sections as $section) {
            $this->generateManualSection($section);
        }

        // 生成索引文件
        $this->generateManualIndex($sections);

        return true;
    }

    /**
     * 生成手册章节
     *
     * @param array $section 章节信息
     * @return bool
     */
    protected function generateManualSection(array $section): bool
    {
        // 生成章节内容
        $content = $this->renderTemplate('manual_section', $section);

        // 保存章节
        file_put_contents($this->outputDir . '/manual/' . $section['file'], $content);

        return true;
    }

    /**
     * 生成手册索引
     *
     * @param array $sections 章节列表
     * @return bool
     */
    protected function generateManualIndex(array $sections): bool
    {
        // 生成索引内容
        $content = $this->renderTemplate('manual_index', [
            'sections' => $sections,
        ]);

        // 保存索引
        file_put_contents($this->outputDir . '/manual/index.md', $content);

        return true;
    }

    /**
     * 解析文档注释
     *
     * @param string|false $docComment 文档注释
     * @return string
     */
    protected function parseDocComment($docComment): string
    {
        if (!$docComment) {
            return '';
        }

        // 移除注释标记
        $docComment = preg_replace('/^\s*\/\*\*\s*|^\s*\*\s*|\s*\*\/\s*$/m', '', $docComment);

        // 移除 @param、@return 等标记
        $docComment = preg_replace('/@\w+\s+.*$/m', '', $docComment);

        // 清理空行
        $docComment = preg_replace('/^\s*$/m', '', $docComment);

        return trim($docComment);
    }

    /**
     * 渲染模板
     *
     * @param string $template 模板名称
     * @param array $data 模板数据
     * @return string
     */
    protected function renderTemplate(string $template, array $data): string
    {
        // 获取模板文件
        $templateFile = $this->templateDir . '/' . $template . '.php';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("模板文件不存在：{$templateFile}");
        }

        // 提取变量
        extract($data);

        // 启动输出缓冲
        ob_start();

        // 包含模板文件
        include $templateFile;

        // 获取输出内容
        $content = ob_get_clean();

        return $content;
    }
}