<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;

class UpdaterCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:updater')
            ->setDescription('管理应用更新配置')
            ->addOption('enable', null, Option::VALUE_NONE, '启用自动更新')
            ->addOption('disable', null, Option::VALUE_NONE, '禁用自动更新')
            ->addOption('server', null, Option::VALUE_REQUIRED, '设置更新服务器地址')
            ->addOption('channel', null, Option::VALUE_REQUIRED, '设置更新通道')
            ->addOption('key', null, Option::VALUE_REQUIRED, '设置更新公钥文件路径')
            ->addOption('sign', null, Option::VALUE_REQUIRED, '为更新包生成签名')
            ->addOption('verify', null, Option::VALUE_REQUIRED, '验证更新包签名');
    }

    protected function execute(Input $input, Output $output)
    {
        // 处理启用/禁用更新
        if ($input->getOption('enable')) {
            $this->updateConfig('updater.enabled', true);
            $output->writeln('<info>已启用自动更新</info>');
        }
        
        if ($input->getOption('disable')) {
            $this->updateConfig('updater.enabled', false);
            $output->writeln('<info>已禁用自动更新</info>');
        }
        
        // 设置更新服务器
        if ($server = $input->getOption('server')) {
            $this->updateConfig('updater.server', $server);
            $output->writeln("<info>已设置更新服务器：{$server}</info>");
        }
        
        // 设置更新通道
        if ($channel = $input->getOption('channel')) {
            $this->updateConfig('updater.channel', $channel);
            $output->writeln("<info>已设置更新通道：{$channel}</info>");
        }
        
        // 设置更新公钥
        if ($keyPath = $input->getOption('key')) {
            if (!file_exists($keyPath)) {
                $output->writeln('<error>公钥文件不存在</error>');
                return 1;
            }
            
            $publicKey = file_get_contents($keyPath);
            $this->updateConfig('updater.pubkey', $publicKey);
            $output->writeln('<info>已设置更新公钥</info>');
        }
        
        // 生成更新包签名
        if ($packagePath = $input->getOption('sign')) {
            if (!file_exists($packagePath)) {
                $output->writeln('<error>更新包文件不存在</error>');
                return 1;
            }
            
            // 读取私钥
            $privateKeyPath = $this->getPrivateKeyPath();
            if (!file_exists($privateKeyPath)) {
                $output->writeln('<error>私钥文件不存在，请先生成密钥对</error>');
                return 1;
            }
            
            try {
                $signature = generate_update_signature(
                    $packagePath,
                    file_get_contents($privateKeyPath)
                );
                
                $output->writeln("更新包签名：{$signature}");
                
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return 1;
            }
        }
        
        // 验证更新包签名
        if ($packagePath = $input->getOption('verify')) {
            if (!file_exists($packagePath)) {
                $output->writeln('<error>更新包文件不存在</error>');
                return 1;
            }
            
            $signature = $this->ask($output, '请输入签名：');
            $publicKey = Config::get('native.updater.pubkey');
            
            if (!$publicKey) {
                $output->writeln('<error>未配置公钥，请先设置公钥</error>');
                return 1;
            }
            
            try {
                $result = verify_update_signature($packagePath, $signature, $publicKey);
                if ($result) {
                    $output->writeln('<info>签名验证通过</info>');
                } else {
                    $output->writeln('<error>签名验证失败</error>');
                    return 1;
                }
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return 1;
            }
        }
        
        return 0;
    }
    
    protected function updateConfig(string $key, $value): void
    {
        $config = Config::get('native');
        data_set($config, $key, $value);
        
        $content = '<?php' . PHP_EOL . PHP_EOL;
        $content .= 'return ' . var_export($config, true) . ';' . PHP_EOL;
        
        file_put_contents(
            config_path() . '/native.php',
            $content
        );
        
        // 更新运行时配置
        Config::set(['native' => $config]);
    }
    
    protected function getPrivateKeyPath(): string
    {
        return root_path('.updater/private.key');
    }
    
    protected function ask(Output $output, string $question): string
    {
        $output->write($question);
        return trim(fgets(STDIN));
    }
}