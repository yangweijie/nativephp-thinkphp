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
    
    // 电源监控管理
    powerMonitor: {
        /**
         * 获取系统空闲时间（秒）
         * 
         * @returns {Promise<number>}
         */
        getSystemIdleTime: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getSystemIdleTime();
            }
            return NativeClient._apiCall('power-monitor/system-idle-time', {}, 'GET')
                .then(response => response.time);
        },
        
        /**
         * 获取系统是否空闲
         * 
         * @param {number} threshold 阈值（秒）
         * @returns {Promise<boolean>}
         */
        isSystemIdle: function(threshold = 60) {
            if (isElectron) {
                return window.NativePHP.powerMonitor.isSystemIdle(threshold);
            }
            return NativeClient._apiCall('power-monitor/is-system-idle', { threshold }, 'POST')
                .then(response => response.idle);
        },
        
        /**
         * 获取系统是否锁定
         * 
         * @returns {Promise<boolean>}
         */
        isSystemLocked: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.isSystemLocked();
            }
            return NativeClient._apiCall('power-monitor/is-system-locked', {}, 'GET')
                .then(response => response.locked);
        },
        
        /**
         * 获取系统是否在屏幕保护状态
         * 
         * @returns {Promise<boolean>}
         */
        isSystemOnScreenSaver: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.isSystemOnScreenSaver();
            }
            return NativeClient._apiCall('power-monitor/is-system-on-screen-saver', {}, 'GET')
                .then(response => response.onScreenSaver);
        },
        
        /**
         * 获取系统电源状态
         * 
         * @returns {Promise<string>}
         */
        getPowerState: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getPowerState();
            }
            return NativeClient._apiCall('power-monitor/power-state', {}, 'GET')
                .then(response => response.state);
        },
        
        /**
         * 获取电池电量
         * 
         * @returns {Promise<number>}
         */
        getBatteryLevel: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getBatteryLevel();
            }
            return NativeClient._apiCall('power-monitor/battery-level', {}, 'GET')
                .then(response => response.level);
        },
        
        /**
         * 获取电池是否正在充电
         * 
         * @returns {Promise<boolean>}
         */
        isBatteryCharging: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.isBatteryCharging();
            }
            return NativeClient._apiCall('power-monitor/is-battery-charging', {}, 'GET')
                .then(response => response.charging);
        },
        
        /**
         * 获取电池剩余时间（分钟）
         * 
         * @returns {Promise<number>}
         */
        getBatteryTimeRemaining: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getBatteryTimeRemaining();
            }
            return NativeClient._apiCall('power-monitor/battery-time-remaining', {}, 'GET')
                .then(response => response.time);
        },
        
        /**
         * 获取系统是否处于低电量模式
         * 
         * @returns {Promise<boolean>}
         */
        isLowPowerMode: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.isLowPowerMode();
            }
            return NativeClient._apiCall('power-monitor/is-low-power-mode', {}, 'GET')
                .then(response => response.lowPowerMode);
        },
        
        /**
         * 监听系统挂起事件
         * 
         * @param {function} callback 回调函数
         * @returns {Promise<string>} 监听器ID
         */
        onSuspend: function(callback) {
            if (isElectron) {
                return window.NativePHP.powerMonitor.onSuspend(callback);
            }
            return NativeClient._apiCall('power-monitor/on', { event: 'suspend' }, 'POST')
                .then(response => {
                    if (response.success) {
                        window.addEventListener('native.power-monitor.suspend', function(event) {
                            callback(event.detail);
                        });
                        return response.id;
                    }
                    return null;
                });
        },
        
        /**
         * 监听系统恢复事件
         * 
         * @param {function} callback 回调函数
         * @returns {Promise<string>} 监听器ID
         */
        onResume: function(callback) {
            if (isElectron) {
                return window.NativePHP.powerMonitor.onResume(callback);
            }
            return NativeClient._apiCall('power-monitor/on', { event: 'resume' }, 'POST')
                .then(response => {
                    if (response.success) {
                        window.addEventListener('native.power-monitor.resume', function(event) {
                            callback(event.detail);
                        });
                        return response.id;
                    }
                    return null;
                });
        },
        
        /**
         * 取消事件监听器
         * 
         * @param {string} id 监听器ID
         * @returns {Promise<boolean>}
         */
        off: function(id) {
            if (isElectron) {
                return window.NativePHP.powerMonitor.off(id);
            }
            return NativeClient._apiCall('power-monitor/off', { id }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 取消所有事件监听器
         * 
         * @returns {Promise<boolean>}
         */
        offAll: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.offAll();
            }
            return NativeClient._apiCall('power-monitor/off-all', {}, 'POST')
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
