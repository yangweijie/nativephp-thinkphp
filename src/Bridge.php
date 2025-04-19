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
        // 监听PHP端事件并转发到Tauri
        $this->events->listen('*', function ($payload, $event) {
            $this->sendToTauri('php.' . $event, $payload);
        });
        
        // 监听来自Tauri的事件
        $this->listenFromTauri();
    }
    
    protected function sendToTauri(string $event, array $payload = [])
    {
        $this->native->ipc()->invoke('tauri.emit', [
            'event' => $event,
            'payload' => $payload
        ]);
    }
    
    protected function listenFromTauri()
    {
        $this->native->ipc()->handle('tauri.event', function ($data) {
            if (isset($data['event'], $data['payload'])) {
                $this->events->dispatch('tauri.' . $data['event'], $data['payload']);
            }
        });
    }
    
    public function emit(string $event, array $payload = [])
    {
        $this->sendToTauri($event, $payload);
        return $this;
    }
    
    public function on(string $event, callable $callback)
    {
        $this->events->listen('tauri.' . $event, $callback);
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
    }
}