<?php

spl_autoload_register(function ($className) {

    $prefix = 'controllerframework\\';

    if (strncmp($prefix, $className, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($className, strlen($prefix));

    $file = __DIR__ . '/' .
            str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use controllerframework\registry\Registry;
use controllerframework\controllers\Conf;
use controllerframework\mail\MailConfig;

// Test 1: Mail enabled
Registry::instance()->setAppConfig(new Conf(['mailenabled' => 'Y']));
echo "Test Y: " . (MailConfig::isEnabled() ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 2: Mail disabled
Registry::reset();
Registry::instance()->setAppConfig(new Conf(['mailenabled' => 'N']));
echo "Test N: " . (!MailConfig::isEnabled() ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 3: Setting missing
Registry::reset();
Registry::instance()->setAppConfig(new Conf([]));
echo "Test missing: " . (MailConfig::isEnabled() ? 'PASS' : 'FAIL') . PHP_EOL;