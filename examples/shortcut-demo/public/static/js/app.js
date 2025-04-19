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
    
    // 快捷方式管理
    shortcut: {
        /**
         * 创建桌面快捷方式
         * 
         * @param {object} options 选项
         * @returns {Promise<boolean>}
         */
        createDesktopShortcut: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.shortcut.createDesktopShortcut(options);
            }
            return NativeClient._apiCall('shortcut/create-desktop', options, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 创建开始菜单快捷方式
         * 
         * @param {object} options 选项
         * @returns {Promise<boolean>}
         */
        createStartMenuShortcut: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.shortcut.createStartMenuShortcut(options);
            }
            return NativeClient._apiCall('shortcut/create-start-menu', options, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 创建应用程序快捷方式
         * 
         * @param {string} path 快捷方式路径
         * @param {object} options 选项
         * @returns {Promise<boolean>}
         */
        createShortcut: function(path, options = {}) {
            if (isElectron) {
                return window.NativePHP.shortcut.createShortcut(path, options);
            }
            return NativeClient._apiCall('shortcut/create', { path, ...options }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 检查桌面快捷方式是否存在
         * 
         * @returns {Promise<boolean>}
         */
        existsOnDesktop: function() {
            if (isElectron) {
                return window.NativePHP.shortcut.existsOnDesktop();
            }
            return NativeClient._apiCall('shortcut/exists-on-desktop', {}, 'GET')
                .then(response => response.exists);
        },
        
        /**
         * 检查开始菜单快捷方式是否存在
         * 
         * @returns {Promise<boolean>}
         */
        existsInStartMenu: function() {
            if (isElectron) {
                return window.NativePHP.shortcut.existsInStartMenu();
            }
            return NativeClient._apiCall('shortcut/exists-in-start-menu', {}, 'GET')
                .then(response => response.exists);
        },
        
        /**
         * 检查快捷方式是否存在
         * 
         * @param {string} path 快捷方式路径
         * @returns {Promise<boolean>}
         */
        exists: function(path) {
            if (isElectron) {
                return window.NativePHP.shortcut.exists(path);
            }
            return NativeClient._apiCall('shortcut/exists', { path }, 'POST')
                .then(response => response.exists);
        },
        
        /**
         * 删除桌面快捷方式
         * 
         * @returns {Promise<boolean>}
         */
        removeFromDesktop: function() {
            if (isElectron) {
                return window.NativePHP.shortcut.removeFromDesktop();
            }
            return NativeClient._apiCall('shortcut/remove-from-desktop', {}, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 删除开始菜单快捷方式
         * 
         * @returns {Promise<boolean>}
         */
        removeFromStartMenu: function() {
            if (isElectron) {
                return window.NativePHP.shortcut.removeFromStartMenu();
            }
            return NativeClient._apiCall('shortcut/remove-from-start-menu', {}, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 删除快捷方式
         * 
         * @param {string} path 快捷方式路径
         * @returns {Promise<boolean>}
         */
        remove: function(path) {
            if (isElectron) {
                return window.NativePHP.shortcut.remove(path);
            }
            return NativeClient._apiCall('shortcut/remove', { path }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置开机自启动
         * 
         * @param {boolean} enabled 是否启用
         * @param {object} options 选项
         * @returns {Promise<boolean>}
         */
        setLoginItemSettings: function(enabled = true, options = {}) {
            if (isElectron) {
                return window.NativePHP.shortcut.setLoginItemSettings(enabled, options);
            }
            return NativeClient._apiCall('shortcut/set-login-item-settings', { enabled, ...options }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 获取开机自启动设置
         * 
         * @param {object} options 选项
         * @returns {Promise<object>}
         */
        getLoginItemSettings: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.shortcut.getLoginItemSettings(options);
            }
            return NativeClient._apiCall('shortcut/get-login-item-settings', options, 'POST')
                .then(response => response.settings);
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
