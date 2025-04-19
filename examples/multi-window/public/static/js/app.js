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
            return NativeClient._apiCall('window/open', { url, options }, 'POST')
                .then(response => response.id);
        },
        
        /**
         * 关闭窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        close: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.close(id);
            }
            return NativeClient._apiCall('window/close', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 最小化窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        minimize: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.minimize(id);
            }
            return NativeClient._apiCall('window/minimize', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 最大化窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        maximize: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.maximize(id);
            }
            return NativeClient._apiCall('window/maximize', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 恢复窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        restore: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.restore(id);
            }
            return NativeClient._apiCall('window/restore', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 聚焦窗口
         * 
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        focus: function(id = null) {
            if (isElectron) {
                return window.NativePHP.window.focus(id);
            }
            return NativeClient._apiCall('window/focus', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置窗口标题
         * 
         * @param {string} title 窗口标题
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        setTitle: function(title, id = null) {
            if (isElectron) {
                return window.NativePHP.window.setTitle(title, id);
            }
            return NativeClient._apiCall('window/title', { id, title }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置窗口大小
         * 
         * @param {number} width 窗口宽度
         * @param {number} height 窗口高度
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        setSize: function(width, height, id = null) {
            if (isElectron) {
                return window.NativePHP.window.setSize(width, height, id);
            }
            return NativeClient._apiCall('window/resize', { id, width, height }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置窗口位置
         * 
         * @param {number} x X坐标
         * @param {number} y Y坐标
         * @param {boolean} animated 是否使用动画
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        setPosition: function(x, y, animated = false, id = null) {
            if (isElectron) {
                return window.NativePHP.window.setPosition(x, y, animated, id);
            }
            return NativeClient._apiCall('window/position', { id, x, y, animate: animated }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置窗口是否总是置顶
         * 
         * @param {boolean} alwaysOnTop 是否总是置顶
         * @param {string} id 窗口ID
         * @returns {Promise<boolean>}
         */
        alwaysOnTop: function(alwaysOnTop = true, id = null) {
            if (isElectron) {
                return window.NativePHP.window.alwaysOnTop(alwaysOnTop, id);
            }
            return NativeClient._apiCall('window/always-on-top', { id, alwaysOnTop }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 监听窗口关闭事件
         * 
         * @param {function} callback 回调函数
         * @param {string} id 窗口ID
         * @returns {Promise<string>} 监听器ID
         */
        onClose: function(callback, id = null) {
            if (isElectron) {
                return window.NativePHP.window.onClose(callback, id);
            }
            return NativeClient._apiCall('window/on-close', { id }, 'POST')
                .then(response => {
                    if (response.success) {
                        // 注册事件监听器
                        window.addEventListener('native.window.close', function(event) {
                            if (event.detail.id === id) {
                                callback(event.detail);
                            }
                        });
                        return response.listener_id;
                    }
                    return null;
                });
        },
        
        /**
         * 监听窗口聚焦事件
         * 
         * @param {function} callback 回调函数
         * @param {string} id 窗口ID
         * @returns {Promise<string>} 监听器ID
         */
        onFocus: function(callback, id = null) {
            if (isElectron) {
                return window.NativePHP.window.onFocus(callback, id);
            }
            return NativeClient._apiCall('window/on-focus', { id }, 'POST')
                .then(response => {
                    if (response.success) {
                        // 注册事件监听器
                        window.addEventListener('native.window.focus', function(event) {
                            if (event.detail.id === id) {
                                callback(event.detail);
                            }
                        });
                        return response.listener_id;
                    }
                    return null;
                });
        },
        
        /**
         * 监听窗口失去焦点事件
         * 
         * @param {function} callback 回调函数
         * @param {string} id 窗口ID
         * @returns {Promise<string>} 监听器ID
         */
        onBlur: function(callback, id = null) {
            if (isElectron) {
                return window.NativePHP.window.onBlur(callback, id);
            }
            return NativeClient._apiCall('window/on-blur', { id }, 'POST')
                .then(response => {
                    if (response.success) {
                        // 注册事件监听器
                        window.addEventListener('native.window.blur', function(event) {
                            if (event.detail.id === id) {
                                callback(event.detail);
                            }
                        });
                        return response.listener_id;
                    }
                    return null;
                });
        },
        
        /**
         * 监听窗口移动事件
         * 
         * @param {function} callback 回调函数
         * @param {string} id 窗口ID
         * @returns {Promise<string>} 监听器ID
         */
        onMove: function(callback, id = null) {
            if (isElectron) {
                return window.NativePHP.window.onMove(callback, id);
            }
            return NativeClient._apiCall('window/on-move', { id }, 'POST')
                .then(response => {
                    if (response.success) {
                        // 注册事件监听器
                        window.addEventListener('native.window.move', function(event) {
                            if (event.detail.id === id) {
                                callback(event.detail);
                            }
                        });
                        return response.listener_id;
                    }
                    return null;
                });
        },
        
        /**
         * 监听窗口调整大小事件
         * 
         * @param {function} callback 回调函数
         * @param {string} id 窗口ID
         * @returns {Promise<string>} 监听器ID
         */
        onResize: function(callback, id = null) {
            if (isElectron) {
                return window.NativePHP.window.onResize(callback, id);
            }
            return NativeClient._apiCall('window/on-resize', { id }, 'POST')
                .then(response => {
                    if (response.success) {
                        // 注册事件监听器
                        window.addEventListener('native.window.resize', function(event) {
                            if (event.detail.id === id) {
                                callback(event.detail);
                            }
                        });
                        return response.listener_id;
                    }
                    return null;
                });
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
