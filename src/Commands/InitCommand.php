<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class InitCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:init';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '初始化 NativePHP 应用';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int|null
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('正在初始化 NativePHP 应用...');

        // 创建配置文件
        $this->createConfigFile($output);

        // 创建示例控制器
        $this->createExampleController($output);

        $output->writeln('<info>NativePHP 应用初始化完成！</info>');
        $output->writeln('<info>使用 php think native:serve 启动应用</info>');

        return 0;
    }

    /**
     * 创建配置文件
     *
     * @param Output $output
     * @return void
     */
    protected function createConfigFile(Output $output)
    {
        $configPath = App::getConfigPath() . 'native.php';

        if (file_exists($configPath)) {
            $output->writeln('<comment>配置文件已存在，跳过创建。</comment>');
            return;
        }

        $content = file_get_contents(__DIR__ . '/../../config/native.php');
        file_put_contents($configPath, $content);

        $output->writeln('<info>配置文件创建成功！</info>');
    }

    /**
     * 创建示例控制器
     *
     * @param Output $output
     * @return void
     */
    protected function createExampleController(Output $output)
    {
        $controllerPath = App::getAppPath() . 'controller/Native.php';

        if (file_exists($controllerPath)) {
            $output->writeln('<comment>示例控制器已存在，跳过创建。</comment>');
            return;
        }

        $content = <<<'EOT'
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class Native extends BaseController
{
    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('native/index', [
            'appName' => App::name(),
            'appVersion' => App::version(),
        ]);
    }

    /**
     * 发送通知
     *
     * @return \think\Response
     */
    public function notify()
    {
        Notification::send('NativePHP', '这是一条来自 NativePHP 的通知！');

        return json(['message' => '通知已发送']);
    }

    /**
     * 打开新窗口
     *
     * @return \think\Response
     */
    public function openWindow()
    {
        Window::open('/native/about', [
            'title' => '关于',
            'width' => 400,
            'height' => 300,
        ]);

        return json(['message' => '窗口已打开']);
    }

    /**
     * 关于页面
     *
     * @return \think\Response
     */
    public function about()
    {
        return view('native/about');
    }
}
EOT;

        // 创建控制器目录
        if (!is_dir(dirname($controllerPath))) {
            mkdir(dirname($controllerPath), 0755, true);
        }

        file_put_contents($controllerPath, $content);

        $output->writeln('<info>示例控制器创建成功！</info>');

        // 创建视图目录和文件
        $this->createExampleViews($output);
    }

    /**
     * 创建示例视图
     *
     * @param Output $output
     * @return void
     */
    protected function createExampleViews(Output $output)
    {
        $viewPath = App::getRootPath() . 'view/native/';

        if (!is_dir($viewPath)) {
            mkdir($viewPath, 0755, true);
        }

        // 创建首页视图
        $indexViewPath = $viewPath . 'index.html';
        if (!file_exists($indexViewPath)) {
            $content = <<<'EOT'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$appName}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        .buttons {
            margin-top: 20px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>欢迎使用 {$appName}</h1>
        <p>版本: {$appVersion}</p>

        <div class="buttons">
            <button onclick="sendNotification()">发送通知</button>
            <button onclick="openNewWindow()">打开新窗口</button>
        </div>
    </div>

    <script>
        function sendNotification() {
            fetch('/native/notify')
                .then(response => response.json())
                .then(data => console.log(data));
        }

        function openNewWindow() {
            fetch('/native/openWindow')
                .then(response => response.json())
                .then(data => console.log(data));
        }
    </script>
</body>
</html>
EOT;
            file_put_contents($indexViewPath, $content);
        }

        // 创建关于页面视图
        $aboutViewPath = $viewPath . 'about.html';
        if (!file_exists($aboutViewPath)) {
            $content = <<<'EOT'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>关于</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>关于 NativePHP</h1>
        <p>NativePHP 是一个用于构建桌面应用的 PHP 框架。</p>
        <p>基于 ThinkPHP 构建。</p>
    </div>
</body>
</html>
EOT;
            file_put_contents($aboutViewPath, $content);
        }

        $output->writeln('<info>示例视图创建成功！</info>');
    }
}
