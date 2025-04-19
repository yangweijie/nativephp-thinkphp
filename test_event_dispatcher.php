<?php

require_once __DIR__ . '/vendor/autoload.php';

// Just check if the class can be loaded without errors
echo "Testing EventDispatcher class...\n";
echo "Class exists: " . (class_exists('NativePHP\Think\EventDispatcher') ? 'Yes' : 'No') . "\n";
echo "Interface exists: " . (interface_exists('NativePHP\Think\Contract\EventDispatcherContract') ? 'Yes' : 'No') . "\n";
echo "Done.\n";
