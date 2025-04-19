# 打印功能

NativePHP for ThinkPHP 提供了打印功能，允许你的桌面应用程序打印文档、生成 PDF 文件和显示打印预览。本文档将介绍如何使用这些功能。

## 基本概念

打印功能允许你的应用程序获取系统打印机列表、打印 HTML 内容、打印文件、生成 PDF 文件和显示打印预览。这些功能可以用于创建报表应用、票据打印应用、文档管理应用等。

## 配置

在使用打印功能之前，你可以在 `config/native.php` 文件中配置打印功能：

```php
return [
    // 其他配置...
    
    'printer' => [
        'default' => env('NATIVEPHP_PRINTER_DEFAULT', null),
        'temp_path' => env('NATIVEPHP_PRINTER_TEMP_PATH', runtime_path() . 'temp/print'),
    ],
];
```

## 使用 Printer Facade

NativePHP for ThinkPHP 提供了 `Printer` Facade，用于打印文档和管理打印机。

### 获取打印机

```php
use Native\ThinkPHP\Facades\Printer;

// 获取所有打印机
$printers = Printer::getPrinters();

foreach ($printers as $printer) {
    echo "打印机名称：{$printer['name']}，是否默认：" . ($printer['isDefault'] ? '是' : '否');
}

// 获取默认打印机
$defaultPrinter = Printer::getDefaultPrinter();

if ($defaultPrinter) {
    echo "默认打印机：{$defaultPrinter['name']}";
} else {
    echo "没有默认打印机";
}
```

### 打印 HTML

```php
// 打印 HTML
$success = Printer::printHtml('<h1>测试打印</h1><p>这是一个测试文档</p>');

if ($success) {
    // 打印成功
    echo "HTML 打印成功";
} else {
    // 打印失败
    echo "HTML 打印失败";
}

// 打印 HTML（带选项）
$success = Printer::printHtml('<h1>测试打印</h1><p>这是一个测试文档</p>', [
    'silent' => false, // 是否静默打印（不显示打印对话框）
    'printBackground' => true, // 是否打印背景
    'deviceName' => '', // 打印机名称，为空则使用默认打印机
    'color' => true, // 是否彩色打印
    'landscape' => false, // 是否横向打印
    'scaleFactor' => 1.0, // 缩放因子
    'pagesPerSheet' => 1, // 每张纸打印的页数
    'collate' => true, // 是否逐份打印
    'copies' => 1, // 打印份数
    'pageRanges' => [], // 打印页面范围，如 [{ from: 1, to: 5 }]
    'duplexMode' => 'simplex', // 双面打印模式：simplex（单面）、shortEdge（短边）、longEdge（长边）
    'dpi' => 300, // 打印分辨率
]);
```

### 打印文件

```php
// 打印文件
$success = Printer::printFile('/path/to/file.pdf');

if ($success) {
    // 打印成功
    echo "文件打印成功";
} else {
    // 打印失败
    echo "文件打印失败";
}

// 打印文件（带选项）
$success = Printer::printFile('/path/to/file.pdf', [
    'silent' => false,
    'printBackground' => true,
    'deviceName' => '',
    'color' => true,
    'landscape' => false,
    'scaleFactor' => 1.0,
    'pagesPerSheet' => 1,
    'collate' => true,
    'copies' => 1,
    'pageRanges' => [],
    'duplexMode' => 'simplex',
    'dpi' => 300,
]);
```

### 打印到 PDF

```php
// 打印到 PDF
$success = Printer::printToPdf('<h1>测试 PDF</h1>', '/path/to/output.pdf');

if ($success) {
    // 打印成功
    echo "PDF 生成成功：/path/to/output.pdf";
} else {
    // 打印失败
    echo "PDF 生成失败";
}

// 打印到 PDF（带选项）
$success = Printer::printToPdf('<h1>测试 PDF</h1>', '/path/to/output.pdf', [
    'marginsType' => 0, // 边距类型：0（默认）、1（无边距）、2（最小边距）
    'pageSize' => 'A4', // 页面大小：A4、A3、Letter 等
    'printBackground' => true, // 是否打印背景
    'printSelectionOnly' => false, // 是否只打印选中内容
    'landscape' => false, // 是否横向打印
]);
```

### 显示打印预览

```php
// 显示打印预览
$success = Printer::showPrintPreview('<h1>测试预览</h1>');

if ($success) {
    // 预览成功
    echo "打印预览已显示";
} else {
    // 预览失败
    echo "打印预览显示失败";
}

// 显示打印预览（带选项）
$success = Printer::showPrintPreview('<h1>测试预览</h1>', [
    'printBackground' => true,
    'landscape' => false,
]);
```

## 打印选项

### HTML 打印选项

- `silent`：是否静默打印（不显示打印对话框），默认为 `false`
- `printBackground`：是否打印背景，默认为 `true`
- `deviceName`：打印机名称，为空则使用默认打印机
- `color`：是否彩色打印，默认为 `true`
- `landscape`：是否横向打印，默认为 `false`
- `scaleFactor`：缩放因子，默认为 `1.0`
- `pagesPerSheet`：每张纸打印的页数，默认为 `1`
- `collate`：是否逐份打印，默认为 `true`
- `copies`：打印份数，默认为 `1`
- `pageRanges`：打印页面范围，如 `[{ from: 1, to: 5 }]`，默认为 `[]`（全部页面）
- `duplexMode`：双面打印模式，可选值为 `simplex`（单面）、`shortEdge`（短边）、`longEdge`（长边），默认为 `simplex`
- `dpi`：打印分辨率，默认为 `300`

### PDF 打印选项

- `marginsType`：边距类型，可选值为 `0`（默认）、`1`（无边距）、`2`（最小边距），默认为 `0`
- `pageSize`：页面大小，可选值为 `A4`、`A3`、`Letter` 等，默认为 `A4`
- `printBackground`：是否打印背景，默认为 `true`
- `printSelectionOnly`：是否只打印选中内容，默认为 `false`
- `landscape`：是否横向打印，默认为 `false`

## 打印机对象格式

```php
[
    'name' => 'Printer Name', // 打印机名称
    'description' => 'Printer Description', // 打印机描述
    'status' => 0, // 打印机状态
    'isDefault' => true, // 是否为默认打印机
    'options' => [ // 打印机选项
        'media' => 'A4',
        'color' => true,
        'duplex' => false,
    ],
]
```

## 实际应用场景

### 报表打印应用

```php
use Native\ThinkPHP\Facades\Printer;
use Native\ThinkPHP\Facades\Notification;

class ReportController
{
    /**
     * 生成并打印报表
     *
     * @param string $type 报表类型
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return \think\Response
     */
    public function print($type, $startDate, $endDate)
    {
        // 获取报表数据
        $data = $this->getReportData($type, $startDate, $endDate);
        
        // 生成报表 HTML
        $html = $this->generateReportHtml($type, $data, $startDate, $endDate);
        
        // 打印报表
        $success = Printer::printHtml($html, [
            'silent' => false,
            'printBackground' => true,
            'landscape' => $type === 'summary',
        ]);
        
        if ($success) {
            Notification::send('报表打印', '报表已发送到打印机');
            
            return json(['success' => true, 'message' => '报表已发送到打印机']);
        } else {
            return json(['success' => false, 'message' => '报表打印失败']);
        }
    }
    
    /**
     * 生成并导出报表为 PDF
     *
     * @param string $type 报表类型
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return \think\Response
     */
    public function exportPdf($type, $startDate, $endDate)
    {
        // 获取报表数据
        $data = $this->getReportData($type, $startDate, $endDate);
        
        // 生成报表 HTML
        $html = $this->generateReportHtml($type, $data, $startDate, $endDate);
        
        // 生成 PDF 文件名
        $filename = $type . '_' . $startDate . '_' . $endDate . '.pdf';
        $outputPath = runtime_path() . 'temp/' . $filename;
        
        // 打印到 PDF
        $success = Printer::printToPdf($html, $outputPath, [
            'landscape' => $type === 'summary',
        ]);
        
        if ($success) {
            Notification::send('报表导出', '报表已导出为 PDF');
            
            return download($outputPath, $filename);
        } else {
            return json(['success' => false, 'message' => '报表导出失败']);
        }
    }
    
    /**
     * 预览报表
     *
     * @param string $type 报表类型
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return \think\Response
     */
    public function preview($type, $startDate, $endDate)
    {
        // 获取报表数据
        $data = $this->getReportData($type, $startDate, $endDate);
        
        // 生成报表 HTML
        $html = $this->generateReportHtml($type, $data, $startDate, $endDate);
        
        // 显示打印预览
        $success = Printer::showPrintPreview($html, [
            'landscape' => $type === 'summary',
        ]);
        
        if ($success) {
            return json(['success' => true, 'message' => '报表预览已显示']);
        } else {
            return json(['success' => false, 'message' => '报表预览显示失败']);
        }
    }
    
    /**
     * 获取报表数据
     *
     * @param string $type 报表类型
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return array
     */
    protected function getReportData($type, $startDate, $endDate)
    {
        // 根据报表类型获取数据
        switch ($type) {
            case 'sales':
                return $this->getSalesData($startDate, $endDate);
            case 'inventory':
                return $this->getInventoryData($startDate, $endDate);
            case 'summary':
                return $this->getSummaryData($startDate, $endDate);
            default:
                return [];
        }
    }
    
    /**
     * 生成报表 HTML
     *
     * @param string $type 报表类型
     * @param array $data 报表数据
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return string
     */
    protected function generateReportHtml($type, $data, $startDate, $endDate)
    {
        // 根据报表类型生成 HTML
        switch ($type) {
            case 'sales':
                return $this->generateSalesReportHtml($data, $startDate, $endDate);
            case 'inventory':
                return $this->generateInventoryReportHtml($data, $startDate, $endDate);
            case 'summary':
                return $this->generateSummaryReportHtml($data, $startDate, $endDate);
            default:
                return '';
        }
    }
    
    // 其他方法...
}
```

### 票据打印应用

```php
use Native\ThinkPHP\Facades\Printer;
use Native\ThinkPHP\Facades\Notification;

class ReceiptController
{
    /**
     * 打印票据
     *
     * @param int $orderId 订单 ID
     * @return \think\Response
     */
    public function print($orderId)
    {
        // 获取订单信息
        $order = $this->getOrder($orderId);
        
        if (!$order) {
            return json(['success' => false, 'message' => '订单不存在']);
        }
        
        // 生成票据 HTML
        $html = $this->generateReceiptHtml($order);
        
        // 获取所有打印机
        $printers = Printer::getPrinters();
        
        // 查找票据打印机
        $receiptPrinter = null;
        foreach ($printers as $printer) {
            if (strpos($printer['name'], 'Receipt') !== false || strpos($printer['name'], '票据') !== false) {
                $receiptPrinter = $printer;
                break;
            }
        }
        
        // 打印票据
        $success = Printer::printHtml($html, [
            'silent' => true,
            'deviceName' => $receiptPrinter ? $receiptPrinter['name'] : '',
            'copies' => $order['copies'] ?? 1,
        ]);
        
        if ($success) {
            // 更新订单打印状态
            $this->updateOrderPrintStatus($orderId);
            
            Notification::send('票据打印', '订单 #' . $orderId . ' 的票据已打印');
            
            return json(['success' => true, 'message' => '票据已打印']);
        } else {
            return json(['success' => false, 'message' => '票据打印失败']);
        }
    }
    
    /**
     * 生成票据 HTML
     *
     * @param array $order 订单信息
     * @return string
     */
    protected function generateReceiptHtml($order)
    {
        // 生成票据 HTML
        $html = '
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                    margin: 0;
                    padding: 0;
                }
                .receipt {
                    width: 80mm;
                    padding: 5mm;
                }
                .header {
                    text-align: center;
                    margin-bottom: 5mm;
                }
                .items {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 5mm;
                }
                .items th, .items td {
                    text-align: left;
                    padding: 1mm;
                }
                .total {
                    text-align: right;
                    margin-top: 5mm;
                }
                .footer {
                    text-align: center;
                    margin-top: 5mm;
                    font-size: 8pt;
                }
            </style>
            <div class="receipt">
                <div class="header">
                    <h1>' . config('native.name') . '</h1>
                    <p>订单号：' . $order['id'] . '</p>
                    <p>日期：' . date('Y-m-d H:i:s', $order['create_time']) . '</p>
                    <p>客户：' . $order['customer_name'] . '</p>
                </div>
                <table class="items">
                    <tr>
                        <th>商品</th>
                        <th>数量</th>
                        <th>单价</th>
                        <th>小计</th>
                    </tr>';
        
        foreach ($order['items'] as $item) {
            $html .= '
                    <tr>
                        <td>' . $item['name'] . '</td>
                        <td>' . $item['quantity'] . '</td>
                        <td>￥' . number_format($item['price'], 2) . '</td>
                        <td>￥' . number_format($item['quantity'] * $item['price'], 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                </table>
                <div class="total">
                    <p>总计：￥' . number_format($order['total_amount'], 2) . '</p>
                    <p>支付方式：' . $order['payment_method'] . '</p>
                </div>
                <div class="footer">
                    <p>感谢您的惠顾，欢迎再次光临！</p>
                </div>
            </div>';
        
        return $html;
    }
    
    /**
     * 获取订单信息
     *
     * @param int $orderId 订单 ID
     * @return array|null
     */
    protected function getOrder($orderId)
    {
        // 获取订单信息
        // 这里应该从数据库中获取订单信息
        // 这里只是示例
        return [
            'id' => $orderId,
            'create_time' => time(),
            'customer_name' => '张三',
            'items' => [
                [
                    'name' => '商品 1',
                    'quantity' => 2,
                    'price' => 10.00,
                ],
                [
                    'name' => '商品 2',
                    'quantity' => 1,
                    'price' => 20.00,
                ],
            ],
            'total_amount' => 40.00,
            'payment_method' => '微信支付',
            'copies' => 1,
        ];
    }
    
    /**
     * 更新订单打印状态
     *
     * @param int $orderId 订单 ID
     * @return bool
     */
    protected function updateOrderPrintStatus($orderId)
    {
        // 更新订单打印状态
        // 这里应该更新数据库中的订单打印状态
        // 这里只是示例
        return true;
    }
}
```

## 最佳实践

1. **打印机选择**：在打印前，检查可用的打印机，并根据打印内容选择合适的打印机。

2. **打印选项**：根据打印内容设置合适的打印选项，如纸张大小、方向、颜色等。

3. **打印预览**：在打印前，提供打印预览功能，让用户确认打印内容。

4. **错误处理**：妥善处理打印过程中可能出现的错误，提供友好的错误信息和备选方案。

5. **打印样式**：使用 CSS 样式控制打印输出的外观，确保打印结果美观、清晰。

6. **打印优化**：优化打印内容，减少不必要的元素，节约纸张和墨水。

7. **打印记录**：记录打印历史，包括打印时间、内容、打印机等信息，方便后续查询和统计。

## 故障排除

### 打印机不可用

- 确保打印机已连接并开启
- 检查打印机驱动是否已安装
- 尝试重启打印机
- 检查打印机状态和错误信息

### 打印内容不正确

- 检查 HTML 内容是否正确
- 检查 CSS 样式是否适合打印
- 尝试使用打印预览功能查看打印效果
- 调整打印选项，如纸张大小、方向等

### PDF 生成失败

- 确保输出目录存在并可写
- 检查 HTML 内容是否包含不支持的元素
- 尝试使用不同的 PDF 选项
- 检查磁盘空间是否足够

### 打印速度慢

- 减少打印内容的复杂度
- 优化 HTML 和 CSS
- 减少图片和背景
- 使用更高效的打印机
