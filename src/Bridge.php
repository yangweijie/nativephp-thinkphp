<?php

namespace NativePHP\Think;

class Bridge
{
    protected $native;
    protected $events;

    public function __construct(Native $native)
    {
        $this->native = $native;
        $this->events = $native->events();
        $this->setupEventListeners();
    }

    protected function setupEventListeners()
    {
        // 监听PHP端事件并转发到Electron
        $this->events->listen('*', function ($payload, $event) {
            $this->sendToElectron('php.' . $event, $payload);
        });

        // 监听来自Electron的事件
        $this->listenFromElectron();

        // 监听窗口过渡动画事件
        $this->events->listen('window.transition.start', function ($payload) {
            if (isset($payload['window']['label'])) {
                $window = $this->native->window($payload['window']['label']);
                $window->setState('transitioning', true);
            }
        });

        $this->events->listen('window.transition.complete', function ($payload) {
            if (isset($payload['window']['label'])) {
                $window = $this->native->window($payload['window']['label']);
                $window->setState('transitioning', false);
                
                // 应用最终布局
                if (isset($payload['to'])) {
                    $window->configure($payload['to']);
                }
            }
        });

        $this->events->listen('window.transition.cancel', function ($payload) {
            if (isset($payload['window']['label'])) {
                $window = $this->native->window($payload['window']['label']);
                $window->setState('transitioning', false);
                
                // 恢复原始布局
                if (isset($payload['from'])) {
                    $window->configure($payload['from']);
                }
            }
        });
    }

    protected function sendToElectron(string $event, array $payload = [])
    {
        $this->native->ipc()->send($event, $payload);
    }

    protected function listenFromElectron()
    {
        $this->native->ipc()->handle('electron.event', function ($data) {
            if (isset($data['event'], $data['payload'])) {
                $this->events->dispatch('electron.' . $data['event'], $data['payload']);
            }
        });

        // 窗口组操作
        $this->native->ipc()->handle('window.group.create', function (array $data) {
            return $this->native->windowManager()->createGroup($data['name'], $data['windows'] ?? []);
        });

        $this->native->ipc()->handle('window.group.arrange', function (array $data) {
            $group = $this->native->windowManager()->getGroup($data['name']);
            if (!$group) return false;

            switch ($data['layout']) {
                case 'horizontal':
                    $group->arrangeHorizontal();
                    break;
                case 'vertical':
                    $group->arrangeVertical();
                    break;
                case 'grid':
                    $group->arrangeGrid($data['columns'] ?? 2);
                    break;
            }
            return true;
        });

        $this->native->ipc()->handle('window.group.save-state', function (array $data) {
            $group = $this->native->windowManager()->getGroup($data['name']);
            return $group ? $group->saveState() : false;
        });

        $this->native->ipc()->handle('window.group.restore-state', function (array $data) {
            $group = $this->native->windowManager()->getGroup($data['name']);
            return $group ? $group->loadState() : false;
        });

        $this->native->ipc()->handle('window.group.add', function (array $data) {
            $group = $this->native->windowManager()->getGroup($data['name']);
            return $group ? $group->add($data['window']) : false;
        });

        $this->native->ipc()->handle('window.group.remove', function (array $data) {
            $group = $this->native->windowManager()->getGroup($data['name']);
            return $group ? $group->remove($data['window']) : false;
        });
    }

    public function emit(string $event, array $payload = [])
    {
        $this->sendToElectron($event, $payload);
        return $this;
    }

    public function on(string $event, callable $callback)
    {
        $this->events->listen('electron.' . $event, $callback);
        return $this;
    }

    /**
     * 注册所有桥接方法
     */
    public function register(): void
    {
        $this->native->ipc()->handle('window.save-state', function (array $data) {
            return $this->native->windowState()->save($data['label'] ?? 'main');
        });

        $this->native->ipc()->handle('window.restore-state', function (array $data) {
            $label = $data['label'] ?? 'main';
            $state = $data['state'] ?? null;

            if (!$state) {
                return $this->native->windowState()->autoRestore($label);
            }

            return $this->native->windowState()->restore($label, $state);
        });

        $this->native->ipc()->handle('window.clear-state', function (array $data) {
            return $this->native->windowState()->clear($data['label'] ?? 'main');
        });

        $this->native->ipc()->handle('window.get-states', function () {
            return $this->native->windowState()->all();
        });

        $this->native->ipc()->handle('window.create', function (array $data) {
            $window = $this->native->window($data['label'] ?? null);

            if (isset($data['preset'])) {
                $window = $this->native->windowPresets()->apply($data['preset'], $data['label'] ?? null);
            }

            if (isset($data['options']) && is_array($data['options'])) {
                $window->configure($data['options']);
            }

            return $window;
        });

        $this->native->ipc()->handle('window.apply-layout', function (array $data) {
            if (!isset($data['layout']) || !isset($data['windows'])) {
                return false;
            }

            return $this->native->windowLayoutPresets()
                ->apply($data['layout'], $data['windows']);
        });

        // 添加窗口组状态管理相关的处理器
        $this->native->ipc()->handle('window.group.auto-save-all', function () {
            return $this->native->windowGroupStateManager()->autoSaveAll();
        });

        $this->native->ipc()->handle('window.group.auto-restore-all', function () {
            return $this->native->windowGroupStateManager()->autoRestoreAll();
        });

        $this->native->ipc()->handle('window.group.clear-all-states', function () {
            return $this->native->windowGroupStateManager()->clearAll();
        });

        $this->native->ipc()->handle('window.group.set-cache-config', function (array $data) {
            $manager = $this->native->windowGroupStateManager();
            
            if (isset($data['key'])) {
                $manager->setCacheKey($data['key']);
            }
            
            if (isset($data['expire'])) {
                $manager->setExpireTime((int)$data['expire']);
            }
            
            return true;
        });

        // 添加过渡动画相关的处理器
        $this->native->ipc()->handle('window.transition.start', function (array $data) {
            return $this->native->events()->dispatch('window.transition.start', $data);
        });

        $this->native->ipc()->handle('window.transition.complete', function (array $data) {
            if (isset($data['window'], $data['to'])) {
                $window = $this->native->window($data['window']);
                $window->configure($data['to']);
            }
            return $this->native->events()->dispatch('window.transition.complete', $data);
        });

        $this->native->ipc()->handle('window.transition.cancel', function (array $data) {
            if (isset($data['window'], $data['from'])) {
                $window = $this->native->window($data['window']);
                $window->configure($data['from']);
            }
            return $this->native->events()->dispatch('window.transition.cancel', $data);
        });
    }
}