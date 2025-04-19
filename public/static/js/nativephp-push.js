/**
 * NativePHP for ThinkPHP 推送通知 JavaScript 客户端
 */
(function(window) {
    'use strict';

    // 检查是否在 Electron 环境中
    const isElectron = window.NativePHP !== undefined;

    // 获取基础客户端
    const NativeClient = window.NativeClient || {};

    // 扩展 NativeClient
    NativeClient.pushNotification = {
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
            return NativeClient.app._apiCall('push-notification/status', { reference });
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
        },

        /**
         * 获取设备信息
         * 
         * @param {string} token 设备令牌
         * @returns {Promise<object>}
         */
        getDeviceInfo: function(token) {
            if (isElectron) {
                return window.NativePHP.pushNotification.getDeviceInfo(token);
            }
            return NativeClient.app._apiCall('push-notification/device', { token })
                .then(response => response.device);
        },

        /**
         * 获取推送历史
         * 
         * @param {number} limit 每页数量
         * @param {number} offset 偏移量
         * @returns {Promise<Array>}
         */
        getHistory: function(limit = 10, offset = 0) {
            if (isElectron) {
                return window.NativePHP.pushNotification.getHistory(limit, offset);
            }
            return NativeClient.app._apiCall('push-notification/history', { limit, offset })
                .then(response => response.history);
        },

        /**
         * 获取推送统计
         * 
         * @param {string} startDate 开始日期，格式为 Y-m-d
         * @param {string} endDate 结束日期，格式为 Y-m-d
         * @returns {Promise<object>}
         */
        getStatistics: function(startDate = null, endDate = null) {
            if (isElectron) {
                return window.NativePHP.pushNotification.getStatistics(startDate, endDate);
            }
            return NativeClient.app._apiCall('push-notification/statistics', {
                start_date: startDate,
                end_date: endDate
            }).then(response => response.statistics);
        }
    };

})(window);
