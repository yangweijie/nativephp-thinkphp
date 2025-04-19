<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Dialog as DialogFacade;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Dialog extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('dialog/index');
    }
    
    /**
     * 显示消息对话框
     *
     * @return \think\Response
     */
    public function message()
    {
        $message = input('message', '这是一个消息对话框');
        $type = input('type', 'info');
        $title = input('title', '消息');
        $buttons = input('buttons/a', ['确定']);
        $defaultId = input('default_id/d', 0);
        $cancelId = input('cancel_id/d', -1);
        
        // 创建选项
        $options = [
            'title' => $title,
            'buttons' => $buttons,
            'defaultId' => $defaultId,
            'cancelId' => $cancelId,
        ];
        
        // 显示对话框
        $result = null;
        switch ($type) {
            case 'info':
                $result = DialogFacade::info($message, $options);
                break;
            case 'error':
                $result = DialogFacade::error($message, $options);
                break;
            case 'warning':
                $result = DialogFacade::warning($message, $options);
                break;
            case 'question':
                $result = DialogFacade::question($message, $options);
                break;
            default:
                $result = DialogFacade::message($message, $options);
                break;
        }
        
        return json([
            'success' => true,
            'result' => $result,
            'button' => isset($buttons[$result]) ? $buttons[$result] : null,
        ]);
    }
    
    /**
     * 显示确认对话框
     *
     * @return \think\Response
     */
    public function confirm()
    {
        $message = input('message', '您确定要执行此操作吗？');
        $title = input('title', '确认');
        $buttons = input('buttons/a', ['取消', '确认']);
        
        // 创建选项
        $options = [
            'title' => $title,
            'buttons' => $buttons,
        ];
        
        // 显示确认对话框
        $result = DialogFacade::confirm($message, $options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示输入对话框
     *
     * @return \think\Response
     */
    public function prompt()
    {
        $message = input('message', '请输入内容：');
        $title = input('title', '输入');
        $defaultValue = input('default_value', '');
        $placeholder = input('placeholder', '');
        
        // 创建选项
        $options = [
            'title' => $title,
            'defaultValue' => $defaultValue,
            'placeholder' => $placeholder,
        ];
        
        // 显示输入对话框
        $result = DialogFacade::prompt($message, $options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示打开文件对话框
     *
     * @return \think\Response
     */
    public function openFile()
    {
        $title = input('title', '打开文件');
        $defaultPath = input('default_path', '');
        $filters = input('filters/a', [
            ['name' => '所有文件', 'extensions' => ['*']],
        ]);
        $properties = input('properties/a', []);
        $multiSelections = input('multi_selections/b', false);
        
        // 创建选项
        $options = [
            'title' => $title,
            'defaultPath' => $defaultPath,
            'filters' => $filters,
            'properties' => $properties,
            'multiSelections' => $multiSelections,
        ];
        
        // 显示打开文件对话框
        $result = DialogFacade::openFile($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示保存文件对话框
     *
     * @return \think\Response
     */
    public function saveFile()
    {
        $title = input('title', '保存文件');
        $defaultPath = input('default_path', '');
        $filters = input('filters/a', [
            ['name' => '所有文件', 'extensions' => ['*']],
        ]);
        
        // 创建选项
        $options = [
            'title' => $title,
            'defaultPath' => $defaultPath,
            'filters' => $filters,
        ];
        
        // 显示保存文件对话框
        $result = DialogFacade::saveFile($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示选择文件夹对话框
     *
     * @return \think\Response
     */
    public function selectFolder()
    {
        $title = input('title', '选择文件夹');
        $defaultPath = input('default_path', '');
        $properties = input('properties/a', []);
        
        // 创建选项
        $options = [
            'title' => $title,
            'defaultPath' => $defaultPath,
            'properties' => $properties,
        ];
        
        // 显示选择文件夹对话框
        $result = DialogFacade::selectFolder($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示颜色选择对话框
     *
     * @return \think\Response
     */
    public function color()
    {
        $title = input('title', '选择颜色');
        $defaultColor = input('default_color', '#000000');
        
        // 创建选项
        $options = [
            'title' => $title,
            'defaultColor' => $defaultColor,
        ];
        
        // 显示颜色选择对话框
        $result = DialogFacade::color($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示字体选择对话框
     *
     * @return \think\Response
     */
    public function font()
    {
        $title = input('title', '选择字体');
        
        // 创建选项
        $options = [
            'title' => $title,
        ];
        
        // 显示字体选择对话框
        $result = DialogFacade::font($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
    
    /**
     * 显示证书选择对话框
     *
     * @return \think\Response
     */
    public function certificate()
    {
        $title = input('title', '选择证书');
        
        // 创建选项
        $options = [
            'title' => $title,
        ];
        
        // 显示证书选择对话框
        $result = DialogFacade::certificate($options);
        
        return json([
            'success' => true,
            'result' => $result,
        ]);
    }
}
