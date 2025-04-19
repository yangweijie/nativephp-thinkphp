<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Printer;

class PrinterController
{
    /**
     * 获取所有打印机
     *
     * @return Response
     */
    public function getPrinters()
    {
        // 获取所有打印机
        $printers = Printer::getPrinters();
        
        return json([
            'printers' => $printers,
        ]);
    }
    
    /**
     * 获取默认打印机
     *
     * @return Response
     */
    public function getDefaultPrinter()
    {
        // 获取默认打印机
        $printer = Printer::getDefaultPrinter();
        
        return json([
            'printer' => $printer,
        ]);
    }
    
    /**
     * 打印 HTML
     *
     * @param Request $request
     * @return Response
     */
    public function printHtml(Request $request)
    {
        $html = $request->param('html');
        $options = $request->param('options', []);
        
        // 打印 HTML
        $success = Printer::printHtml($html, $options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 打印文件
     *
     * @param Request $request
     * @return Response
     */
    public function printFile(Request $request)
    {
        $filePath = $request->param('file_path');
        $options = $request->param('options', []);
        
        // 打印文件
        $success = Printer::printFile($filePath, $options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 打印到 PDF
     *
     * @param Request $request
     * @return Response
     */
    public function printToPdf(Request $request)
    {
        $html = $request->param('html');
        $outputPath = $request->param('output_path');
        $options = $request->param('options', []);
        
        // 打印到 PDF
        $success = Printer::printToPdf($html, $outputPath, $options);
        
        return json([
            'success' => $success,
            'output_path' => $outputPath,
        ]);
    }
    
    /**
     * 显示打印预览
     *
     * @param Request $request
     * @return Response
     */
    public function showPrintPreview(Request $request)
    {
        $html = $request->param('html');
        $options = $request->param('options', []);
        
        // 显示打印预览
        $success = Printer::showPrintPreview($html, $options);
        
        return json([
            'success' => $success,
        ]);
    }
}
