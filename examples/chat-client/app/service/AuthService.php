<?php

namespace app\service;

use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Settings;

class AuthService
{
    /**
     * API 基础 URL
     *
     * @var string
     */
    protected $baseUrl = 'https://chat.example.com/api';
    
    /**
     * 检查用户是否已登录
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        $token = Settings::get('auth.token');
        
        return !empty($token);
    }
    
    /**
     * 获取当前登录用户信息
     *
     * @return array|null
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $user = Settings::get('auth.user');
        
        if (empty($user)) {
            // 如果本地没有用户信息，从服务器获取
            $response = Http::withToken(Settings::get('auth.token'))
                ->get($this->baseUrl . '/user');
            
            if ($response->successful()) {
                $user = $response->json('data');
                Settings::set('auth.user', $user);
            }
        }
        
        return $user;
    }
    
    /**
     * 用户登录
     *
     * @param string $username
     * @param string $password
     * @param bool $remember
     * @return array
     */
    public function login($username, $password, $remember = false)
    {
        $response = Http::post($this->baseUrl . '/auth/login', [
            'username' => $username,
            'password' => $password,
            'remember' => $remember,
        ]);
        
        if ($response->successful()) {
            $data = $response->json('data');
            
            // 保存 token 和用户信息
            Settings::set('auth.token', $data['token']);
            Settings::set('auth.user', $data['user']);
            
            return [
                'success' => true,
                'user' => $data['user'],
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '登录失败'),
            ];
        }
    }
    
    /**
     * 用户注册
     *
     * @param array $data
     * @return array
     */
    public function register($data)
    {
        $response = Http::post($this->baseUrl . '/auth/register', $data);
        
        if ($response->successful()) {
            return [
                'success' => true,
                'user' => $response->json('data.user'),
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '注册失败'),
            ];
        }
    }
    
    /**
     * 用户登出
     *
     * @return bool
     */
    public function logout()
    {
        if (!$this->isLoggedIn()) {
            return true;
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/auth/logout');
        
        // 无论服务器响应如何，都清除本地 token 和用户信息
        Settings::forget('auth.token');
        Settings::forget('auth.user');
        
        return $response->successful();
    }
    
    /**
     * 更新用户资料
     *
     * @param array $data
     * @return array
     */
    public function updateProfile($data)
    {
        if (!$this->isLoggedIn()) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/user/profile', $data);
        
        if ($response->successful()) {
            // 更新本地用户信息
            $user = Settings::get('auth.user', []);
            $user = array_merge($user, $data);
            Settings::set('auth.user', $user);
            
            return [
                'success' => true,
                'user' => $user,
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '更新资料失败'),
            ];
        }
    }
    
    /**
     * 修改密码
     *
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword($currentPassword, $newPassword)
    {
        if (!$this->isLoggedIn()) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/user/password', [
                'current_password' => $currentPassword,
                'new_password' => $newPassword,
            ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '修改密码失败'),
            ];
        }
    }
}
