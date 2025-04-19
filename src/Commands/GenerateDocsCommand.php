<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use ReflectionClass;
use ReflectionMethod;

class GenerateDocsCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:docs')
            ->setDescription('生成 NativePHP for ThinkPHP 的 API 文档');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('正在生成 API 文档...');

        // 获取所有 Facade 类
        $facadeClasses = $this->getFacadeClasses();

        // 生成文档
        $this->generateDocs($facadeClasses, $output);

        $output->writeln('<info>API 文档生成完成！</info>');
        return 0;
    }

    /**
     * 获取所有 Facade 类
     *
     * @return array
     */
    protected function getFacadeClasses()
    {
        $facadesDir = __DIR__ . '/../Facades';
        $facadeClasses = [];

        if (is_dir($facadesDir)) {
            $files = scandir($facadesDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $className = 'Native\\ThinkPHP\\Facades\\' . pathinfo($file, PATHINFO_FILENAME);
                    $facadeClasses[] = $className;
                }
            }
        }

        return $facadeClasses;
    }

    /**
     * 生成文档
     *
     * @param array $facadeClasses
     * @param Output $output
     * @return void
     */
    protected function generateDocs($facadeClasses, Output $output)
    {
        $docsDir = App::getRootPath() . 'docs/api';
        if (!is_dir($docsDir)) {
            mkdir($docsDir, 0755, true);
        }

        // 生成索引文件
        $this->generateIndexFile($facadeClasses, $docsDir, $output);

        // 生成每个 Facade 的文档
        foreach ($facadeClasses as $facadeClass) {
            $this->generateFacadeDoc($facadeClass, $docsDir, $output);
        }
    }

    /**
     * 生成索引文件
     *
     * @param array $facadeClasses
     * @param string $docsDir
     * @param Output $output
     * @return void
     */
    protected function generateIndexFile($facadeClasses, $docsDir, Output $output)
    {
        $indexContent = "# NativePHP for ThinkPHP API 文档\n\n";
        $indexContent .= "本文档提供了 NativePHP for ThinkPHP 的 API 参考。\n\n";
        $indexContent .= "## Facades\n\n";

        foreach ($facadeClasses as $facadeClass) {
            $facadeName = basename(str_replace('\\', '/', $facadeClass));
            $indexContent .= "- [{$facadeName}]({$facadeName}.md)\n";
        }

        file_put_contents($docsDir . '/index.md', $indexContent);
        $output->writeln("生成索引文件：{$docsDir}/index.md");
    }

    /**
     * 生成 Facade 文档
     *
     * @param string $facadeClass
     * @param string $docsDir
     * @param Output $output
     * @return void
     */
    protected function generateFacadeDoc($facadeClass, $docsDir, Output $output)
    {
        $facadeName = basename(str_replace('\\', '/', $facadeClass));
        $docContent = "# {$facadeName} Facade\n\n";

        try {
            $reflection = new ReflectionClass($facadeClass);
            $docComment = $reflection->getDocComment();

            if ($docComment) {
                $docContent .= $this->parseDocComment($docComment);
            }

            // 获取对应的实现类
            $implementationClass = $this->getImplementationClass($facadeClass);
            if ($implementationClass) {
                $docContent .= "\n## 实现类\n\n";
                $docContent .= "`{$implementationClass}`\n\n";

                // 获取实现类的方法
                $implReflection = new ReflectionClass($implementationClass);
                $methods = $implReflection->getMethods(ReflectionMethod::IS_PUBLIC);

                if ($methods) {
                    $docContent .= "## 方法\n\n";

                    foreach ($methods as $method) {
                        if ($method->isConstructor() || $method->isDestructor()) {
                            continue;
                        }

                        $methodName = $method->getName();
                        $docContent .= "### {$methodName}\n\n";

                        $methodDocComment = $method->getDocComment();
                        if ($methodDocComment) {
                            $docContent .= $this->parseMethodDocComment($methodDocComment);
                        }

                        $docContent .= "```php\n";
                        $docContent .= $this->getMethodSignature($method);
                        $docContent .= "\n```\n\n";
                    }
                }
            }

            file_put_contents($docsDir . '/' . $facadeName . '.md', $docContent);
            $output->writeln("生成 Facade 文档：{$docsDir}/{$facadeName}.md");
        } catch (\Exception $e) {
            $output->writeln("<error>生成 {$facadeName} 文档时出错：{$e->getMessage()}</error>");
        }
    }

    /**
     * 解析类文档注释
     *
     * @param string $docComment
     * @return string
     */
    protected function parseDocComment($docComment)
    {
        $docContent = '';
        $lines = explode("\n", $docComment);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = preg_replace('/^\s*\*\s*/', '', $line);
            $line = preg_replace('/^\s*\/\*\*/', '', $line);
            $line = preg_replace('/\*\/\s*$/', '', $line);

            if (preg_match('/^@method\s+(.+)$/', $line, $matches)) {
                if (!strpos($docContent, '## 方法')) {
                    $docContent .= "## 方法\n\n";
                }

                $methodInfo = $matches[1];
                $docContent .= "- `{$methodInfo}`\n";
            } elseif (!empty($line) && !preg_match('/^@/', $line)) {
                $docContent .= "{$line}\n";
            }
        }

        return $docContent;
    }

    /**
     * 解析方法文档注释
     *
     * @param string $docComment
     * @return string
     */
    protected function parseMethodDocComment($docComment)
    {
        $docContent = '';
        $description = '';
        $params = [];
        $return = '';

        $lines = explode("\n", $docComment);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = preg_replace('/^\s*\*\s*/', '', $line);
            $line = preg_replace('/^\s*\/\*\*/', '', $line);
            $line = preg_replace('/\*\/\s*$/', '', $line);

            if (preg_match('/^@param\s+(\S+)\s+\$(\S+)(?:\s+(.+))?$/', $line, $matches)) {
                $type = $matches[1];
                $name = $matches[2];
                $desc = isset($matches[3]) ? $matches[3] : '';
                $params[] = [
                    'type' => $type,
                    'name' => $name,
                    'description' => $desc,
                ];
            } elseif (preg_match('/^@return\s+(\S+)(?:\s+(.+))?$/', $line, $matches)) {
                $type = $matches[1];
                $desc = isset($matches[2]) ? $matches[2] : '';
                $return = [
                    'type' => $type,
                    'description' => $desc,
                ];
            } elseif (!empty($line) && !preg_match('/^@/', $line)) {
                $description .= "{$line}\n";
            }
        }

        if (!empty($description)) {
            $docContent .= trim($description) . "\n\n";
        }

        if (!empty($params)) {
            $docContent .= "**参数：**\n\n";
            foreach ($params as $param) {
                $docContent .= "- `\${$param['name']}` ({$param['type']})";
                if (!empty($param['description'])) {
                    $docContent .= " - {$param['description']}";
                }
                $docContent .= "\n";
            }
            $docContent .= "\n";
        }

        if (!empty($return)) {
            $docContent .= "**返回值：** ";
            $docContent .= "{$return['type']}";
            if (!empty($return['description'])) {
                $docContent .= " - {$return['description']}";
            }
            $docContent .= "\n\n";
        }

        return $docContent;
    }

    /**
     * 获取方法签名
     *
     * @param ReflectionMethod $method
     * @return string
     */
    protected function getMethodSignature(ReflectionMethod $method)
    {
        $signature = '';

        if ($method->isPublic()) {
            $signature .= 'public ';
        } elseif ($method->isProtected()) {
            $signature .= 'protected ';
        } elseif ($method->isPrivate()) {
            $signature .= 'private ';
        }

        if ($method->isStatic()) {
            $signature .= 'static ';
        }

        $signature .= 'function ' . $method->getName() . '(';

        $parameters = $method->getParameters();
        $paramStrings = [];

        foreach ($parameters as $parameter) {
            $paramStr = '';

            if ($parameter->hasType()) {
                $type = $parameter->getType();
                /** @phpstan-ignore-next-line */
                $paramStr .= $type->getName() . ' ';
            }

            $paramStr .= '$' . $parameter->getName();

            if ($parameter->isDefaultValueAvailable()) {
                $defaultValue = $parameter->getDefaultValue();
                if (is_string($defaultValue)) {
                    $defaultValue = "'{$defaultValue}'";
                } elseif (is_array($defaultValue)) {
                    $defaultValue = '[]';
                } elseif (is_null($defaultValue)) {
                    $defaultValue = 'null';
                } elseif (is_bool($defaultValue)) {
                    $defaultValue = $defaultValue ? 'true' : 'false';
                }
                $paramStr .= ' = ' . $defaultValue;
            }

            $paramStrings[] = $paramStr;
        }

        $signature .= implode(', ', $paramStrings);
        $signature .= ')';

        return $signature;
    }

    /**
     * 获取 Facade 对应的实现类
     *
     * @param string $facadeClass
     * @return string|null
     */
    protected function getImplementationClass($facadeClass)
    {
        try {
            $reflection = new ReflectionClass($facadeClass);
            $docComment = $reflection->getDocComment();

            if (preg_match('/@see\s+([^\s]+)/', $docComment, $matches)) {
                return $matches[1];
            }
        } catch (\Exception $e) {
            // 忽略异常
        }

        return null;
    }
}
