const { performance, PerformanceObserver } = require('perf_hooks');
const EventEmitter = require('events');
const log = require('electron-log');

class PerformanceMonitor extends EventEmitter {
    constructor() {
        super();
        this.metrics = new Map();
        this.initObserver();
    }

    initObserver() {
        // 观察性能指标
        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                this.metrics.set(entry.name, {
                    duration: entry.duration,
                    startTime: entry.startTime,
                    entryType: entry.entryType
                });

                this.emit('metric', {
                    name: entry.name,
                    duration: entry.duration,
                    startTime: entry.startTime,
                    entryType: entry.entryType
                });

                // 记录到日志
                log.debug('Performance metric:', {
                    name: entry.name,
                    duration: entry.duration,
                    startTime: entry.startTime,
                    entryType: entry.entryType
                });
            }
        });

        observer.observe({ entryTypes: ['measure', 'mark'] });
    }

    start(name) {
        performance.mark(`${name}-start`);
    }

    end(name) {
        performance.mark(`${name}-end`);
        performance.measure(name, `${name}-start`, `${name}-end`);
    }

    getMetric(name) {
        return this.metrics.get(name);
    }

    getAllMetrics() {
        return Array.from(this.metrics.entries()).reduce((acc, [key, value]) => {
            acc[key] = value;
            return acc;
        }, {});
    }

    clearMetrics() {
        this.metrics.clear();
        performance.clearMarks();
        performance.clearMeasures();
    }

    measureAsync(name, asyncFn) {
        this.start(name);
        return Promise.resolve(asyncFn())
            .finally(() => this.end(name));
    }

    // 添加常用指标
    measureResourceTiming() {
        if (typeof window !== 'undefined') {
            const resourceTiming = performance.getEntriesByType('resource');
            for (const entry of resourceTiming) {
                this.metrics.set(`resource-${entry.name}`, {
                    duration: entry.duration,
                    startTime: entry.startTime,
                    entryType: entry.entryType,
                    size: entry.transferSize,
                    protocol: entry.nextHopProtocol
                });
            }
        }
    }

    measureMemory() {
        if (typeof process !== 'undefined') {
            const memoryUsage = process.memoryUsage();
            this.metrics.set('memory', {
                heapUsed: memoryUsage.heapUsed,
                heapTotal: memoryUsage.heapTotal,
                external: memoryUsage.external,
                arrayBuffers: memoryUsage.arrayBuffers
            });
        }
    }

    // CPU 使用率监控
    startCPUMonitoring(interval = 1000) {
        if (typeof process === 'undefined') return;

        this.cpuInterval = setInterval(() => {
            const startUsage = process.cpuUsage();
            const startTime = Date.now();

            setTimeout(() => {
                const endUsage = process.cpuUsage(startUsage);
                const endTime = Date.now();

                const userPercent = (endUsage.user / 1000) / (endTime - startTime);
                const systemPercent = (endUsage.system / 1000) / (endTime - startTime);

                this.metrics.set('cpu', {
                    user: userPercent,
                    system: systemPercent,
                    total: userPercent + systemPercent
                });

                this.emit('cpu', {
                    user: userPercent,
                    system: systemPercent,
                    total: userPercent + systemPercent
                });
            }, interval);
        }, interval);
    }

    stopCPUMonitoring() {
        if (this.cpuInterval) {
            clearInterval(this.cpuInterval);
            this.cpuInterval = null;
        }
    }
}

module.exports = new PerformanceMonitor();