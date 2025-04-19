<?php

namespace app\controller;

use app\BaseController;
use app\service\FavoriteService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Favorite extends BaseController
{
    /**
     * 收藏夹服务
     *
     * @var \app\service\FavoriteService
     */
    protected $favoriteService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\FavoriteService $favoriteService
     * @param \app\service\LogService $logService
     */
    public function __construct(FavoriteService $favoriteService, LogService $logService)
    {
        $this->favoriteService = $favoriteService;
        $this->logService = $logService;
    }
    
    /**
     * 收藏夹列表
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $favorites = $this->favoriteService->getAll();
            
            View::assign([
                'favorites' => $favorites,
            ]);
            
            return view('favorite/index');
        } catch (\Exception $e) {
            $this->logService->error('获取收藏夹失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }
    
    /**
     * 添加收藏
     *
     * @return \think\Response
     */
    public function add()
    {
        $path = input('path');
        $name = input('name');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $this->logService->info('添加收藏', [
                'path' => $path,
                'name' => $name
            ]);
            
            $result = $this->favoriteService->add($path, $name);
            
            if ($result) {
                Notification::send('添加成功', '已添加到收藏夹');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '添加失败，文件或目录不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('添加收藏失败', [
                'path' => $path,
                'name' => $name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除收藏
     *
     * @return \think\Response
     */
    public function remove()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $this->logService->info('删除收藏', [
                'path' => $path
            ]);
            
            $result = $this->favoriteService->remove($path);
            
            if ($result) {
                Notification::send('删除成功', '已从收藏夹中删除');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '删除失败，收藏不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('删除收藏失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 重命名收藏
     *
     * @return \think\Response
     */
    public function rename()
    {
        $path = input('path');
        $newName = input('newName');
        
        if (empty($path) || empty($newName)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('重命名收藏', [
                'path' => $path,
                'newName' => $newName
            ]);
            
            $result = $this->favoriteService->rename($path, $newName);
            
            if ($result) {
                Notification::send('重命名成功', '收藏已重命名');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '重命名失败，收藏不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('重命名收藏失败', [
                'path' => $path,
                'newName' => $newName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 检查路径是否已收藏
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
            $isFavorite = $this->favoriteService->isFavorite($path);
            
            return json(['success' => true, 'isFavorite' => $isFavorite]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
