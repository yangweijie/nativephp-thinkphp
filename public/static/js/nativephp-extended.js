/**
 * NativePHP for ThinkPHP 扩展功能 JavaScript 客户端
 */
(function(window) {
    'use strict';

    // 检查是否在 Electron 环境中
    const isElectron = window.NativePHP !== undefined;

    // 获取基础客户端
    const NativeClient = window.NativeClient || {};

    // 扩展 NativeClient
    NativeClient.keyboard = {
        /**
         * 注册快捷键
         * 
         * @param {string} accelerator 快捷键组合，如 'CommandOrControl+Shift+K'
         * @param {Function} callback 回调函数
         * @returns {Promise<string>} 快捷键ID
         */
        register: function(accelerator, callback) {
            if (isElectron) {
                return window.NativePHP.keyboard.register(accelerator, callback);
            }
            return NativeClient.app._apiCall('keyboard/register', { accelerator }, 'POST')
                .then(response => {
                    if (response.success && callback) {
                        // 注册事件监听器
                        document.addEventListener('native.keyboard.shortcut', function(e) {
                            if (e.detail === response.id) {
                                callback();
                            }
                        });
                    }
                    return response.id;
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
            return NativeClient.app._apiCall('keyboard/unregister', { id }, 'POST')
                .then(response => response.success);
        },

        /**
         * 模拟按键
         * 
         * @param {string} key 按键
         * @param {Array} modifiers 修饰键数组，如 ['shift', 'control']
         * @returns {Promise<boolean>}
         */
        sendKey: function(key, modifiers = []) {
            if (isElectron) {
                return window.NativePHP.keyboard.sendKey(key, modifiers);
            }
            return NativeClient.app._apiCall('keyboard/send-key', { key, modifiers }, 'POST')
                .then(response => response.success);
        },

        /**
         * 模拟按键序列
         * 
         * @param {string} text 按键序列，如 'Hello, World!'
         * @returns {Promise<boolean>}
         */
        sendText: function(text) {
            if (isElectron) {
                return window.NativePHP.keyboard.sendText(text);
            }
            return NativeClient.app._apiCall('keyboard/send-text', { text }, 'POST')
                .then(response => response.success);
        }
    };

    NativeClient.powerMonitor = {
        /**
         * 获取系统空闲时间
         * 
         * @returns {Promise<number>} 空闲时间（秒）
         */
        getSystemIdleTime: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getSystemIdleTime();
            }
            return NativeClient.app._apiCall('power-monitor/idle-time')
                .then(response => response.idle_time);
        },

        /**
         * 获取系统是否空闲
         * 
         * @param {number} threshold 空闲阈值（秒）
         * @returns {Promise<boolean>}
         */
        isSystemIdle: function(threshold = 60) {
            return this.getSystemIdleTime().then(idleTime => idleTime >= threshold);
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
            return NativeClient.app._apiCall('power-monitor/is-locked')
                .then(response => response.locked);
        },

        /**
         * 获取系统电源状态
         * 
         * @returns {Promise<string>} 'on-ac', 'on-battery', 'charging', 'discharging', 'unknown'
         */
        getPowerState: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getPowerState();
            }
            return NativeClient.app._apiCall('power-monitor/power-state')
                .then(response => response.state);
        },

        /**
         * 获取电池电量
         * 
         * @returns {Promise<number>} 0.0 到 1.0 之间的值，表示电池电量百分比
         */
        getBatteryLevel: function() {
            if (isElectron) {
                return window.NativePHP.powerMonitor.getBatteryLevel();
            }
            return NativeClient.app._apiCall('power-monitor/battery-level')
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
            return NativeClient.app._apiCall('power-monitor/is-battery-charging')
                .then(response => response.charging);
        }
    };

    NativeClient.dock = {
        /**
         * 设置 Dock 图标
         * 
         * @param {string} path 图标路径
         * @returns {Promise<boolean>}
         */
        setIcon: function(path) {
            if (isElectron) {
                return window.NativePHP.dock.setIcon(path);
            }
            return NativeClient.app._apiCall('dock/set-icon', { path }, 'POST')
                .then(response => response.success);
        },

        /**
         * 设置 Dock 徽章文本
         * 
         * @param {string} text 徽章文本
         * @returns {Promise<boolean>}
         */
        setBadge: function(text) {
            if (isElectron) {
                return window.NativePHP.dock.setBadge(text);
            }
            return NativeClient.app._apiCall('dock/set-badge', { text }, 'POST')
                .then(response => response.success);
        },

        /**
         * 设置 Dock 徽章计数
         * 
         * @param {number} count 徽章计数
         * @returns {Promise<boolean>}
         */
        setBadgeCount: function(count) {
            if (isElectron) {
                return window.NativePHP.dock.setBadgeCount(count);
            }
            return NativeClient.app._apiCall('dock/set-badge-count', { count }, 'POST')
                .then(response => response.success);
        },

        /**
         * 获取 Dock 徽章计数
         * 
         * @returns {Promise<number>}
         */
        getBadgeCount: function() {
            if (isElectron) {
                return window.NativePHP.dock.getBadgeCount();
            }
            return NativeClient.app._apiCall('dock/get-badge-count')
                .then(response => response.count);
        },

        /**
         * 清除 Dock 徽章
         * 
         * @returns {Promise<boolean>}
         */
        clearBadge: function() {
            if (isElectron) {
                return window.NativePHP.dock.clearBadge();
            }
            return NativeClient.app._apiCall('dock/clear-badge', {}, 'POST')
                .then(response => response.success);
        },

        /**
         * 弹跳 Dock 图标
         * 
         * @param {string} type 弹跳类型，'informational' 或 'critical'
         * @returns {Promise<boolean>}
         */
        bounce: function(type = 'informational') {
            if (isElectron) {
                return window.NativePHP.dock.bounce(type);
            }
            return NativeClient.app._apiCall('dock/bounce', { type }, 'POST')
                .then(response => response.success);
        },

        /**
         * 设置下载进度条
         * 
         * @param {number} progress 进度，0.0 到 1.0 之间的值
         * @returns {Promise<boolean>}
         */
        setDownloadProgress: function(progress) {
            if (isElectron) {
                return window.NativePHP.dock.setDownloadProgress(progress);
            }
            return NativeClient.app._apiCall('dock/set-download-progress', { progress }, 'POST')
                .then(response => response.success);
        },

        /**
         * 清除下载进度条
         * 
         * @returns {Promise<boolean>}
         */
        clearDownloadProgress: function() {
            if (isElectron) {
                return window.NativePHP.dock.clearDownloadProgress();
            }
            return NativeClient.app._apiCall('dock/clear-download-progress', {}, 'POST')
                .then(response => response.success);
        }
    };

})(window);
