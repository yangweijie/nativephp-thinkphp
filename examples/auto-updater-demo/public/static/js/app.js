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
    
    // 自动更新管理
    autoUpdater: {
        /**
         * 设置更新服务器 URL
         * 
         * @param {string} url 更新服务器 URL
         * @returns {Promise<boolean>}
         */
        setFeedURL: function(url) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.setFeedURL(url);
            }
            return NativeClient._apiCall('auto-updater/set-feed-url', { url }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置是否自动下载更新
         * 
         * @param {boolean} autoDownload 是否自动下载更新
         * @returns {Promise<boolean>}
         */
        setAutoDownload: function(autoDownload = true) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.setAutoDownload(autoDownload);
            }
            return NativeClient._apiCall('auto-updater/set-auto-download', { autoDownload }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置是否自动安装更新
         * 
         * @param {boolean} autoInstall 是否自动安装更新
         * @returns {Promise<boolean>}
         */
        setAutoInstall: function(autoInstall = true) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.setAutoInstall(autoInstall);
            }
            return NativeClient._apiCall('auto-updater/set-auto-install', { autoInstall }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 设置是否允许预发布版本
         * 
         * @param {boolean} allowPrerelease 是否允许预发布版本
         * @returns {Promise<boolean>}
         */
        setAllowPrerelease: function(allowPrerelease = true) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.setAllowPrerelease(allowPrerelease);
            }
            return NativeClient._apiCall('auto-updater/set-allow-prerelease', { allowPrerelease }, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 检查更新
         * 
         * @returns {Promise<boolean>}
         */
        checkForUpdates: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.checkForUpdates();
            }
            return NativeClient._apiCall('auto-updater/check-for-updates', {}, 'POST')
                .then(response => response.checking);
        },
        
        /**
         * 下载更新
         * 
         * @returns {Promise<boolean>}
         */
        downloadUpdate: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.downloadUpdate();
            }
            return NativeClient._apiCall('auto-updater/download-update', {}, 'POST')
                .then(response => response.downloading);
        },
        
        /**
         * 安装更新
         * 
         * @returns {Promise<boolean>}
         */
        installUpdate: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.installUpdate();
            }
            return NativeClient._apiCall('auto-updater/install-update', {}, 'POST')
                .then(response => response.installing);
        },
        
        /**
         * 获取当前版本
         * 
         * @returns {Promise<string>}
         */
        getCurrentVersion: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.getCurrentVersion();
            }
            return NativeClient._apiCall('auto-updater/current-version', {}, 'GET')
                .then(response => response.version);
        },
        
        /**
         * 获取最新版本
         * 
         * @returns {Promise<string|null>}
         */
        getLatestVersion: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.getLatestVersion();
            }
            return NativeClient._apiCall('auto-updater/latest-version', {}, 'GET')
                .then(response => response.version);
        },
        
        /**
         * 获取更新信息
         * 
         * @returns {Promise<object|null>}
         */
        getUpdateInfo: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.getUpdateInfo();
            }
            return NativeClient._apiCall('auto-updater/update-info', {}, 'GET')
                .then(response => response.info);
        },
        
        /**
         * 取消更新下载
         * 
         * @returns {Promise<boolean>}
         */
        cancelDownload: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.cancelDownload();
            }
            return NativeClient._apiCall('auto-updater/cancel-download', {}, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 重启应用并安装更新
         * 
         * @returns {Promise<boolean>}
         */
        quitAndInstall: function() {
            if (isElectron) {
                return window.NativePHP.autoUpdater.quitAndInstall();
            }
            return NativeClient._apiCall('auto-updater/quit-and-install', {}, 'POST')
                .then(response => response.success);
        },
        
        /**
         * 监听更新检查事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onCheckingForUpdate: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onCheckingForUpdate(callback);
            }
            window.addEventListener('native.auto-updater.checking-for-update', function(event) {
                callback(event.detail);
            });
        },
        
        /**
         * 监听更新可用事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onUpdateAvailable: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onUpdateAvailable(callback);
            }
            window.addEventListener('native.auto-updater.update-available', function(event) {
                callback(event.detail);
            });
        },
        
        /**
         * 监听更新不可用事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onUpdateNotAvailable: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onUpdateNotAvailable(callback);
            }
            window.addEventListener('native.auto-updater.update-not-available', function(event) {
                callback(event.detail);
            });
        },
        
        /**
         * 监听更新下载进度事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onDownloadProgress: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onDownloadProgress(callback);
            }
            window.addEventListener('native.auto-updater.download-progress', function(event) {
                callback(event.detail);
            });
        },
        
        /**
         * 监听更新下载完成事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onUpdateDownloaded: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onUpdateDownloaded(callback);
            }
            window.addEventListener('native.auto-updater.update-downloaded', function(event) {
                callback(event.detail);
            });
        },
        
        /**
         * 监听更新错误事件
         * 
         * @param {function} callback 回调函数
         * @returns {void}
         */
        onError: function(callback) {
            if (isElectron) {
                return window.NativePHP.autoUpdater.onError(callback);
            }
            window.addEventListener('native.auto-updater.error', function(event) {
                callback(event.detail);
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
