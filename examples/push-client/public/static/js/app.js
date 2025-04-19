// 检查是否在 Electron 环境中运行
const isElectron = typeof window !== 'undefined' && window.process && window.process.type === 'renderer';

// 初始化 NativeClient
const NativeClient = {
    // 应用管理
    app: {
        /**
         * 获取应用名称
         * 
         * @returns {Promise<string>}
         */
        name: function() {
            if (isElectron) {
                return window.NativePHP.app.name();
            }
            return NativeClient.app._apiCall('app/name').then(response => response.name);
        },
        
        /**
         * 获取应用版本
         * 
         * @returns {Promise<string>}
         */
        version: function() {
            if (isElectron) {
                return window.NativePHP.app.version();
            }
            return NativeClient.app._apiCall('app/version').then(response => response.version);
        },
        
        /**
         * 退出应用
         * 
         * @returns {Promise<void>}
         */
        quit: function() {
            if (isElectron) {
                return window.NativePHP.app.quit();
            }
            return NativeClient.app._apiCall('app/quit', {}, 'POST');
        },
        
        /**
         * 重启应用
         * 
         * @returns {Promise<void>}
         */
        restart: function() {
            if (isElectron) {
                return window.NativePHP.app.restart();
            }
            return NativeClient.app._apiCall('app/restart', {}, 'POST');
        },
        
        /**
         * 内部 API 调用方法
         * 
         * @param {string} endpoint 
         * @param {object} data 
         * @param {string} method 
         * @returns {Promise<any>}
         */
        _apiCall: function(endpoint, data = {}, method = 'GET') {
            const url = `/_native/${endpoint}`;
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            
            if (method === 'POST' || method === 'PUT') {
                options.body = JSON.stringify(data);
            }
            
            return fetch(url, options).then(response => response.json());
        }
    },
    
    // 通知管理
    notification: {
        /**
         * 发送通知
         * 
         * @param {string} title 通知标题
         * @param {string} body 通知内容
         * @param {object} options 选项
         * @returns {Promise<string>} 通知引用ID
         */
        send: function(title, body, options = {}) {
            if (isElectron) {
                return window.NativePHP.notification.send(title, body, options);
            }
            return NativeClient.app._apiCall('notification', { title, body, options }, 'POST')
                .then(response => response.reference);
        }
    },
    
    // 窗口管理
    window: {
        /**
         * 打开新窗口
         * 
         * @param {string} url 窗口URL
         * @param {object} options 窗口选项
         * @returns {Promise<string>} 窗口ID
         */
        open: function(url, options = {}) {
            if (isElectron) {
                return window.NativePHP.window.open(url, options);
            }
            return NativeClient.app._apiCall('window/open', { url, options }, 'POST')
                .then(response => response.id);
        },
        
        /**
         * 关闭窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<void>}
         */
        close: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.close(id);
            }
            return NativeClient.app._apiCall('window/close', { id }, 'POST');
        }
    },
    
    // 推送通知管理
    pushNotification: {
        /**
         * 注册设备
         * 
         * @param {string} token 设备令牌
         * @param {object} data 设备数据
         * @returns {Promise<boolean>}
         */
        registerDevice: function(token, data = {}) {
            if (isElectron) {
                return window.NativePHP.pushNotification.registerDevice(token, data);
            }
            return NativeClient.app._apiCall('push-notification/register-device', { token, data }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 注销设备
         * 
         * @param {string} token 设备令牌
         * @returns {Promise<boolean>}
         */
        unregisterDevice: function(token) {
            if (isElectron) {
                return window.NativePHP.pushNotification.unregisterDevice(token);
            }
            return NativeClient.app._apiCall('push-notification/unregister-device', { token }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 发送推送通知
         * 
         * @param {string|Array} tokens 设备令牌
         * @param {string} title 通知标题
         * @param {string} body 通知内容
         * @param {object} data 附加数据
         * @param {object} options 选项
         * @returns {Promise<string>} 推送引用ID
         */
        send: function(tokens, title, body, data = {}, options = {}) {
            if (isElectron) {
                return window.NativePHP.pushNotification.send(tokens, title, body, data, options);
            }
            return NativeClient.app._apiCall('push-notification/send', {
                tokens: Array.isArray(tokens) ? tokens : [tokens],
                title,
                body,
                data,
                options
            }, 'POST').then(response => {
                return response.success ? response.reference : null;
            });
        },
        
        /**
         * 获取推送状态
         * 
         * @param {string} reference 推送引用ID
         * @returns {Promise<object>}
         */
        getStatus: function(reference) {
            if (isElectron) {
                return window.NativePHP.pushNotification.getStatus(reference);
            }
            return NativeClient.app._apiCall(`push-notification/status?reference=${reference}`);
        },
        
        /**
         * 取消推送
         * 
         * @param {string} reference 推送引用ID
         * @returns {Promise<boolean>}
         */
        cancel: function(reference) {
            if (isElectron) {
                return window.NativePHP.pushNotification.cancel(reference);
            }
            return NativeClient.app._apiCall('push-notification/cancel', { reference }, 'POST')
                .then(response => response.success);
        }
    }
};

// 页面加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化应用
    initApp();
});

/**
 * 初始化应用
 */
function initApp() {
    // 获取应用信息
    NativeClient.app.name().then(name => {
        document.title = name + ' - ' + document.title;
    });
    
    // 注册事件监听器
    registerEventListeners();
}

/**
 * 注册事件监听器
 */
function registerEventListeners() {
    // 注册设备表单提交
    const registerForm = document.querySelector('.register-form');
    if (registerForm) {
        const registerButton = registerForm.querySelector('button');
        if (registerButton) {
            registerButton.addEventListener('click', function() {
                registerDevice();
            });
        }
    }
    
    // 设置表单提交
    const settingsForm = document.querySelector('.settings-form');
    if (settingsForm) {
        const saveButton = document.querySelector('.actions .btn-primary');
        if (saveButton) {
            saveButton.addEventListener('click', function() {
                saveSettings();
            });
        }
        
        // 切换推送服务提供商设置
        const providerSelect = document.getElementById('providerSelect');
        if (providerSelect) {
            providerSelect.addEventListener('change', function() {
                const provider = this.value;
                
                document.querySelectorAll('.provider-settings').forEach(function(element) {
                    element.style.display = 'none';
                });
                
                document.getElementById(provider + 'Settings').style.display = 'block';
            });
        }
        
        // 切换自定义声音设置
        const notificationSound = document.getElementById('notificationSound');
        if (notificationSound) {
            notificationSound.addEventListener('change', function() {
                const sound = this.value;
                
                if (sound === 'custom') {
                    document.getElementById('customSoundPath').style.display = 'block';
                } else {
                    document.getElementById('customSoundPath').style.display = 'none';
                }
            });
        }
    }
}

/**
 * 注册设备
 */
function registerDevice() {
    const token = document.getElementById('tokenInput').value;
    const provider = document.getElementById('providerSelect').value;
    
    if (!token) {
        alert('设备令牌不能为空');
        return;
    }
    
    fetch('/push/registerDevice', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token,
            provider,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('注册设备失败');
    });
}

/**
 * 注销设备
 */
function unregisterDevice() {
    if (!confirm('确定要注销设备吗？注销后将不再接收推送通知。')) {
        return;
    }
    
    fetch('/push/unregisterDevice', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('注销设备失败');
    });
}

/**
 * 发送测试通知
 */
function sendTestNotification() {
    fetch('/push/sendTestNotification', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('发送测试通知失败');
    });
}

/**
 * 保存设置
 */
function saveSettings() {
    const provider = document.getElementById('providerSelect').value;
    const config = {
        firebase: {
            server_key: document.getElementById('firebaseServerKey').value,
            sender_id: document.getElementById('firebaseSenderId').value,
        },
        apns: {
            cert_path: document.getElementById('apnsCertPath').value,
            cert_password: document.getElementById('apnsCertPassword').value,
            environment: document.getElementById('apnsEnvironment').value,
        },
        jpush: {
            app_key: document.getElementById('jpushAppKey').value,
            master_secret: document.getElementById('jpushMasterSecret').value,
        },
        notification: {
            sound: document.getElementById('notificationSound').value,
            custom_sound: document.getElementById('notificationCustomSound').value,
            badge: document.getElementById('notificationBadge').checked,
            vibrate: document.getElementById('notificationVibrate').checked,
            lights: document.getElementById('notificationLights').checked,
        },
    };
    
    fetch('/push/saveSettings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            provider,
            config,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('保存设置失败');
    });
}

/**
 * 标记通知为已读
 * 
 * @param {string} id 通知ID
 */
function markAsRead(id) {
    fetch('/push/markAsRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 刷新页面
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('标记为已读失败');
    });
}

/**
 * 清空历史记录
 */
function clearHistory() {
    if (!confirm('确定要清空历史记录吗？此操作不可恢复。')) {
        return;
    }
    
    fetch('/push/clearHistory', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('清空历史记录失败');
    });
}

/**
 * 刷新通知列表
 */
function refreshNotifications() {
    location.reload();
}
