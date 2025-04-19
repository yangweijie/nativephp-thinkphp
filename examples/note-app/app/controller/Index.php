<?php

namespace app\controller;

use app\BaseController;
use app\service\NoteService;
use Native\ThinkPHP\Facades\App;

class Index extends BaseController
{
    protected $noteService;
    
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
        
        // 初始化数据库
        $this->noteService->initDatabase();
    }
    
    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('index/index', [
            'appName' => App::name(),
            'appVersion' => App::version(),
        ]);
    }
}
