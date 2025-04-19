/**
 * NativePHP for ThinkPHP JavaScript 客户端
 */
(function(window) {
    'use strict';

    // 检查是否在 Electron 环境中
    const isElectron = window.NativePHP !== undefined;

    // NativePHP 客户端
    const NativePHP = {
        /**
         * 是否在 Electron 环境中
         */
        isElectron: isElectron,

        /**
         * 应用相关
         */
        app: {
            /**
             * 获取应用名称
             *
             * @returns {Promise<string>}
             */
            getName: function() {
                if (isElectron) {
                    return window.NativePHP.app.getName();
                }
                return this._apiCall('info').then(response => response.name);
            },

            /**
             * 获取应用版本
             *
             * @returns {Promise<string>}
             */
            getVersion: function() {
                if (isElectron) {
                    return window.NativePHP.app.getVersion();
                }
                return this._apiCall('info').then(response => response.version);
            },

            /**
             * 获取应用环境
             *
             * @returns {Promise<string>}
             */
            getEnvironment: function() {
                if (isElectron) {
                    return window.NativePHP.app.isPackaged() ? 'desktop' : 'development';
                }
                return this._apiCall('info').then(response => response.environment);
            },

            /**
             * 退出应用
             *
             * @returns {Promise<void>}
             */
            quit: function() {
                if (isElectron) {
                    window.NativePHP.app.quit();
                    return Promise.resolve();
                }
                return this._apiCall('app/quit', {}, 'POST');
            },

            /**
             * 重启应用
             *
             * @returns {Promise<void>}
             */
            restart: function() {
                if (isElectron) {
                    window.NativePHP.app.relaunch();
                    return Promise.resolve();
                }
                return this._apiCall('app/restart', {}, 'POST');
            },

            /**
             * API 调用
             *
             * @param {string} endpoint
             * @param {object} data
             * @param {string} method
             * @returns {Promise<any>}
             */
            _apiCall: function(endpoint, data = {}, method = 'GET') {
                const url = '/_native/' + endpoint;
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                };

                if (method === 'POST' || method === 'PUT' || method === 'PATCH') {
                    options.body = JSON.stringify(data);
                }

                return fetch(url, options).then(response => response.json());
            }
        },

        /**
         * 窗口相关
         */
        window: {
            /**
             * 打开窗口
             *
             * @param {string} url
             * @param {object} options
             * @returns {Promise<string>}
             */
            open: function(url, options = {}) {
                if (isElectron) {
                    return window.NativePHP.window.open(url, options);
                }
                return NativePHP.app._apiCall('window/open', { url, options }, 'POST')
                    .then(response => response.id);
            },

            /**
             * 关闭窗口
             *
             * @param {string} id
             * @returns {Promise<void>}
             */
            close: function(id = null) {
                if (isElectron) {
                    if (id) {
                        // 关闭指定窗口
                        const win = window.NativePHP.window.getById(id);
                        if (win) {
                            win.close();
                        }
                    } else {
                        // 关闭当前窗口
                        window.NativePHP.window.close();
                    }
                    return Promise.resolve();
                }
                return NativePHP.app._apiCall('window/close', { id }, 'POST');
            }
        },

        /**
         * 通知相关
         */
        notification: {
            /**
             * 发送通知
             *
             * @param {string} title
             * @param {string} body
             * @param {object} options
             * @returns {Promise<string>}
             */
            send: function(title, body, options = {}) {
                if (isElectron) {
                    return window.NativePHP.notification.send(title, body, options);
                }
                return NativePHP.app._apiCall('notification', { title, body, options }, 'POST')
                    .then(response => response.reference);
            }
        }
    };

    // 导出 NativePHP 客户端
    window.NativeClient = NativePHP;

})(window);
