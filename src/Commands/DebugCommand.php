<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;
use think\facade\Config;
use think\facade\Env;
use Native\ThinkPHP\Support\Environment;

class DebugCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:debug')
            ->addArgument('output', Argument::OPTIONAL, '输出方式：File, Clipboard, Console', 'File')
            ->setDescription('生成调试信息，用于提交问题');
    }

    protected function execute(Input $input, Output $output)
    {
        $debugInfo = [];

        $output->info('正在生成调试信息...');

        // 处理环境信息
        $debugInfo['Environment'] = $this->processEnvironment();

        // 处理 NativePHP 信息
        $debugInfo['NativePHP'] = $this->processNativePHP();

        // 输出调试信息
        $outputType = $input->getArgument('output');
        switch ($outputType) {
            case 'File':
                $this->outputToFile($debugInfo, $output);
                break;
            case 'Clipboard':
                $this->outputToClipboard($debugInfo, $output);
                break;
            case 'Console':
                $this->outputToConsole($debugInfo, $output);
                break;
            default:
                $output->error('无效的输出选项');
                return 1;
        }

        $output->info('调试信息已生成');
        return 0;
    }

    /**
     * 处理环境信息
     *
     * @return array
     */
    private function processEnvironment(): array
    {
        $locationCommand = 'which';
        if (Environment::isWindows()) {
            $locationCommand = 'where';
        }

        $nodeVersion = trim(shell_exec('node -v') ?: '');
        $nodePath = trim(shell_exec("$locationCommand node") ?: '');
        $npmVersion = trim(shell_exec('npm -v') ?: '');
        $npmPath = trim(shell_exec("$locationCommand npm") ?: '');

        return [
            'PHP' => [
                'Version' => phpversion(),
                'Path' => PHP_BINARY,
            ],
            'ThinkPHP' => [
                'Version' => App::version(),
                'DebugEnabled' => Env::get('APP_DEBUG', false),
            ],
            'Node' => [
                'Version' => $nodeVersion,
                'Path' => $nodePath,
            ],
            'NPM' => [
                'Version' => $npmVersion,
                'Path' => $npmPath,
            ],
            'OperatingSystem' => PHP_OS,
        ];
    }

    /**
     * 处理 NativePHP 信息
     *
     * @return array
     */
    private function processNativePHP(): array
    {
        // 获取 Composer 版本
        $composerJson = file_get_contents(App::getRootPath() . 'composer.json');
        $composerData = json_decode($composerJson, true);
        $versions = [
            'nativephp/electron' => $composerData['require']['nativephp/electron'] ?? 'Not Installed',
            'nativephp/thinkphp' => $composerData['require']['nativephp/thinkphp'] ?? 'Not Installed',
            'nativephp/php-bin' => $composerData['require']['nativephp/php-bin'] ?? 'Not Installed',
        ];

        $isNotarizationConfigured = Config::get('native.notarization.apple_id')
            && Config::get('native.notarization.apple_id_pass')
            && Config::get('native.notarization.apple_team_id');

        return [
            'Versions' => $versions,
            'Configuration' => [
                'Provider' => Config::get('native.provider'),
                'BuildHooks' => [
                    'Pre' => Config::get('native.prebuild'),
                    'Post' => Config::get('native.postbuild'),
                ],
                'NotarizationEnabled' => $isNotarizationConfigured,
                'CustomPHPBinary' => Config::get('native.php_binary_path') ?? false,
            ],
        ];
    }

    /**
     * 输出到文件
     *
     * @param array $debugInfo
     * @param Output $output
     * @return void
     */
    private function outputToFile(array $debugInfo, Output $output): void
    {
        $filePath = App::getRootPath() . 'nativephp_debug.json';
        file_put_contents($filePath, json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $output->info('调试信息已保存到 ' . $filePath);
    }

    /**
     * 输出到控制台
     *
     * @param array $debugInfo
     * @param Output $output
     * @return void
     */
    private function outputToConsole(array $debugInfo, Output $output): void
    {
        $output->writeln(print_r($debugInfo, true));
    }

    /**
     * 输出到剪贴板
     *
     * @param array $debugInfo
     * @param Output $output
     * @return void
     */
    private function outputToClipboard(array $debugInfo, Output $output): void
    {
        $json = json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // 复制 JSON 到剪贴板
        if (Environment::isWindows()) {
            shell_exec('echo ' . escapeshellarg($json) . ' | clip');
        } elseif (Environment::isLinux()) {
            shell_exec('echo ' . escapeshellarg($json) . ' | xclip -selection clipboard');
        } else {
            shell_exec('echo ' . escapeshellarg($json) . ' | pbcopy');
        }

        $output->info('调试信息已复制到剪贴板');
    }
}
