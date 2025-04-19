<?php

require_once __DIR__ . '/vendor/autoload.php';

use NativePHP\Think\Contract\EventDispatcherContract;

// 检查接口和实现类的兼容性
echo "检查 EventDispatcherContract 接口和 EventDispatcher 类的兼容性...\n";

// 获取接口的 remove 方法
$interface = new ReflectionClass(EventDispatcherContract::class);
$interfaceMethod = $interface->getMethod('remove');
echo "接口 remove 方法签名: " . getMethodSignature($interfaceMethod) . "\n";

// 获取实现类的 remove 方法
$class = new ReflectionClass(\NativePHP\Think\EventDispatcher::class);
$classMethod = $class->getMethod('remove');
echo "实现类 remove 方法签名: " . getMethodSignature($classMethod) . "\n";

// 检查 removeListener 方法
if ($interface->hasMethod('removeListener')) {
    $interfaceMethod = $interface->getMethod('removeListener');
    echo "接口 removeListener 方法签名: " . getMethodSignature($interfaceMethod) . "\n";
    
    $classMethod = $class->getMethod('removeListener');
    echo "实现类 removeListener 方法签名: " . getMethodSignature($classMethod) . "\n";
} else {
    echo "接口中没有 removeListener 方法\n";
}

echo "兼容性检查完成。\n";

// 辅助函数：获取方法签名
function getMethodSignature(ReflectionMethod $method) {
    $params = [];
    foreach ($method->getParameters() as $param) {
        $paramStr = '';
        if ($param->hasType()) {
            $type = $param->getType();
            $paramStr .= $type->getName() . ' ';
        }
        $paramStr .= '$' . $param->getName();
        if ($param->isDefaultValueAvailable()) {
            $defaultValue = $param->getDefaultValue();
            if ($defaultValue === null) {
                $paramStr .= ' = null';
            } else if (is_bool($defaultValue)) {
                $paramStr .= ' = ' . ($defaultValue ? 'true' : 'false');
            } else if (is_string($defaultValue)) {
                $paramStr .= " = '" . $defaultValue . "'";
            } else {
                $paramStr .= ' = ' . $defaultValue;
            }
        }
        $params[] = $paramStr;
    }
    
    $returnType = $method->hasReturnType() ? $method->getReturnType()->getName() : 'mixed';
    
    return sprintf(
        '%s function %s(%s): %s',
        $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private'),
        $method->getName(),
        implode(', ', $params),
        $returnType
    );
}
