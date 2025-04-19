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
    
    // 对话框管理
    dialog: {
        /**
         * 显示消息对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<number>} 点击的按钮索引
         */
        message: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.message(message, options);
            }
            return NativeClient._apiCall('dialog/message', { message, ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示错误消息对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<number>} 点击的按钮索引
         */
        error: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.error(message, options);
            }
            return NativeClient._apiCall('dialog/message', { message, type: 'error', ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示信息消息对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<number>} 点击的按钮索引
         */
        info: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.info(message, options);
            }
            return NativeClient._apiCall('dialog/message', { message, type: 'info', ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示警告消息对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<number>} 点击的按钮索引
         */
        warning: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.warning(message, options);
            }
            return NativeClient._apiCall('dialog/message', { message, type: 'warning', ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示问题消息对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<number>} 点击的按钮索引
         */
        question: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.question(message, options);
            }
            return NativeClient._apiCall('dialog/message', { message, type: 'question', ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示确认对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<boolean>} 是否确认
         */
        confirm: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.confirm(message, options);
            }
            return NativeClient._apiCall('dialog/confirm', { message, ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示输入对话框
         * 
         * @param {string} message 消息内容
         * @param {object} options 选项
         * @returns {Promise<string|null>} 输入的文本
         */
        prompt: function(message, options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.prompt(message, options);
            }
            return NativeClient._apiCall('dialog/prompt', { message, ...options }, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示打开文件对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<string|string[]|null>} 选择的文件路径
         */
        openFile: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.openFile(options);
            }
            return NativeClient._apiCall('dialog/openFile', options, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示保存文件对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<string|null>} 保存的文件路径
         */
        saveFile: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.saveFile(options);
            }
            return NativeClient._apiCall('dialog/saveFile', options, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示选择文件夹对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<string|null>} 选择的文件夹路径
         */
        selectFolder: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.selectFolder(options);
            }
            return NativeClient._apiCall('dialog/selectFolder', options, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示颜色选择对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<string|null>} 选择的颜色
         */
        color: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.color(options);
            }
            return NativeClient._apiCall('dialog/color', options, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示字体选择对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<object|null>} 选择的字体
         */
        font: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.font(options);
            }
            return NativeClient._apiCall('dialog/font', options, 'POST')
                .then(response => response.result);
        },
        
        /**
         * 显示证书选择对话框
         * 
         * @param {object} options 选项
         * @returns {Promise<object|null>} 选择的证书
         */
        certificate: function(options = {}) {
            if (isElectron) {
                return window.NativePHP.dialog.certificate(options);
            }
            return NativeClient._apiCall('dialog/certificate', options, 'POST')
                .then(response => response.result);
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
