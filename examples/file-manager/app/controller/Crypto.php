<?php

namespace app\controller;

use app\BaseController;
use app\service\CryptoService;
use app\service\LogService;
use think\facade\View;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;

class Crypto extends BaseController
{
    /**
     * 加密服务
     *
     * @var \app\service\CryptoService
     */
    protected $cryptoService;

    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;

    /**
     * 构造函数
     *
     * @param \app\service\CryptoService $cryptoService
     * @param \app\service\LogService $logService
     */
    public function __construct(CryptoService $cryptoService, LogService $logService)
    {
        $this->cryptoService = $cryptoService;
        $this->logService = $logService;
    }

    /**
     * 加密文件
     *
     * @return \think\Response
     */
    public function encrypt()
    {
        $source = input('source');

        if (empty($source)) {
            return json(['success' => false, 'message' => '源文件不能为空']);
        }

        if (!FileSystem::exists($source) || is_dir($source)) {
            return json(['success' => false, 'message' => '源文件不存在或是一个目录']);
        }

        try {
            // 获取支持的加密算法
            $algorithms = $this->cryptoService->getSupportedAlgorithms();
            
            if (empty($algorithms)) {
                return json(['success' => false, 'message' => '当前系统不支持任何加密算法']);
            }

            // 显示加密选项对话框
            $html = $this->buildEncryptionOptionsHtml($algorithms, $source);
            
            return json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            $this->logService->error('获取加密选项失败', [
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 执行文件加密
     *
     * @return \think\Response
     */
    public function doEncrypt()
    {
        $source = input('source');
        $password = input('password');
        $algorithm = input('algorithm', 'aes-256-cbc');
        $saveAs = input('save_as', true);

        if (empty($source) || empty($password)) {
            return json(['success' => false, 'message' => '源文件和密码不能为空']);
        }

        if (!FileSystem::exists($source) || is_dir($source)) {
            return json(['success' => false, 'message' => '源文件不存在或是一个目录']);
        }

        try {
            // 确定目标文件路径
            $destination = $source . '.encrypted';
            
            if ($saveAs) {
                // 选择保存位置
                $defaultPath = $destination;
                
                $selectedPath = Dialog::saveFile([
                    'title' => '保存加密文件',
                    'defaultPath' => $defaultPath,
                    'filters' => [
                        ['name' => '加密文件', 'extensions' => ['encrypted']],
                        ['name' => '所有文件', 'extensions' => ['*']],
                    ],
                ]);

                if (!$selectedPath) {
                    return json(['success' => false, 'message' => '未选择保存位置']);
                }
                
                $destination = $selectedPath;
            }

            $this->logService->info('加密文件', [
                'source' => $source,
                'destination' => $destination,
                'algorithm' => $algorithm
            ]);

            // 执行加密
            $result = $this->cryptoService->encryptFile($source, $destination, $password, $algorithm);

            if ($result) {
                Notification::send('加密成功', '文件已成功加密到: ' . $destination);
                return json(['success' => true, 'path' => $destination]);
            } else {
                return json(['success' => false, 'message' => '加密失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('加密文件失败', [
                'source' => $source,
                'algorithm' => $algorithm,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 解密文件
     *
     * @return \think\Response
     */
    public function decrypt()
    {
        $source = input('source');

        if (empty($source)) {
            return json(['success' => false, 'message' => '源文件不能为空']);
        }

        if (!FileSystem::exists($source) || is_dir($source)) {
            return json(['success' => false, 'message' => '源文件不存在或是一个目录']);
        }

        // 检查文件是否为加密文件
        if (!$this->cryptoService->isEncrypted($source)) {
            return json(['success' => false, 'message' => '所选文件不是有效的加密文件']);
        }

        try {
            // 获取加密文件信息
            $info = $this->cryptoService->getEncryptedFileInfo($source);
            
            if (!$info) {
                return json(['success' => false, 'message' => '无法读取加密文件信息']);
            }

            // 显示解密选项对话框
            $html = $this->buildDecryptionOptionsHtml($info, $source);
            
            return json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            $this->logService->error('获取解密选项失败', [
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 执行文件解密
     *
     * @return \think\Response
     */
    public function doDecrypt()
    {
        $source = input('source');
        $password = input('password');
        $saveAs = input('save_as', true);

        if (empty($source) || empty($password)) {
            return json(['success' => false, 'message' => '源文件和密码不能为空']);
        }

        if (!FileSystem::exists($source) || is_dir($source)) {
            return json(['success' => false, 'message' => '源文件不存在或是一个目录']);
        }

        // 检查文件是否为加密文件
        if (!$this->cryptoService->isEncrypted($source)) {
            return json(['success' => false, 'message' => '所选文件不是有效的加密文件']);
        }

        try {
            // 确定目标文件路径
            $destination = preg_replace('/\.encrypted$/', '', $source);
            
            if ($saveAs) {
                // 选择保存位置
                $defaultPath = $destination;
                
                $selectedPath = Dialog::saveFile([
                    'title' => '保存解密文件',
                    'defaultPath' => $defaultPath,
                ]);

                if (!$selectedPath) {
                    return json(['success' => false, 'message' => '未选择保存位置']);
                }
                
                $destination = $selectedPath;
            }

            $this->logService->info('解密文件', [
                'source' => $source,
                'destination' => $destination
            ]);

            // 执行解密
            $result = $this->cryptoService->decryptFile($source, $destination, $password);

            if ($result) {
                Notification::send('解密成功', '文件已成功解密到: ' . $destination);
                return json(['success' => true, 'path' => $destination]);
            } else {
                return json(['success' => false, 'message' => '解密失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('解密文件失败', [
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 检查文件是否为加密文件
     *
     * @return \think\Response
     */
    public function check()
    {
        $path = input('path');

        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }

        try {
            $isEncrypted = $this->cryptoService->isEncrypted($path);
            $info = $isEncrypted ? $this->cryptoService->getEncryptedFileInfo($path) : null;

            return json([
                'success' => true,
                'isEncrypted' => $isEncrypted,
                'info' => $info
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 评估密码强度
     *
     * @return \think\Response
     */
    public function evaluatePassword()
    {
        $password = input('password');

        if (empty($password)) {
            return json(['success' => false, 'message' => '密码不能为空']);
        }

        try {
            $evaluation = $this->cryptoService->evaluatePasswordStrength($password);
            return json(['success' => true, 'evaluation' => $evaluation]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 生成随机密码
     *
     * @return \think\Response
     */
    public function generatePassword()
    {
        $length = input('length', 16);

        try {
            $password = $this->cryptoService->generateRandomPassword($length);
            return json(['success' => true, 'password' => $password]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 构建加密选项 HTML
     *
     * @param array $algorithms 支持的加密算法
     * @param string $source 源文件路径
     * @return string HTML 内容
     */
    protected function buildEncryptionOptionsHtml($algorithms, $source)
    {
        $algorithmOptions = '';
        foreach ($algorithms as $algo => $name) {
            $algorithmOptions .= '<option value="' . $algo . '">' . $name . '</option>';
        }

        $randomPassword = $this->cryptoService->generateRandomPassword();

        return <<<HTML
<div class="encryption-options">
    <p>请为文件 <strong>{$source}</strong> 设置加密选项：</p>
    
    <div class="form-group">
        <label for="encryptionPassword">密码：</label>
        <div class="password-input-group">
            <input type="password" id="encryptionPassword" class="form-control" value="{$randomPassword}">
            <button type="button" onclick="togglePasswordVisibility('encryptionPassword')" class="btn">显示</button>
            <button type="button" onclick="generatePassword()" class="btn">生成</button>
        </div>
    </div>
    
    <div class="form-group">
        <div class="password-strength" id="passwordStrength">
            <div class="strength-bar">
                <div class="strength-indicator" style="width: 0%"></div>
            </div>
            <div class="strength-text">密码强度: <span>未评估</span></div>
        </div>
        <div class="password-suggestions" id="passwordSuggestions"></div>
    </div>
    
    <div class="form-group">
        <label for="encryptionAlgorithm">加密算法：</label>
        <select id="encryptionAlgorithm" class="form-control">
            {$algorithmOptions}
        </select>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" id="saveAsOption" checked>
            选择保存位置
        </label>
    </div>
    
    <input type="hidden" id="sourceFile" value="{$source}">
</div>

<script>
    // 初始化密码强度评估
    document.getElementById('encryptionPassword').addEventListener('input', function() {
        evaluatePassword(this.value);
    });
    
    // 初始评估
    evaluatePassword(document.getElementById('encryptionPassword').value);
    
    // 评估密码强度
    function evaluatePassword(password) {
        fetch('/crypto/evaluatePassword', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'password=' + encodeURIComponent(password),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePasswordStrengthUI(data.evaluation);
            }
        })
        .catch(error => {
            console.error('Error evaluating password:', error);
        });
    }
    
    // 更新密码强度 UI
    function updatePasswordStrengthUI(evaluation) {
        const strengthBar = document.querySelector('#passwordStrength .strength-indicator');
        const strengthText = document.querySelector('#passwordStrength .strength-text span');
        const suggestionsContainer = document.getElementById('passwordSuggestions');
        
        // 更新强度条
        strengthBar.style.width = evaluation.score + '%';
        
        // 更新强度文本
        let strengthLabel = '弱';
        let barColor = '#ff4d4d';
        
        switch (evaluation.strength) {
            case 'medium':
                strengthLabel = '中等';
                barColor = '#ffaa00';
                break;
            case 'strong':
                strengthLabel = '强';
                barColor = '#2ecc71';
                break;
            case 'very-strong':
                strengthLabel = '非常强';
                barColor = '#27ae60';
                break;
        }
        
        strengthBar.style.backgroundColor = barColor;
        strengthText.textContent = strengthLabel + ' (' + evaluation.score + '/100)';
        
        // 更新建议
        if (evaluation.suggestions.length > 0) {
            let suggestionsHtml = '<ul>';
            evaluation.suggestions.forEach(suggestion => {
                suggestionsHtml += '<li>' + suggestion + '</li>';
            });
            suggestionsHtml += '</ul>';
            suggestionsContainer.innerHTML = suggestionsHtml;
        } else {
            suggestionsContainer.innerHTML = '';
        }
    }
    
    // 生成随机密码
    function generatePassword() {
        fetch('/crypto/generatePassword', {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('encryptionPassword').value = data.password;
                evaluatePassword(data.password);
            }
        })
        .catch(error => {
            console.error('Error generating password:', error);
        });
    }
    
    // 切换密码可见性
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '隐藏';
        } else {
            input.type = 'password';
            button.textContent = '显示';
        }
    }
</script>
HTML;
    }

    /**
     * 构建解密选项 HTML
     *
     * @param array $info 加密文件信息
     * @param string $source 源文件路径
     * @return string HTML 内容
     */
    protected function buildDecryptionOptionsHtml($info, $source)
    {
        $algorithm = $info['algorithm'] ?? '未知';
        $version = $info['version'] ?? '未知';

        return <<<HTML
<div class="decryption-options">
    <p>请输入密码解密文件 <strong>{$source}</strong>：</p>
    
    <div class="file-info">
        <p><strong>加密算法:</strong> {$algorithm}</p>
        <p><strong>加密版本:</strong> {$version}</p>
    </div>
    
    <div class="form-group">
        <label for="decryptionPassword">密码：</label>
        <div class="password-input-group">
            <input type="password" id="decryptionPassword" class="form-control">
            <button type="button" onclick="togglePasswordVisibility('decryptionPassword')" class="btn">显示</button>
        </div>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" id="saveAsOption" checked>
            选择保存位置
        </label>
    </div>
    
    <input type="hidden" id="sourceFile" value="{$source}">
</div>

<script>
    // 切换密码可见性
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '隐藏';
        } else {
            input.type = 'password';
            button.textContent = '显示';
        }
    }
</script>
HTML;
    }
}
