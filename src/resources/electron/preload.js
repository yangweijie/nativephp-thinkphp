const { contextBridge, ipcRenderer, shell } = require('electron');

// 暴露给渲染进程的 API
contextBridge.exposeInMainWorld('Native', {
    // 窗口控制
    window: {
        minimize: () => ipcRenderer.send('window-minimize'),
        maximize: () => ipcRenderer.send('window-maximize'),
        close: () => ipcRenderer.send('window-close'),
        hide: () => ipcRenderer.send('window-hide'),
        show: () => ipcRenderer.send('window-show'),
        focus: () => ipcRenderer.send('window-focus'),
        setTitle: (title) => ipcRenderer.send('window-set-title', title),
        setSize: (width, height) => ipcRenderer.send('window-set-size', { width, height }),
        setPosition: (x, y) => ipcRenderer.send('window-set-position', { x, y }),
        center: () => ipcRenderer.send('window-center'),
        setFullScreen: (fullscreen) => ipcRenderer.send('window-set-fullscreen', fullscreen),
        isFullScreen: () => ipcRenderer.invoke('window-is-fullscreen'),
        isMaximized: () => ipcRenderer.invoke('window-is-maximized'),
        isMinimized: () => ipcRenderer.invoke('window-is-minimized'),
        isVisible: () => ipcRenderer.invoke('window-is-visible'),
        isFocused: () => ipcRenderer.invoke('window-is-focused')
    },

    // 应用控制
    app: {
        exit: (code = 0) => ipcRenderer.send('app:exit', { code }),
        getVersion: () => ipcRenderer.invoke('app-get-version'),
        getName: () => ipcRenderer.invoke('app-get-name'),
        getPath: (name) => ipcRenderer.invoke('app-get-path', name),
        showAboutPanel: () => ipcRenderer.send('app-show-about-panel'),
        setAppUserModelId: (id) => ipcRenderer.send('app-set-app-user-model-id', id)
    },

    // 菜单控制
    menu: {
        popup: () => ipcRenderer.send('menu-popup'),
        setApplicationMenu: (template) => ipcRenderer.send('menu-set-application-menu', template),
        getApplicationMenu: () => ipcRenderer.invoke('menu-get-application-menu')
    },

    // 托盘控制
    tray: {
        setTooltip: (tooltip) => ipcRenderer.send('tray-set-tooltip', tooltip),
        setImage: (image) => ipcRenderer.send('tray-set-image', image),
        setContextMenu: (template) => ipcRenderer.send('tray-set-context-menu', template),
        destroy: () => ipcRenderer.send('tray-destroy')
    },

    // 对话框
    dialog: {
        showOpenDialog: (options) => ipcRenderer.invoke('dialog-show-open-dialog', options),
        showSaveDialog: (options) => ipcRenderer.invoke('dialog-show-save-dialog', options),
        showMessageBox: (options) => ipcRenderer.invoke('dialog-show-message-box', options),
        showErrorBox: (title, content) => ipcRenderer.send('dialog-show-error-box', { title, content })
    },

    // 通知
    notification: {
        show: (options) => ipcRenderer.send('notification-show', options)
    },

    // 快捷键
    globalShortcut: {
        register: (accelerator, callback) => {
            const id = Date.now().toString();
            ipcRenderer.send('global-shortcut-register', { id, accelerator });
            ipcRenderer.on(`global-shortcut-triggered-${id}`, callback);
            return id;
        },
        unregister: (id) => {
            ipcRenderer.send('global-shortcut-unregister', id);
            ipcRenderer.removeAllListeners(`global-shortcut-triggered-${id}`);
        },
        unregisterAll: () => ipcRenderer.send('global-shortcut-unregister-all')
    },

    // 外部链接
    shell: {
        openExternal: (url) => shell.openExternal(url),
        openPath: (path) => shell.openPath(path),
        showItemInFolder: (path) => shell.showItemInFolder(path)
    },

    // IPC 通信
    ipc: {
        send: (channel, data) => {
            ipcRenderer.send('ipc-message', channel, data);
        },
        on: (channel, callback) => {
            const subscription = (event, ...args) => callback(...args);
            ipcRenderer.on(channel, subscription);

            return () => {
                ipcRenderer.removeListener(channel, subscription);
            };
        },
        invoke: (channel, data) => {
            return ipcRenderer.invoke('ipc-invoke', { channel, data });
        },
        removeAllListeners: (channel) => {
            ipcRenderer.removeAllListeners(channel);
        }
    }
});
