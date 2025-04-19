<?php

namespace app\controller;

use app\BaseController;
use app\service\AuthService;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class Auth extends BaseController
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    public function login()
    {
        // 如果已经登录，跳转到首页
        if ($this->authService->isLoggedIn()) {
            return redirect('/');
        }
        
        return view('auth/login');
    }
    
    public function doLogin()
    {
        $username = input('username');
        $password = input('password');
        $remember = input('remember', false);
        
        if (empty($username) || empty($password)) {
            return json(['success' => false, 'message' => '用户名和密码不能为空']);
        }
        
        $result = $this->authService->login($username, $password, $remember);
        
        if ($result['success']) {
            // 登录成功，跳转到首页
            Notification::send('登录成功', '欢迎回来，' . $result['user']['name']);
            
            return json(['success' => true, 'redirect' => '/']);
        } else {
            // 登录失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
    
    public function register()
    {
        // 如果已经登录，跳转到首页
        if ($this->authService->isLoggedIn()) {
            return redirect('/');
        }
        
        return view('auth/register');
    }
    
    public function doRegister()
    {
        $username = input('username');
        $password = input('password');
        $confirmPassword = input('confirm_password');
        $email = input('email');
        $name = input('name');
        
        if (empty($username) || empty($password) || empty($confirmPassword) || empty($email) || empty($name)) {
            return json(['success' => false, 'message' => '所有字段都不能为空']);
        }
        
        if ($password !== $confirmPassword) {
            return json(['success' => false, 'message' => '两次输入的密码不一致']);
        }
        
        $result = $this->authService->register([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'name' => $name,
        ]);
        
        if ($result['success']) {
            // 注册成功，跳转到登录页面
            Notification::send('注册成功', '请登录您的账号');
            
            return json(['success' => true, 'redirect' => '/auth/login']);
        } else {
            // 注册失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
    
    public function logout()
    {
        $this->authService->logout();
        
        // 清除设置
        Settings::forget('auth.token');
        Settings::forget('auth.user');
        
        // 关闭所有窗口
        $windows = Window::all();
        foreach ($windows as $window) {
            $window->close();
        }
        
        // 打开登录窗口
        Window::open('/auth/login', [
            'title' => '登录',
            'width' => 400,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function profile()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取用户信息
        $user = $this->authService->getCurrentUser();
        
        return view('auth/profile', [
            'user' => $user,
        ]);
    }
    
    public function updateProfile()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $name = input('name');
        $email = input('email');
        $avatar = input('avatar');
        $bio = input('bio');
        
        if (empty($name) || empty($email)) {
            return json(['success' => false, 'message' => '姓名和邮箱不能为空']);
        }
        
        $result = $this->authService->updateProfile([
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar,
            'bio' => $bio,
        ]);
        
        if ($result['success']) {
            // 更新成功
            Notification::send('更新成功', '个人资料已更新');
            
            return json(['success' => true]);
        } else {
            // 更新失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
    
    public function changePassword()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        return view('auth/change-password');
    }
    
    public function doChangePassword()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $currentPassword = input('current_password');
        $newPassword = input('new_password');
        $confirmPassword = input('confirm_password');
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return json(['success' => false, 'message' => '所有字段都不能为空']);
        }
        
        if ($newPassword !== $confirmPassword) {
            return json(['success' => false, 'message' => '两次输入的新密码不一致']);
        }
        
        $result = $this->authService->changePassword($currentPassword, $newPassword);
        
        if ($result['success']) {
            // 修改密码成功
            Notification::send('修改密码成功', '您的密码已更新');
            
            return json(['success' => true]);
        } else {
            // 修改密码失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
}
