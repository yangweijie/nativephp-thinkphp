# 打印功能示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的打印功能，包括：

1. 打印（Printing）功能
2. 二维码/条形码（QR/Barcode）功能
3. 硬件加速（Hardware Acceleration）功能
4. 深度链接（Deep Linking）功能
5. 应用内购买（In-App Purchase）功能

## 功能

- 打印：打印 HTML 内容、文件和 PDF
- 二维码/条形码：生成和扫描二维码和条形码
- 硬件加速：启用和禁用硬件加速
- 深度链接：处理自定义 URL 协议
- 应用内购买：实现应用内购买功能

## 文件结构

- `app/controller/PrintingController.php` - 主控制器
- `view/printing/index.html` - 主页面
- `view/printing/print.html` - 打印页面
- `view/printing/qrcode.html` - 二维码/条形码页面
- `view/printing/hardware.html` - 硬件加速页面
- `view/printing/deeplink.html` - 深度链接页面
- `view/printing/purchase.html` - 应用内购买页面

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 实现说明

本示例使用 NativePHP for ThinkPHP 的以下功能：

- **Printer**：用于打印 HTML 内容、文件和 PDF
- **QRCode**：用于生成和扫描二维码和条形码
- **Window**：用于启用和禁用硬件加速
- **App**：用于处理自定义 URL 协议
- **Settings**：用于存储应用内购买信息
