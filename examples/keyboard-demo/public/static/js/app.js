// 检查是否在 Electron 环境中运行
const isElectron = typeof window !== 'undefined' && window.process && window.process.type === 'renderer';

// 初始化 NativeClient
const NativeClient = {
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
            return NativeClient._apiCall('notification', { title, body, options }, 'POST')
                .then(response => response.reference);
        }
    },
    
    // 键盘管理
    keyboard: {
        /**
         * 注册快捷键
         * 
         * @param {string} accelerator 快捷键组合
         * @param {function} callback 回调函数
         * @returns {Promise<string>} 快捷键ID
         */
        register: function(accelerator, callback) {
            if (isElectron) {
                return window.NativePHP.keyboard.register(accelerator, callback);
            }
            return NativeClient._apiCall('keyboard/register', { accelerator }, 'POST')
                .then(response => {
                    if (response.success) {
                        return response.id;
                    }
                    return null;
                });
        },
        
        /**
         * 注销快捷键
         * 
         * @param {string} id 快捷键ID
         * @returns {Promise<boolean>}
         */
        unregister: function(id) {
            if (isElectron) {
                return window.NativePHP.keyboard.unregister(id);
            }
            return NativeClient._apiCall('keyboard/unregister', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 注册全局快捷键
         * 
         * @param {string} accelerator 快捷键组合
         * @param {function} callback 回调函数
         * @returns {Promise<string>} 快捷键ID
         */
        registerGlobal: function(accelerator, callback) {
            if (isElectron) {
                return window.NativePHP.keyboard.registerGlobal(accelerator, callback);
            }
            return NativeClient._apiCall('keyboard/register-global', { accelerator }, 'POST')
                .then(response => {
                    if (response.success) {
                        return response.id;
                    }
                    return null;
                });
        },
        
        /**
         * 注销全局快捷键
         * 
         * @param {string} id 快捷键ID
         * @returns {Promise<boolean>}
         */
        unregisterGlobal: function(id) {
            if (isElectron) {
                return window.NativePHP.keyboard.unregisterGlobal(id);
            }
            return NativeClient._apiCall('keyboard/unregister-global', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 模拟按键
         * 
         * @param {string} key 按键
         * @param {array} modifiers 修饰键数组
         * @returns {Promise<boolean>}
         */
        sendKey: function(key, modifiers = []) {
            if (isElectron) {
                return window.NativePHP.keyboard.sendKey(key, modifiers);
            }
            return NativeClient._apiCall('keyboard/send-key', { key, modifiers }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 模拟按键序列
         * 
         * @param {string} text 文本
         * @returns {Promise<boolean>}
         */
        sendText: function(text) {
            if (isElectron) {
                return window.NativePHP.keyboard.sendText(text);
            }
            return NativeClient._apiCall('keyboard/send-text', { text }, 'POST')
                .then(response => response.success);
        }
    },
    
    // 内部 API 调用方法
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
    // 注册事件监听器
    registerEventListeners();
}

/**
 * 注册事件监听器
 */
function registerEventListeners() {
    // 这里可以添加全局事件监听器
}

/**
 * 显示通知
 * 
 * @param {string} title 通知标题
 * @param {string} body 通知内容
 */
function showNotification(title, body) {
    if (isElectron) {
        window.NativePHP.notification.send(title, body);
    } else {
        NativeClient.notification.send(title, body);
    }
}
