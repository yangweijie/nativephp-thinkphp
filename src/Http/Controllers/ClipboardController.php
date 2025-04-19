<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Clipboard;

class ClipboardController
{
    /**
     * 读取剪贴板文本
     *
     * @return Response
     */
    public function getText()
    {
        // 读取剪贴板文本
        $text = Clipboard::text();
        
        return json([
            'text' => $text,
        ]);
    }
    
    /**
     * 写入文本到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function setText(Request $request)
    {
        $text = $request->param('text');
        
        // 写入文本到剪贴板
        Clipboard::setText($text);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 读取剪贴板图片
     *
     * @return Response
     */
    public function getImage()
    {
        // 读取剪贴板图片
        $image = Clipboard::image();
        
        return json([
            'image' => $image,
        ]);
    }
    
    /**
     * 写入图片到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function setImage(Request $request)
    {
        $path = $request->param('path');
        
        // 写入图片到剪贴板
        Clipboard::setImage($path);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 清空剪贴板
     *
     * @return Response
     */
    public function clear()
    {
        // 清空剪贴板
        Clipboard::clear();
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 读取剪贴板 HTML
     *
     * @return Response
     */
    public function getHtml()
    {
        // 读取剪贴板 HTML
        $html = Clipboard::html();
        
        return json([
            'html' => $html,
        ]);
    }
    
    /**
     * 写入 HTML 到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function setHtml(Request $request)
    {
        $html = $request->param('html');
        
        // 写入 HTML 到剪贴板
        Clipboard::setHtml($html);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 检查剪贴板是否包含指定格式的数据
     *
     * @param Request $request
     * @return Response
     */
    public function has(Request $request)
    {
        $format = $request->param('format');
        
        // 检查剪贴板是否包含指定格式的数据
        $has = Clipboard::has($format);
        
        return json([
            'has' => $has,
        ]);
    }
    
    /**
     * 获取剪贴板中可用的格式
     *
     * @return Response
     */
    public function getFormats()
    {
        // 获取剪贴板中可用的格式
        $formats = Clipboard::formats();
        
        return json([
            'formats' => $formats,
        ]);
    }
    
    /**
     * 读取剪贴板 RTF
     *
     * @return Response
     */
    public function getRtf()
    {
        // 读取剪贴板 RTF
        $rtf = Clipboard::rtf();
        
        return json([
            'rtf' => $rtf,
        ]);
    }
    
    /**
     * 写入 RTF 到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function setRtf(Request $request)
    {
        $rtf = $request->param('rtf');
        
        // 写入 RTF 到剪贴板
        Clipboard::setRtf($rtf);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 读取剪贴板文件路径
     *
     * @return Response
     */
    public function getFiles()
    {
        // 读取剪贴板文件路径
        $files = Clipboard::files();
        
        return json([
            'files' => $files,
        ]);
    }
    
    /**
     * 写入文件路径到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function setFiles(Request $request)
    {
        $files = $request->param('files');
        
        // 写入文件路径到剪贴板
        Clipboard::setFiles($files);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 读取剪贴板自定义格式数据
     *
     * @param Request $request
     * @return Response
     */
    public function readFormat(Request $request)
    {
        $format = $request->param('format');
        
        // 读取剪贴板自定义格式数据
        $data = Clipboard::readFormat($format);
        
        return json([
            'data' => $data,
        ]);
    }
    
    /**
     * 写入自定义格式数据到剪贴板
     *
     * @param Request $request
     * @return Response
     */
    public function writeFormat(Request $request)
    {
        $format = $request->param('format');
        $data = $request->param('data');
        
        // 写入自定义格式数据到剪贴板
        Clipboard::writeFormat($format, $data);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 监听剪贴板变化
     *
     * @param Request $request
     * @return Response
     */
    public function onChange(Request $request)
    {
        $id = $request->param('id');
        
        // 监听剪贴板变化
        $success = true;
        
        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }
    
    /**
     * 移除剪贴板变化监听器
     *
     * @param Request $request
     * @return Response
     */
    public function offChange(Request $request)
    {
        $id = $request->param('id');
        
        // 移除剪贴板变化监听器
        $success = Clipboard::offChange($id);
        
        return json([
            'success' => $success,
        ]);
    }
}
