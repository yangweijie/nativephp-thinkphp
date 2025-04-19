const { app, BrowserWindow, ipcMain, dialog, Menu, Tray, clipboard, globalShortcut, shell, nativeImage, screen } = require('electron');
const path = require('path');
const fs = require('fs');
const os = require('os');
const { exec, spawn } = require('child_process');
const Store = require('electron-store');
const { net } = require('electron');
const { autoUpdater } = require('electron-updater');

// 配置存储
const store = new Store();

// 保持对窗口对象的全局引用
let mainWindow;
let tray = null;

// 全局变量
const windows = new Map();
const menus = new Map();
const trays = new Map();
const globalShortcuts = new Map();
const fileWatchers = new Map();
const geolocationWatchers = new Map();
const settingsWatchers = new Map();
const eventListeners = new Map();

// 创建主窗口
function createWindow() {
    mainWindow = new BrowserWindow({
        width: 800,
        height: 600,
        minWidth: 400,
        minHeight: 400,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // 加载应用的 URL
    mainWindow.loadURL('http://localhost:8000');

    // 当窗口关闭时调用的方法
    mainWindow.on('closed', function() {
        mainWindow = null;
    });

    // 保存窗口引用
    windows.set('main', mainWindow);
}

// 当 Electron 完成初始化并准备创建浏览器窗口时调用此方法
app.on('ready', createWindow);

// 当所有窗口关闭时退出应用
app.on('window-all-closed', function() {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', function() {
    if (mainWindow === null) {
        createWindow();
    }
});

// 应用相关 IPC 处理程序
ipcMain.handle('app:getName', () => app.getName());
ipcMain.handle('app:getVersion', () => app.getVersion());
ipcMain.handle('app:getPath', (event, name) => app.getPath(name));
ipcMain.on('app:quit', () => app.quit());
ipcMain.on('app:relaunch', () => {
    app.relaunch();
    app.exit();
});
ipcMain.on('app:focus', () => app.focus());
ipcMain.handle('app:isPackaged', () => app.isPackaged);
ipcMain.handle('app:getAppPath', () => app.getAppPath());
ipcMain.handle('app:getLocale', () => app.getLocale());
ipcMain.handle('app:getSystemLocale', () => app.getSystemLocale());

// 窗口相关 IPC 处理程序
ipcMain.handle('window:open', (event, url, options = {}) => {
    const windowId = options.id || Date.now().toString();
    const win = new BrowserWindow({
        width: options.width || 800,
        height: options.height || 600,
        minWidth: options.minWidth || 400,
        minHeight: options.minHeight || 400,
        maxWidth: options.maxWidth || null,
        maxHeight: options.maxHeight || null,
        resizable: options.resizable !== false,
        fullscreen: options.fullscreen || false,
        title: options.title || app.getName(),
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // 加载 URL
    if (url.startsWith('http://') || url.startsWith('https://')) {
        win.loadURL(url);
    } else {
        win.loadURL(`http://localhost:8000${url}`);
    }

    // 保存窗口引用
    windows.set(windowId, win);

    // 当窗口关闭时移除引用
    win.on('closed', () => {
        windows.delete(windowId);
    });

    return windowId;
});

ipcMain.on('window:close', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.close();
    }
});

ipcMain.on('window:minimize', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.minimize();
    }
});

ipcMain.on('window:maximize', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.maximize();
    }
});

ipcMain.on('window:unmaximize', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.unmaximize();
    }
});

ipcMain.handle('window:isMaximized', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return win ? win.isMaximized() : false;
});

ipcMain.handle('window:isMinimized', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return win ? win.isMinimized() : false;
});

ipcMain.handle('window:isVisible', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return win ? win.isVisible() : false;
});

ipcMain.handle('window:isFocused', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return win ? win.isFocused() : false;
});

ipcMain.on('window:show', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.show();
    }
});

ipcMain.on('window:hide', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.hide();
    }
});

ipcMain.on('window:focus', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.focus();
    }
});

ipcMain.on('window:blur', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.blur();
    }
});

ipcMain.on('window:setTitle', (event, title) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.setTitle(title);
    }
});

ipcMain.on('window:setSize', (event, width, height) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.setSize(width, height);
    }
});

ipcMain.on('window:setPosition', (event, x, y) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.setPosition(x, y);
    }
});

ipcMain.on('window:center', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.center();
    }
});

ipcMain.on('window:setFullScreen', (event, flag) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.setFullScreen(flag);
    }
});

ipcMain.handle('window:isFullScreen', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return win ? win.isFullScreen() : false;
});

ipcMain.on('window:reload', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        win.reload();
    }
});

ipcMain.handle('window:getCurrentWindow', (event) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    if (win) {
        for (const [id, window] of windows.entries()) {
            if (window === win) {
                return id;
            }
        }
    }
    return null;
});

// 对话框相关 IPC 处理程序
ipcMain.handle('dialog:openFile', async (event, options = {}) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    const result = await dialog.showOpenDialog(win, {
        title: options.title,
        defaultPath: options.defaultPath,
        buttonLabel: options.buttonLabel,
        filters: options.filters || [],
        properties: options.properties || ['openFile']
    });
    return result.canceled ? null : (options.properties && options.properties.includes('multiSelections') ? result.filePaths : result.filePaths[0]);
});

ipcMain.handle('dialog:saveFile', async (event, options = {}) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    const result = await dialog.showSaveDialog(win, {
        title: options.title,
        defaultPath: options.defaultPath,
        buttonLabel: options.buttonLabel,
        filters: options.filters || []
    });
    return result.canceled ? null : result.filePath;
});

ipcMain.handle('dialog:selectFolder', async (event, options = {}) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    const result = await dialog.showOpenDialog(win, {
        title: options.title,
        defaultPath: options.defaultPath,
        buttonLabel: options.buttonLabel,
        properties: ['openDirectory']
    });
    return result.canceled ? null : result.filePaths[0];
});

ipcMain.handle('dialog:showMessageBox', async (event, options = {}) => {
    const win = BrowserWindow.fromWebContents(event.sender);
    return await dialog.showMessageBox(win, options);
});

ipcMain.handle('dialog:showErrorBox', (event, title, content) => {
    dialog.showErrorBox(title, content);
    return true;
});

// 通知相关 IPC 处理程序
ipcMain.handle('notification:send', (event, title, body, options = {}) => {
    const notification = new Notification({
        title,
        body,
        silent: options.silent || false,
        icon: options.icon ? nativeImage.createFromPath(options.icon) : undefined
    });
    notification.show();
    return true;
});

ipcMain.handle('notification:sendWithIcon', (event, title, body, icon, options = {}) => {
    const notification = new Notification({
        title,
        body,
        silent: options.silent || false,
        icon: nativeImage.createFromPath(icon)
    });
    notification.show();
    return true;
});

ipcMain.handle('notification:sendWithSound', (event, title, body, sound, options = {}) => {
    const notification = new Notification({
        title,
        body,
        silent: false,
        icon: options.icon ? nativeImage.createFromPath(options.icon) : undefined
    });
    notification.show();
    // 播放声音
    // 在实际实现中，需要使用其他方法播放声音
    return true;
});

ipcMain.handle('notification:sendWithActions', (event, title, body, actions, options = {}) => {
    const notification = new Notification({
        title,
        body,
        silent: options.silent || false,
        icon: options.icon ? nativeImage.createFromPath(options.icon) : undefined,
        actions: actions.map(action => ({
            type: 'button',
            text: action.text
        }))
    });
    notification.show();
    return true;
});

// 菜单相关 IPC 处理程序
ipcMain.handle('menu:create', (event, template) => {
    const menuId = Date.now().toString();
    const menu = Menu.buildFromTemplate(template);
    menus.set(menuId, menu);
    return menuId;
});

ipcMain.on('menu:popup', (event, menuId, options = {}) => {
    const menu = menus.get(menuId);
    if (menu) {
        const win = BrowserWindow.fromWebContents(event.sender);
        menu.popup({
            window: win,
            x: options.x,
            y: options.y
        });
    }
});

ipcMain.on('menu:setApplicationMenu', (event, menuId) => {
    const menu = menus.get(menuId);
    if (menu) {
        Menu.setApplicationMenu(menu);
    } else {
        Menu.setApplicationMenu(null);
    }
});

ipcMain.handle('menu:getApplicationMenu', () => {
    const menu = Menu.getApplicationMenu();
    return menu ? menu.items.map(item => ({
        label: item.label,
        enabled: item.enabled,
        visible: item.visible,
        checked: item.checked,
        role: item.role,
        type: item.type,
        submenu: item.submenu ? item.submenu.items.map(subItem => ({
            label: subItem.label,
            enabled: subItem.enabled,
            visible: subItem.visible,
            checked: subItem.checked,
            role: subItem.role,
            type: subItem.type
        })) : undefined
    })) : null;
});

// 全局快捷键相关 IPC 处理程序
ipcMain.on('globalShortcut:register', (event, accelerator, id) => {
    if (!globalShortcuts.has(accelerator)) {
        const success = globalShortcut.register(accelerator, () => {
            event.sender.send(`globalShortcut:${id}`);
        });
        if (success) {
            globalShortcuts.set(accelerator, {
                id,
                webContents: event.sender
            });
        }
    }
});

ipcMain.on('globalShortcut:unregister', (event, id) => {
    for (const [accelerator, shortcut] of globalShortcuts.entries()) {
        if (shortcut.id === id && shortcut.webContents === event.sender) {
            globalShortcut.unregister(accelerator);
            globalShortcuts.delete(accelerator);
            break;
        }
    }
});

ipcMain.handle('globalShortcut:isRegistered', (event, accelerator) => {
    return globalShortcut.isRegistered(accelerator);
});

ipcMain.on('globalShortcut:unregisterAll', () => {
    globalShortcut.unregisterAll();
    globalShortcuts.clear();
});

// 系统托盘相关 IPC 处理程序
ipcMain.handle('tray:create', (event, icon, tooltip) => {
    const trayId = Date.now().toString();
    const trayObj = new Tray(nativeImage.createFromPath(icon));
    if (tooltip) {
        trayObj.setToolTip(tooltip);
    }
    trays.set(trayId, trayObj);
    return trayId;
});

ipcMain.on('tray:setImage', (event, trayId, icon) => {
    const trayObj = trays.get(trayId);
    if (trayObj) {
        trayObj.setImage(nativeImage.createFromPath(icon));
    }
});

ipcMain.on('tray:setTooltip', (event, trayId, tooltip) => {
    const trayObj = trays.get(trayId);
    if (trayObj) {
        trayObj.setToolTip(tooltip);
    }
});

ipcMain.on('tray:setMenu', (event, trayId, menuId) => {
    const trayObj = trays.get(trayId);
    const menu = menus.get(menuId);
    if (trayObj && menu) {
        trayObj.setContextMenu(menu);
    }
});

ipcMain.on('tray:destroy', (event, trayId) => {
    const trayObj = trays.get(trayId);
    if (trayObj) {
        trayObj.destroy();
        trays.delete(trayId);
    }
});

ipcMain.on('tray:show', (event, trayId) => {
    const trayObj = trays.get(trayId);
    if (trayObj) {
        trayObj.setVisible(true);
    }
});

ipcMain.on('tray:hide', (event, trayId) => {
    const trayObj = trays.get(trayId);
    if (trayObj) {
        trayObj.setVisible(false);
    }
});

ipcMain.handle('tray:isVisible', (event, trayId) => {
    const trayObj = trays.get(trayId);
    return trayObj ? trayObj.isVisible() : false;
});

// 文件系统相关 IPC 处理程序
ipcMain.handle('fileSystem:read', (event, path) => {
    try {
        return fs.readFileSync(path, 'utf8');
    } catch (error) {
        return null;
    }
});

ipcMain.handle('fileSystem:write', (event, path, content) => {
    try {
        fs.writeFileSync(path, content, 'utf8');
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:append', (event, path, content) => {
    try {
        fs.appendFileSync(path, content, 'utf8');
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:exists', (event, path) => {
    return fs.existsSync(path);
});

ipcMain.handle('fileSystem:delete', (event, path) => {
    try {
        fs.unlinkSync(path);
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:copy', (event, source, destination) => {
    try {
        fs.copyFileSync(source, destination);
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:move', (event, source, destination) => {
    try {
        fs.renameSync(source, destination);
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:makeDirectory', (event, path, recursive = true) => {
    try {
        fs.mkdirSync(path, { recursive });
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:deleteDirectory', (event, path, recursive = true) => {
    try {
        if (recursive) {
            fs.rmdirSync(path, { recursive });
        } else {
            fs.rmdirSync(path);
        }
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('fileSystem:getFiles', (event, path) => {
    try {
        return fs.readdirSync(path, { withFileTypes: true })
            .filter(dirent => dirent.isFile())
            .map(dirent => dirent.name);
    } catch (error) {
        return [];
    }
});

ipcMain.handle('fileSystem:getDirectories', (event, path) => {
    try {
        return fs.readdirSync(path, { withFileTypes: true })
            .filter(dirent => dirent.isDirectory())
            .map(dirent => dirent.name);
    } catch (error) {
        return [];
    }
});

ipcMain.handle('fileSystem:getSize', (event, path) => {
    try {
        const stats = fs.statSync(path);
        return stats.size;
    } catch (error) {
        return -1;
    }
});

ipcMain.handle('fileSystem:getLastModified', (event, path) => {
    try {
        const stats = fs.statSync(path);
        return stats.mtime.getTime();
    } catch (error) {
        return -1;
    }
});

ipcMain.handle('fileSystem:getPermissions', (event, path) => {
    try {
        const stats = fs.statSync(path);
        return stats.mode & 0o777;
    } catch (error) {
        return -1;
    }
});

ipcMain.handle('fileSystem:setPermissions', (event, path, mode) => {
    try {
        fs.chmodSync(path, mode);
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.on('fileSystem:watch', (event, path, id) => {
    try {
        const watcher = fs.watch(path, (eventType, filename) => {
            event.sender.send(`fileSystem:watch:${id}`, eventType, filename);
        });
        fileWatchers.set(id, watcher);
    } catch (error) {
        // 忽略错误
    }
});

ipcMain.on('fileSystem:unwatch', (event, id) => {
    const watcher = fileWatchers.get(id);
    if (watcher) {
        watcher.close();
        fileWatchers.delete(id);
    }
});

// 系统相关 IPC 处理程序
ipcMain.handle('system:getPlatform', () => process.platform);
ipcMain.handle('system:getArch', () => process.arch);
ipcMain.handle('system:getHostname', () => os.hostname());
ipcMain.handle('system:getUsername', () => os.userInfo().username);
ipcMain.handle('system:getHomePath', () => os.homedir());
ipcMain.handle('system:getTempPath', () => os.tmpdir());
ipcMain.handle('system:getDesktopPath', () => app.getPath('desktop'));
ipcMain.handle('system:getDocumentsPath', () => app.getPath('documents'));
ipcMain.handle('system:getDownloadsPath', () => app.getPath('downloads'));
ipcMain.handle('system:getMusicPath', () => app.getPath('music'));
ipcMain.handle('system:getPicturesPath', () => app.getPath('pictures'));
ipcMain.handle('system:getVideosPath', () => app.getPath('videos'));
ipcMain.handle('system:getCPUs', () => os.cpus());
ipcMain.handle('system:getMemory', () => ({
    total: os.totalmem(),
    free: os.freemem()
}));
ipcMain.handle('system:getNetworkInterfaces', () => os.networkInterfaces());
ipcMain.handle('system:getBattery', async () => {
    // 在实际实现中，需要使用其他方法获取电池信息
    return {
        level: 1.0,
        charging: true
    };
});
ipcMain.handle('system:getDisplays', () => screen.getAllDisplays());

// 屏幕相关 IPC 处理程序
ipcMain.handle('screen:getCursorScreenPoint', () => screen.getCursorScreenPoint());
ipcMain.handle('screen:getPrimaryDisplay', () => screen.getPrimaryDisplay());
ipcMain.handle('screen:getAllDisplays', () => screen.getAllDisplays());
ipcMain.handle('screen:getDisplayNearestPoint', (event, point) => screen.getDisplayNearestPoint(point));
ipcMain.handle('screen:getDisplayMatching', (event, rect) => screen.getDisplayMatching(rect));
ipcMain.handle('screen:capture', async (event, options = {}) => {
    const display = options.display ? screen.getDisplayMatching(options.display) : screen.getPrimaryDisplay();
    const image = await screen.captureScreen(display.bounds);
    return image.toDataURL();
});

// 设置相关 IPC 处理程序
ipcMain.handle('settings:get', (event, key, defaultValue) => {
    return store.get(key, defaultValue);
});

ipcMain.handle('settings:set', (event, key, value) => {
    store.set(key, value);
    
    // 触发监听器
    for (const [watchId, watchInfo] of settingsWatchers.entries()) {
        if (watchInfo.key === key && watchInfo.webContents === event.sender) {
            event.sender.send(`settings:watch:${watchId}`, value);
        }
    }
    
    return true;
});

ipcMain.handle('settings:has', (event, key) => {
    return store.has(key);
});

ipcMain.handle('settings:delete', (event, key) => {
    store.delete(key);
    return true;
});

ipcMain.handle('settings:all', () => {
    return store.store;
});

ipcMain.handle('settings:clear', () => {
    store.clear();
    return true;
});

ipcMain.handle('settings:export', (event, path) => {
    try {
        fs.writeFileSync(path, JSON.stringify(store.store, null, 2), 'utf8');
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.handle('settings:import', (event, path) => {
    try {
        const data = JSON.parse(fs.readFileSync(path, 'utf8'));
        store.store = data;
        return true;
    } catch (error) {
        return false;
    }
});

ipcMain.on('settings:watch', (event, key, id) => {
    settingsWatchers.set(id, {
        key,
        webContents: event.sender
    });
});

ipcMain.on('settings:unwatch', (event, id) => {
    settingsWatchers.delete(id);
});

// 事件工具相关 IPC 处理程序
ipcMain.on('event:on', (event, eventName, id) => {
    if (!eventListeners.has(eventName)) {
        eventListeners.set(eventName, new Map());
    }
    eventListeners.get(eventName).set(id, {
        webContents: event.sender,
        once: false
    });
});

ipcMain.on('event:once', (event, eventName, id) => {
    if (!eventListeners.has(eventName)) {
        eventListeners.set(eventName, new Map());
    }
    eventListeners.get(eventName).set(id, {
        webContents: event.sender,
        once: true
    });
});

ipcMain.on('event:off', (event, eventName, id) => {
    if (eventListeners.has(eventName)) {
        eventListeners.get(eventName).delete(id);
        if (eventListeners.get(eventName).size === 0) {
            eventListeners.delete(eventName);
        }
    }
});

ipcMain.on('event:emit', (event, eventName, ...args) => {
    if (eventListeners.has(eventName)) {
        const listeners = eventListeners.get(eventName);
        for (const [id, listener] of listeners.entries()) {
            listener.webContents.send(`event:${eventName}:${id}`, ...args);
            if (listener.once) {
                listeners.delete(id);
            }
        }
        if (listeners.size === 0) {
            eventListeners.delete(eventName);
        }
    }
});

ipcMain.handle('event:listeners', (event, eventName) => {
    if (eventListeners.has(eventName)) {
        return Array.from(eventListeners.get(eventName).keys());
    }
    return [];
});

ipcMain.handle('event:listenerCount', (event, eventName) => {
    if (eventListeners.has(eventName)) {
        return eventListeners.get(eventName).size;
    }
    return 0;
});

ipcMain.handle('event:eventNames', () => {
    return Array.from(eventListeners.keys());
});

// 应用退出前清理资源
app.on('will-quit', () => {
    // 注销所有全局快捷键
    globalShortcut.unregisterAll();
    globalShortcuts.clear();
    
    // 关闭所有文件监视器
    for (const watcher of fileWatchers.values()) {
        watcher.close();
    }
    fileWatchers.clear();
    
    // 销毁所有托盘图标
    for (const trayObj of trays.values()) {
        trayObj.destroy();
    }
    trays.clear();
});
