// preload.js
const { contextBridge, ipcRenderer, clipboard, shell, nativeImage } = require('electron');

// 暴露安全的 API 到渲染进程
contextBridge.exposeInMainWorld('NativePHP', {
  // 应用相关
  app: {
    getName: () => ipcRenderer.invoke('app:getName'),
    getVersion: () => ipcRenderer.invoke('app:getVersion'),
    getPath: (name) => ipcRenderer.invoke('app:getPath', name),
    quit: () => ipcRenderer.send('app:quit'),
    relaunch: () => ipcRenderer.send('app:relaunch'),
    focus: () => ipcRenderer.send('app:focus'),
    isPackaged: () => ipcRenderer.invoke('app:isPackaged'),
    getAppPath: () => ipcRenderer.invoke('app:getAppPath'),
    getLocale: () => ipcRenderer.invoke('app:getLocale'),
    getSystemLocale: () => ipcRenderer.invoke('app:getSystemLocale'),
  },

  // 窗口相关
  window: {
    open: (url, options) => ipcRenderer.invoke('window:open', url, options),
    close: () => ipcRenderer.send('window:close'),
    minimize: () => ipcRenderer.send('window:minimize'),
    maximize: () => ipcRenderer.send('window:maximize'),
    unmaximize: () => ipcRenderer.send('window:unmaximize'),
    isMaximized: () => ipcRenderer.invoke('window:isMaximized'),
    isMinimized: () => ipcRenderer.invoke('window:isMinimized'),
    isVisible: () => ipcRenderer.invoke('window:isVisible'),
    isFocused: () => ipcRenderer.invoke('window:isFocused'),
    show: () => ipcRenderer.send('window:show'),
    hide: () => ipcRenderer.send('window:hide'),
    focus: () => ipcRenderer.send('window:focus'),
    blur: () => ipcRenderer.send('window:blur'),
    setTitle: (title) => ipcRenderer.send('window:setTitle', title),
    setSize: (width, height) => ipcRenderer.send('window:setSize', width, height),
    setPosition: (x, y) => ipcRenderer.send('window:setPosition', x, y),
    center: () => ipcRenderer.send('window:center'),
    setFullScreen: (flag) => ipcRenderer.send('window:setFullScreen', flag),
    isFullScreen: () => ipcRenderer.invoke('window:isFullScreen'),
    reload: () => ipcRenderer.send('window:reload'),
    getCurrentWindow: () => ipcRenderer.invoke('window:getCurrentWindow'),
  },

  // 对话框相关
  dialog: {
    openFile: (options) => ipcRenderer.invoke('dialog:openFile', options),
    saveFile: (options) => ipcRenderer.invoke('dialog:saveFile', options),
    selectFolder: (options) => ipcRenderer.invoke('dialog:selectFolder', options),
    showMessageBox: (options) => ipcRenderer.invoke('dialog:showMessageBox', options),
    showErrorBox: (title, content) => ipcRenderer.invoke('dialog:showErrorBox', title, content),
  },

  // 通知相关
  notification: {
    send: (title, body, options) => ipcRenderer.invoke('notification:send', title, body, options),
    sendWithIcon: (title, body, icon, options) => ipcRenderer.invoke('notification:sendWithIcon', title, body, icon, options),
    sendWithSound: (title, body, sound, options) => ipcRenderer.invoke('notification:sendWithSound', title, body, sound, options),
    sendWithActions: (title, body, actions, options) => ipcRenderer.invoke('notification:sendWithActions', title, body, actions, options),
  },

  // 菜单相关
  menu: {
    create: (template) => ipcRenderer.invoke('menu:create', template),
    popup: (menuId, options) => ipcRenderer.send('menu:popup', menuId, options),
    setApplicationMenu: (menuId) => ipcRenderer.send('menu:setApplicationMenu', menuId),
    getApplicationMenu: () => ipcRenderer.invoke('menu:getApplicationMenu'),
  },

  // 剪贴板相关
  clipboard: {
    setText: (text) => clipboard.writeText(text),
    text: () => clipboard.readText(),
    setHTML: (html) => clipboard.writeHTML(html),
    html: () => clipboard.readHTML(),
    setImage: (imagePath) => {
      const image = nativeImage.createFromPath(imagePath);
      clipboard.writeImage(image);
    },
    image: () => clipboard.readImage().toDataURL(),
    clear: () => clipboard.clear(),
    has: (format) => clipboard.has(format),
    formats: () => clipboard.availableFormats(),
  },

  // 全局快捷键相关
  globalShortcut: {
    register: (accelerator, callback) => {
      const id = Date.now().toString();
      ipcRenderer.on(`globalShortcut:${id}`, callback);
      ipcRenderer.send('globalShortcut:register', accelerator, id);
      return id;
    },
    unregister: (id) => {
      ipcRenderer.removeAllListeners(`globalShortcut:${id}`);
      ipcRenderer.send('globalShortcut:unregister', id);
    },
    isRegistered: (accelerator) => ipcRenderer.invoke('globalShortcut:isRegistered', accelerator),
    unregisterAll: () => ipcRenderer.send('globalShortcut:unregisterAll'),
  },

  // 系统托盘相关
  tray: {
    create: (icon, tooltip) => ipcRenderer.invoke('tray:create', icon, tooltip),
    setImage: (trayId, icon) => ipcRenderer.send('tray:setImage', trayId, icon),
    setTooltip: (trayId, tooltip) => ipcRenderer.send('tray:setTooltip', trayId, tooltip),
    setMenu: (trayId, menuId) => ipcRenderer.send('tray:setMenu', trayId, menuId),
    destroy: (trayId) => ipcRenderer.send('tray:destroy', trayId),
    show: (trayId) => ipcRenderer.send('tray:show', trayId),
    hide: (trayId) => ipcRenderer.send('tray:hide', trayId),
    isVisible: (trayId) => ipcRenderer.invoke('tray:isVisible', trayId),
  },

  // 文件系统相关
  fileSystem: {
    read: (path) => ipcRenderer.invoke('fileSystem:read', path),
    write: (path, content) => ipcRenderer.invoke('fileSystem:write', path, content),
    append: (path, content) => ipcRenderer.invoke('fileSystem:append', path, content),
    exists: (path) => ipcRenderer.invoke('fileSystem:exists', path),
    delete: (path) => ipcRenderer.invoke('fileSystem:delete', path),
    copy: (source, destination) => ipcRenderer.invoke('fileSystem:copy', source, destination),
    move: (source, destination) => ipcRenderer.invoke('fileSystem:move', source, destination),
    makeDirectory: (path, recursive) => ipcRenderer.invoke('fileSystem:makeDirectory', path, recursive),
    deleteDirectory: (path, recursive) => ipcRenderer.invoke('fileSystem:deleteDirectory', path, recursive),
    getFiles: (path) => ipcRenderer.invoke('fileSystem:getFiles', path),
    getDirectories: (path) => ipcRenderer.invoke('fileSystem:getDirectories', path),
    getSize: (path) => ipcRenderer.invoke('fileSystem:getSize', path),
    getLastModified: (path) => ipcRenderer.invoke('fileSystem:getLastModified', path),
    getPermissions: (path) => ipcRenderer.invoke('fileSystem:getPermissions', path),
    setPermissions: (path, mode) => ipcRenderer.invoke('fileSystem:setPermissions', path, mode),
    watch: (path, callback) => {
      const id = Date.now().toString();
      ipcRenderer.on(`fileSystem:watch:${id}`, (event, eventType, filename) => {
        callback(eventType, filename);
      });
      ipcRenderer.send('fileSystem:watch', path, id);
      return id;
    },
    unwatch: (id) => {
      ipcRenderer.removeAllListeners(`fileSystem:watch:${id}`);
      ipcRenderer.send('fileSystem:unwatch', id);
    },
  },

  // 系统相关
  system: {
    getPlatform: () => ipcRenderer.invoke('system:getPlatform'),
    getArch: () => ipcRenderer.invoke('system:getArch'),
    getHostname: () => ipcRenderer.invoke('system:getHostname'),
    getUsername: () => ipcRenderer.invoke('system:getUsername'),
    getHomePath: () => ipcRenderer.invoke('system:getHomePath'),
    getTempPath: () => ipcRenderer.invoke('system:getTempPath'),
    getDesktopPath: () => ipcRenderer.invoke('system:getDesktopPath'),
    getDocumentsPath: () => ipcRenderer.invoke('system:getDocumentsPath'),
    getDownloadsPath: () => ipcRenderer.invoke('system:getDownloadsPath'),
    getMusicPath: () => ipcRenderer.invoke('system:getMusicPath'),
    getPicturesPath: () => ipcRenderer.invoke('system:getPicturesPath'),
    getVideosPath: () => ipcRenderer.invoke('system:getVideosPath'),
    getCPUs: () => ipcRenderer.invoke('system:getCPUs'),
    getMemory: () => ipcRenderer.invoke('system:getMemory'),
    getNetworkInterfaces: () => ipcRenderer.invoke('system:getNetworkInterfaces'),
    getBattery: () => ipcRenderer.invoke('system:getBattery'),
    getDisplays: () => ipcRenderer.invoke('system:getDisplays'),
    openExternal: (url) => shell.openExternal(url),
    openPath: (path) => shell.openPath(path),
    showItemInFolder: (path) => shell.showItemInFolder(path),
    moveToTrash: (path) => shell.trashItem(path),
    beep: () => shell.beep(),
  },

  // 屏幕相关
  screen: {
    getCursorScreenPoint: () => ipcRenderer.invoke('screen:getCursorScreenPoint'),
    getPrimaryDisplay: () => ipcRenderer.invoke('screen:getPrimaryDisplay'),
    getAllDisplays: () => ipcRenderer.invoke('screen:getAllDisplays'),
    getDisplayNearestPoint: (point) => ipcRenderer.invoke('screen:getDisplayNearestPoint', point),
    getDisplayMatching: (rect) => ipcRenderer.invoke('screen:getDisplayMatching', rect),
    capture: (options) => ipcRenderer.invoke('screen:capture', options),
    startRecording: (options) => ipcRenderer.invoke('screen:startRecording', options),
    stopRecording: () => ipcRenderer.invoke('screen:stopRecording'),
    isRecording: () => ipcRenderer.invoke('screen:isRecording'),
    getRecordingPath: () => ipcRenderer.invoke('screen:getRecordingPath'),
  },

  // 更新相关
  updater: {
    check: () => ipcRenderer.invoke('updater:check'),
    download: () => ipcRenderer.invoke('updater:download'),
    install: () => ipcRenderer.invoke('updater:install'),
    getStatus: () => ipcRenderer.invoke('updater:getStatus'),
    getProgress: () => ipcRenderer.invoke('updater:getProgress'),
    onProgress: (callback) => {
      ipcRenderer.on('updater:progress', (event, progress) => {
        callback(progress);
      });
    },
    onUpdateAvailable: (callback) => {
      ipcRenderer.on('updater:updateAvailable', (event, info) => {
        callback(info);
      });
    },
    onUpdateNotAvailable: (callback) => {
      ipcRenderer.on('updater:updateNotAvailable', (event) => {
        callback();
      });
    },
    onUpdateDownloaded: (callback) => {
      ipcRenderer.on('updater:updateDownloaded', (event, info) => {
        callback(info);
      });
    },
    onError: (callback) => {
      ipcRenderer.on('updater:error', (event, error) => {
        callback(error);
      });
    },
  },

  // HTTP 相关
  http: {
    get: (url, options) => ipcRenderer.invoke('http:get', url, options),
    post: (url, data, options) => ipcRenderer.invoke('http:post', url, data, options),
    put: (url, data, options) => ipcRenderer.invoke('http:put', url, data, options),
    patch: (url, data, options) => ipcRenderer.invoke('http:patch', url, data, options),
    delete: (url, options) => ipcRenderer.invoke('http:delete', url, options),
    head: (url, options) => ipcRenderer.invoke('http:head', url, options),
    options: (url, options) => ipcRenderer.invoke('http:options', url, options),
    download: (url, destination, options) => {
      const id = Date.now().toString();
      ipcRenderer.on(`http:download:progress:${id}`, (event, progress) => {
        if (options && typeof options.onProgress === 'function') {
          options.onProgress(progress);
        }
      });
      return ipcRenderer.invoke('http:download', url, destination, id, options);
    },
  },

  // 数据库相关
  database: {
    query: (query, params) => ipcRenderer.invoke('database:query', query, params),
    fetchOne: (query, params) => ipcRenderer.invoke('database:fetchOne', query, params),
    fetchAll: (query, params) => ipcRenderer.invoke('database:fetchAll', query, params),
    fetchValue: (query, params) => ipcRenderer.invoke('database:fetchValue', query, params),
    insert: (table, data) => ipcRenderer.invoke('database:insert', table, data),
    update: (table, data, where, params) => ipcRenderer.invoke('database:update', table, data, where, params),
    delete: (table, where, params) => ipcRenderer.invoke('database:delete', table, where, params),
    createTable: (table, schema) => ipcRenderer.invoke('database:createTable', table, schema),
    dropTable: (table) => ipcRenderer.invoke('database:dropTable', table),
    tableExists: (table) => ipcRenderer.invoke('database:tableExists', table),
    getTableSchema: (table) => ipcRenderer.invoke('database:getTableSchema', table),
    beginTransaction: () => ipcRenderer.invoke('database:beginTransaction'),
    commit: () => ipcRenderer.invoke('database:commit'),
    rollBack: () => ipcRenderer.invoke('database:rollBack'),
    backup: (path) => ipcRenderer.invoke('database:backup', path),
    restore: (path) => ipcRenderer.invoke('database:restore', path),
    getSize: () => ipcRenderer.invoke('database:getSize'),
    optimize: () => ipcRenderer.invoke('database:optimize'),
  },

  // 设置相关
  settings: {
    get: (key, defaultValue) => ipcRenderer.invoke('settings:get', key, defaultValue),
    set: (key, value) => ipcRenderer.invoke('settings:set', key, value),
    has: (key) => ipcRenderer.invoke('settings:has', key),
    delete: (key) => ipcRenderer.invoke('settings:delete', key),
    all: () => ipcRenderer.invoke('settings:all'),
    clear: () => ipcRenderer.invoke('settings:clear'),
    export: (path) => ipcRenderer.invoke('settings:export', path),
    import: (path) => ipcRenderer.invoke('settings:import', path),
    watch: (key, callback) => {
      const id = Date.now().toString();
      ipcRenderer.on(`settings:watch:${id}`, (event, value) => {
        callback(value);
      });
      ipcRenderer.send('settings:watch', key, id);
      return id;
    },
    unwatch: (id) => {
      ipcRenderer.removeAllListeners(`settings:watch:${id}`);
      ipcRenderer.send('settings:unwatch', id);
    },
  },

  // 进程相关
  process: {
    run: (command, options) => ipcRenderer.invoke('process:run', command, options),
    runPhp: (script, args, options) => ipcRenderer.invoke('process:runPhp', script, args, options),
    runThink: (command, args, options) => ipcRenderer.invoke('process:runThink', command, args, options),
    get: (processId) => ipcRenderer.invoke('process:get', processId),
    all: () => ipcRenderer.invoke('process:all'),
    getOutput: (processId) => ipcRenderer.invoke('process:getOutput', processId),
    getError: (processId) => ipcRenderer.invoke('process:getError', processId),
    getExitCode: (processId) => ipcRenderer.invoke('process:getExitCode', processId),
    isRunning: (processId) => ipcRenderer.invoke('process:isRunning', processId),
    write: (processId, input) => ipcRenderer.invoke('process:write', processId, input),
    kill: (processId) => ipcRenderer.invoke('process:kill', processId),
    wait: (processId, timeout) => ipcRenderer.invoke('process:wait', processId, timeout),
    cleanup: () => ipcRenderer.invoke('process:cleanup'),
  },

  // 打印相关
  printer: {
    getPrinters: () => ipcRenderer.invoke('printer:getPrinters'),
    getDefaultPrinter: () => ipcRenderer.invoke('printer:getDefaultPrinter'),
    printHtml: (html, options) => ipcRenderer.invoke('printer:printHtml', html, options),
    printFile: (filePath, options) => ipcRenderer.invoke('printer:printFile', filePath, options),
    printToPdf: (html, outputPath, options) => ipcRenderer.invoke('printer:printToPdf', html, outputPath, options),
    showPrintPreview: (html, options) => ipcRenderer.invoke('printer:showPrintPreview', html, options),
  },

  // 语音相关
  speech: {
    startRecognition: (options) => ipcRenderer.invoke('speech:startRecognition', options),
    stopRecognition: () => ipcRenderer.invoke('speech:stopRecognition'),
    isRecognizing: () => ipcRenderer.invoke('speech:isRecognizing'),
    getRecognitionResult: () => ipcRenderer.invoke('speech:getRecognitionResult'),
    speak: (text, options) => ipcRenderer.invoke('speech:speak', text, options),
    pause: () => ipcRenderer.invoke('speech:pause'),
    resume: () => ipcRenderer.invoke('speech:resume'),
    cancel: () => ipcRenderer.invoke('speech:cancel'),
    isSpeaking: () => ipcRenderer.invoke('speech:isSpeaking'),
    getVoices: () => ipcRenderer.invoke('speech:getVoices'),
    textToAudio: (text, outputPath, options) => ipcRenderer.invoke('speech:textToAudio', text, outputPath, options),
    audioToText: (audioPath, options) => ipcRenderer.invoke('speech:audioToText', audioPath, options),
    onRecognitionResult: (callback) => {
      ipcRenderer.on('speech:recognitionResult', (event, result) => {
        callback(result);
      });
    },
  },

  // 设备相关
  device: {
    getBluetoothDevices: () => ipcRenderer.invoke('device:getBluetoothDevices'),
    getUsbDevices: () => ipcRenderer.invoke('device:getUsbDevices'),
    scanBluetoothDevices: (options) => ipcRenderer.invoke('device:scanBluetoothDevices', options),
    connectBluetoothDevice: (deviceId) => ipcRenderer.invoke('device:connectBluetoothDevice', deviceId),
    disconnectBluetoothDevice: (deviceId) => ipcRenderer.invoke('device:disconnectBluetoothDevice', deviceId),
    pairBluetoothDevice: (deviceId) => ipcRenderer.invoke('device:pairBluetoothDevice', deviceId),
    unpairBluetoothDevice: (deviceId) => ipcRenderer.invoke('device:unpairBluetoothDevice', deviceId),
    sendDataToBluetoothDevice: (deviceId, data) => ipcRenderer.invoke('device:sendDataToBluetoothDevice', deviceId, data),
    receiveDataFromBluetoothDevice: (deviceId) => ipcRenderer.invoke('device:receiveDataFromBluetoothDevice', deviceId),
    openUsbDevice: (deviceId) => ipcRenderer.invoke('device:openUsbDevice', deviceId),
    closeUsbDevice: (deviceId) => ipcRenderer.invoke('device:closeUsbDevice', deviceId),
    sendDataToUsbDevice: (deviceId, data) => ipcRenderer.invoke('device:sendDataToUsbDevice', deviceId, data),
    receiveDataFromUsbDevice: (deviceId) => ipcRenderer.invoke('device:receiveDataFromUsbDevice', deviceId),
    getDeviceInfo: (deviceId, type) => ipcRenderer.invoke('device:getDeviceInfo', deviceId, type),
    onDeviceConnected: (callback) => {
      ipcRenderer.on('device:connected', (event, device) => {
        callback(device);
      });
    },
    onDeviceDisconnected: (callback) => {
      ipcRenderer.on('device:disconnected', (event, device) => {
        callback(device);
      });
    },
    onDeviceData: (callback) => {
      ipcRenderer.on('device:data', (event, deviceId, data) => {
        callback(deviceId, data);
      });
    },
  },

  // 地理位置相关
  geolocation: {
    getCurrentPosition: (options) => ipcRenderer.invoke('geolocation:getCurrentPosition', options),
    watchPosition: (options) => {
      const id = Date.now().toString();
      ipcRenderer.on(`geolocation:position:${id}`, (event, position) => {
        if (options && typeof options.onSuccess === 'function') {
          options.onSuccess(position);
        }
      });
      ipcRenderer.on(`geolocation:error:${id}`, (event, error) => {
        if (options && typeof options.onError === 'function') {
          options.onError(error);
        }
      });
      ipcRenderer.send('geolocation:watchPosition', id, options);
      return id;
    },
    clearWatch: (id) => {
      ipcRenderer.removeAllListeners(`geolocation:position:${id}`);
      ipcRenderer.removeAllListeners(`geolocation:error:${id}`);
      ipcRenderer.send('geolocation:clearWatch', id);
    },
    isWatching: () => ipcRenderer.invoke('geolocation:isWatching'),
    getWatchId: () => ipcRenderer.invoke('geolocation:getWatchId'),
    calculateDistance: (lat1, lon1, lat2, lon2, unit) => ipcRenderer.invoke('geolocation:calculateDistance', lat1, lon1, lat2, lon2, unit),
    getAddressFromCoordinates: (lat, lon) => ipcRenderer.invoke('geolocation:getAddressFromCoordinates', lat, lon),
    getCoordinatesFromAddress: (address) => ipcRenderer.invoke('geolocation:getCoordinatesFromAddress', address),
    isAvailable: () => ipcRenderer.invoke('geolocation:isAvailable'),
    checkPermission: () => ipcRenderer.invoke('geolocation:checkPermission'),
    requestPermission: () => ipcRenderer.invoke('geolocation:requestPermission'),
  },

  // 推送通知相关
  pushNotification: {
    setProvider: (provider) => ipcRenderer.invoke('pushNotification:setProvider', provider),
    getProvider: () => ipcRenderer.invoke('pushNotification:getProvider'),
    setConfig: (config) => ipcRenderer.invoke('pushNotification:setConfig', config),
    getConfig: () => ipcRenderer.invoke('pushNotification:getConfig'),
    registerDevice: (token, data) => ipcRenderer.invoke('pushNotification:registerDevice', token, data),
    unregisterDevice: (token) => ipcRenderer.invoke('pushNotification:unregisterDevice', token),
    send: (tokens, title, body, data, options) => ipcRenderer.invoke('pushNotification:send', tokens, title, body, data, options),
    getDeviceInfo: (token) => ipcRenderer.invoke('pushNotification:getDeviceInfo', token),
    getHistory: (limit, offset) => ipcRenderer.invoke('pushNotification:getHistory', limit, offset),
    getStatistics: (startDate, endDate) => ipcRenderer.invoke('pushNotification:getStatistics', startDate, endDate),
    onNotificationReceived: (callback) => {
      ipcRenderer.on('pushNotification:received', (event, notification) => {
        callback(notification);
      });
    },
    onNotificationClicked: (callback) => {
      ipcRenderer.on('pushNotification:clicked', (event, notification) => {
        callback(notification);
      });
    },
  },

  // 工具相关
  utils: {
    // 日志工具
    logger: {
      debug: (message, context) => ipcRenderer.invoke('logger:debug', message, context),
      info: (message, context) => ipcRenderer.invoke('logger:info', message, context),
      warn: (message, context) => ipcRenderer.invoke('logger:warn', message, context),
      error: (message, context) => ipcRenderer.invoke('logger:error', message, context),
      critical: (message, context) => ipcRenderer.invoke('logger:critical', message, context),
      getLogFile: () => ipcRenderer.invoke('logger:getLogFile'),
      getLogs: (level, limit, offset) => ipcRenderer.invoke('logger:getLogs', level, limit, offset),
      clear: () => ipcRenderer.invoke('logger:clear'),
    },

    // 缓存工具
    cache: {
      get: (key, defaultValue) => ipcRenderer.invoke('cache:get', key, defaultValue),
      set: (key, value, ttl) => ipcRenderer.invoke('cache:set', key, value, ttl),
      has: (key) => ipcRenderer.invoke('cache:has', key),
      delete: (key) => ipcRenderer.invoke('cache:delete', key),
      clear: () => ipcRenderer.invoke('cache:clear'),
      remember: (key, callback, ttl) => ipcRenderer.invoke('cache:remember', key, callback, ttl),
      getInfo: (key) => ipcRenderer.invoke('cache:getInfo', key),
      getAllInfo: () => ipcRenderer.invoke('cache:getAllInfo'),
      getSize: () => ipcRenderer.invoke('cache:getSize'),
      gc: () => ipcRenderer.invoke('cache:gc'),
    },

    // 事件工具
    event: {
      on: (event, callback) => {
        const id = Date.now().toString();
        ipcRenderer.on(`event:${event}:${id}`, (_, ...args) => {
          callback(...args);
        });
        ipcRenderer.send('event:on', event, id);
        return id;
      },
      once: (event, callback) => {
        const id = Date.now().toString();
        ipcRenderer.once(`event:${event}:${id}`, (_, ...args) => {
          callback(...args);
          ipcRenderer.send('event:off', event, id);
        });
        ipcRenderer.send('event:once', event, id);
        return id;
      },
      off: (event, id) => {
        ipcRenderer.removeAllListeners(`event:${event}:${id}`);
        ipcRenderer.send('event:off', event, id);
      },
      emit: (event, ...args) => ipcRenderer.send('event:emit', event, ...args),
      listeners: (event) => ipcRenderer.invoke('event:listeners', event),
      listenerCount: (event) => ipcRenderer.invoke('event:listenerCount', event),
      eventNames: () => ipcRenderer.invoke('event:eventNames'),
    },

    // 配置工具
    config: {
      get: (key, defaultValue) => ipcRenderer.invoke('config:get', key, defaultValue),
      set: (key, value) => ipcRenderer.invoke('config:set', key, value),
      has: (key) => ipcRenderer.invoke('config:has', key),
      delete: (key) => ipcRenderer.invoke('config:delete', key),
      all: () => ipcRenderer.invoke('config:all'),
      clear: () => ipcRenderer.invoke('config:clear'),
      export: (path) => ipcRenderer.invoke('config:export', path),
      import: (path) => ipcRenderer.invoke('config:import', path),
      merge: (config) => ipcRenderer.invoke('config:merge', config),
      replace: (config) => ipcRenderer.invoke('config:replace', config),
    },
  },

  // 版本信息
  versions: {
    electron: process.versions.electron,
    chrome: process.versions.chrome,
    node: process.versions.node,
  },
});

// 处理 DOM 内容加载完成事件
window.addEventListener('DOMContentLoaded', () => {
  // 可以在这里执行一些初始化操作
  console.log('NativePHP for ThinkPHP 已加载');
});
