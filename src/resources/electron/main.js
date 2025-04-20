const { app, BrowserWindow, ipcMain, Menu, Tray, globalShortcut, shell, nativeTheme, dialog } = require('electron');
const path = require('path');
const url = require('url');
const fs = require('fs');

// 环境变量
const host = process.env.NATIVEPHP_SERVE_HOST || '127.0.0.1';
const port = process.env.NATIVEPHP_SERVE_PORT || 8000;
const noReload = process.env.NATIVEPHP_SERVE_NO_RELOAD === 'true';
const devTools = process.env.NATIVEPHP_DEV_TOOLS === 'true';

// 全局变量
let mainWindow = null;
let tray = null;
let appIcon = null;
let appMenu = null;
let isQuitting = false;

// 应用配置
const appConfig = {
    name: 'NativePHP App',
    version: '1.0.0',
    icon: null,
    tray: {
        icon: null,
        tooltip: 'NativePHP App',
        menuItems: []
    },
    window: {
        width: 800,
        height: 600,
        center: true,
        title: 'NativePHP App',
        vibrancy: null,
        transparent: false,
        frame: true,
        hasShadow: true
    }
};

// 加载配置
function loadConfig() {
    try {
        const configUrl = url.format({
            protocol: 'http',
            hostname: host,
            port: port,
            pathname: '/native-php/config'
        });

        fetch(configUrl)
            .then(response => response.json())
            .then(config => {
                Object.assign(appConfig, config);
                updateAppConfig();
            })
            .catch(error => {
                console.error('Failed to load config:', error);
            });
    } catch (error) {
        console.error('Error loading config:', error);
    }
}

// 更新应用配置
function updateAppConfig() {
    if (mainWindow && !mainWindow.isDestroyed()) {
        // 更新窗口配置
        mainWindow.setTitle(appConfig.window.title);
        mainWindow.setSize(appConfig.window.width, appConfig.window.height);
        if (appConfig.window.center) {
            mainWindow.center();
        }
    }

    // 更新托盘图标
    updateTray();

    // 更新应用菜单
    updateMenu();
}

// 创建主窗口
function createMainWindow() {
    mainWindow = new BrowserWindow({
        width: appConfig.window.width,
        height: appConfig.window.height,
        title: appConfig.window.title,
        center: appConfig.window.center,
        vibrancy: appConfig.window.vibrancy,
        transparent: appConfig.window.transparent,
        frame: appConfig.window.frame,
        hasShadow: appConfig.window.hasShadow,
        icon: appConfig.icon,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // 加载应用
    mainWindow.loadURL(url.format({
        protocol: 'http',
        hostname: host,
        port: port,
        pathname: '/'
    }));

    // 开发者工具
    if (devTools) {
        mainWindow.webContents.openDevTools();
    }

    // 窗口关闭事件
    mainWindow.on('close', (event) => {
        if (!isQuitting) {
            event.preventDefault();
            mainWindow.hide();
            return false;
        }
    });

    // 窗口关闭后事件
    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    // 加载配置
    loadConfig();
}

// 更新托盘图标
function updateTray() {
    if (tray && !tray.isDestroyed()) {
        tray.destroy();
    }

    // 创建托盘图标
    const trayIcon = appConfig.tray.icon || appConfig.icon;
    if (trayIcon) {
        tray = new Tray(trayIcon);
        tray.setToolTip(appConfig.tray.tooltip || appConfig.name);

        // 创建托盘菜单
        const contextMenu = Menu.buildFromTemplate([
            {
                label: '显示应用',
                click: () => {
                    if (mainWindow) {
                        mainWindow.show();
                    }
                }
            },
            { type: 'separator' },
            {
                label: '退出',
                click: () => {
                    isQuitting = true;
                    app.quit();
                }
            }
        ]);

        tray.setContextMenu(contextMenu);

        // 点击托盘图标显示应用
        tray.on('click', () => {
            if (mainWindow) {
                mainWindow.show();
            }
        });
    }
}

// 更新应用菜单
function updateMenu() {
    // 默认菜单
    const template = [
        {
            label: '文件',
            submenu: [
                {
                    label: '退出',
                    accelerator: 'CmdOrCtrl+Q',
                    click: () => {
                        isQuitting = true;
                        app.quit();
                    }
                }
            ]
        },
        {
            label: '编辑',
            submenu: [
                { label: '撤销', accelerator: 'CmdOrCtrl+Z', role: 'undo' },
                { label: '重做', accelerator: 'Shift+CmdOrCtrl+Z', role: 'redo' },
                { type: 'separator' },
                { label: '剪切', accelerator: 'CmdOrCtrl+X', role: 'cut' },
                { label: '复制', accelerator: 'CmdOrCtrl+C', role: 'copy' },
                { label: '粘贴', accelerator: 'CmdOrCtrl+V', role: 'paste' },
                { label: '全选', accelerator: 'CmdOrCtrl+A', role: 'selectAll' }
            ]
        },
        {
            label: '视图',
            submenu: [
                { label: '重新加载', accelerator: 'CmdOrCtrl+R', role: 'reload' },
                { label: '强制重新加载', accelerator: 'CmdOrCtrl+Shift+R', role: 'forceReload' },
                { type: 'separator' },
                { label: '实际大小', accelerator: 'CmdOrCtrl+0', role: 'resetZoom' },
                { label: '放大', accelerator: 'CmdOrCtrl+Plus', role: 'zoomIn' },
                { label: '缩小', accelerator: 'CmdOrCtrl+-', role: 'zoomOut' },
                { type: 'separator' },
                { label: '切换全屏', accelerator: 'F11', role: 'togglefullscreen' }
            ]
        },
        {
            label: '窗口',
            submenu: [
                { label: '最小化', accelerator: 'CmdOrCtrl+M', role: 'minimize' },
                { label: '关闭', accelerator: 'CmdOrCtrl+W', role: 'close' }
            ]
        },
        {
            label: '帮助',
            submenu: [
                {
                    label: '关于',
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            title: '关于',
                            message: `${appConfig.name} v${appConfig.version}`,
                            detail: 'NativePHP for ThinkPHP',
                            buttons: ['确定']
                        });
                    }
                }
            ]
        }
    ];

    appMenu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(appMenu);
}

// 注册 IPC 事件处理器
function registerIpcHandlers() {
    // ========== 窗口控制 ==========
    ipcMain.on('window-minimize', () => {
        if (mainWindow) {
            mainWindow.minimize();
        }
    });

    ipcMain.on('window-maximize', () => {
        if (mainWindow) {
            if (mainWindow.isMaximized()) {
                mainWindow.unmaximize();
            } else {
                mainWindow.maximize();
            }
        }
    });

    ipcMain.on('window-close', () => {
        if (mainWindow) {
            mainWindow.close();
        }
    });

    ipcMain.on('window-hide', () => {
        if (mainWindow) {
            mainWindow.hide();
        }
    });

    ipcMain.on('window-show', () => {
        if (mainWindow) {
            mainWindow.show();
        }
    });

    ipcMain.on('window-focus', () => {
        if (mainWindow) {
            mainWindow.focus();
        }
    });

    ipcMain.on('window-set-title', (event, title) => {
        if (mainWindow) {
            mainWindow.setTitle(title);
        }
    });

    ipcMain.on('window-set-size', (event, { width, height }) => {
        if (mainWindow) {
            mainWindow.setSize(width, height);
        }
    });

    ipcMain.on('window-set-position', (event, { x, y }) => {
        if (mainWindow) {
            mainWindow.setPosition(x, y);
        }
    });

    ipcMain.on('window-center', () => {
        if (mainWindow) {
            mainWindow.center();
        }
    });

    ipcMain.on('window-set-fullscreen', (event, fullscreen) => {
        if (mainWindow) {
            mainWindow.setFullScreen(fullscreen);
        }
    });

    ipcMain.handle('window-is-fullscreen', () => {
        return mainWindow ? mainWindow.isFullScreen() : false;
    });

    ipcMain.handle('window-is-maximized', () => {
        return mainWindow ? mainWindow.isMaximized() : false;
    });

    ipcMain.handle('window-is-minimized', () => {
        return mainWindow ? mainWindow.isMinimized() : false;
    });

    ipcMain.handle('window-is-visible', () => {
        return mainWindow ? mainWindow.isVisible() : false;
    });

    ipcMain.handle('window-is-focused', () => {
        return mainWindow ? mainWindow.isFocused() : false;
    });

    // ========== 应用控制 ==========
    ipcMain.on('app:exit', (event, data) => {
        const code = data?.code || 0;
        app.exit(code);
    });

    ipcMain.handle('app-get-version', () => {
        return app.getVersion();
    });

    ipcMain.handle('app-get-name', () => {
        return app.getName();
    });

    ipcMain.handle('app-get-path', (event, name) => {
        return app.getPath(name);
    });

    ipcMain.on('app-show-about-panel', () => {
        app.showAboutPanel();
    });

    ipcMain.on('app-set-app-user-model-id', (event, id) => {
        app.setAppUserModelId(id);
    });

    // ========== 菜单控制 ==========
    ipcMain.on('menu-popup', () => {
        if (appMenu) {
            appMenu.popup();
        }
    });

    ipcMain.on('menu-set-application-menu', (event, template) => {
        const menu = Menu.buildFromTemplate(template);
        Menu.setApplicationMenu(menu);
        appMenu = menu;
    });

    ipcMain.handle('menu-get-application-menu', () => {
        return Menu.getApplicationMenu();
    });

    // ========== 托盘控制 ==========
    ipcMain.on('tray-set-tooltip', (event, tooltip) => {
        if (tray) {
            tray.setToolTip(tooltip);
        }
    });

    ipcMain.on('tray-set-image', (event, image) => {
        if (tray) {
            tray.setImage(image);
        }
    });

    ipcMain.on('tray-set-context-menu', (event, template) => {
        if (tray) {
            const menu = Menu.buildFromTemplate(template);
            tray.setContextMenu(menu);
        }
    });

    ipcMain.on('tray-destroy', () => {
        if (tray) {
            tray.destroy();
            tray = null;
        }
    });

    // ========== 对话框 ==========
    ipcMain.handle('dialog-show-open-dialog', (event, options) => {
        return dialog.showOpenDialog(options);
    });

    ipcMain.handle('dialog-show-save-dialog', (event, options) => {
        return dialog.showSaveDialog(options);
    });

    ipcMain.handle('dialog-show-message-box', (event, options) => {
        return dialog.showMessageBox(options);
    });

    ipcMain.on('dialog-show-error-box', (event, { title, content }) => {
        dialog.showErrorBox(title, content);
    });

    // ========== 通知 ==========
    ipcMain.on('notification-show', (event, options) => {
        const notification = new Notification(options);
        notification.show();
    });

    // ========== 快捷键 ==========
    let shortcutCallbacks = {};

    ipcMain.on('global-shortcut-register', (event, { id, accelerator }) => {
        globalShortcut.register(accelerator, () => {
            if (mainWindow && !mainWindow.isDestroyed()) {
                mainWindow.webContents.send(`global-shortcut-triggered-${id}`);
            }
        });
        shortcutCallbacks[id] = accelerator;
    });

    ipcMain.on('global-shortcut-unregister', (event, id) => {
        if (shortcutCallbacks[id]) {
            globalShortcut.unregister(shortcutCallbacks[id]);
            delete shortcutCallbacks[id];
        }
    });

    ipcMain.on('global-shortcut-unregister-all', () => {
        globalShortcut.unregisterAll();
        shortcutCallbacks = {};
    });

    // ========== IPC 通信 ==========
    ipcMain.on('ipc-message', (event, channel, data) => {
        if (mainWindow && !mainWindow.isDestroyed()) {
            mainWindow.webContents.send(channel, data);
        }

        // 将消息发送到服务器
        sendToServer('ipc', { channel, payload: data });
    });

    ipcMain.handle('ipc-invoke', (event, { channel, data }) => {
        // 将调用请求发送到服务器
        return fetch(url.format({
            protocol: 'http',
            hostname: host,
            port: port,
            pathname: '/native-php/invoke'
        }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ channel, payload: data })
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Error invoking method:', error);
            return { error: error.message };
        });
    });
}

// 发送数据到服务器
function sendToServer(endpoint, data) {
    try {
        const serverUrl = url.format({
            protocol: 'http',
            hostname: host,
            port: port,
            pathname: '/native-php/' + endpoint
        });

        fetch(serverUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(error => {
            console.error('Error sending data to server:', error);
        });
    } catch (error) {
        console.error('Error sending data to server:', error);
    }
}

// 注册全局快捷键
function registerGlobalShortcuts() {
    // 示例：注册 Ctrl+Shift+F 快捷键
    globalShortcut.register('CommandOrControl+Shift+F', () => {
        if (mainWindow) {
            mainWindow.show();
            mainWindow.focus();
        }
    });
}

// 应用就绪事件
app.whenReady().then(() => {
    createMainWindow();
    registerIpcHandlers();
    registerGlobalShortcuts();

    // macOS 应用激活事件
    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createMainWindow();
        } else if (mainWindow) {
            mainWindow.show();
        }
    });
});

// 所有窗口关闭事件
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

// 应用退出前事件
app.on('before-quit', () => {
    isQuitting = true;
});

// 注销全局快捷键
app.on('will-quit', () => {
    globalShortcut.unregisterAll();
});
