<?php

spl_autoload_register(function ($className) {

    $prefix = 'controllerframework\\';

    if (strncmp($prefix, $className, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($className, strlen($prefix));

    $file = dirname(__DIR__) . '/src/' .
            str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use controllerframework\registry\Registry;
use controllerframework\controllers\Conf;
use controllerframework\mail\Mailer;
use controllerframework\mail\MailerQueue;

// Disable email sending
Registry::instance()->setAppConfig(new Conf(['mailenabled' => 'N']));

echo "Testing Mailer...\n";

try {
    Mailer::sendMail('Test', 'Test message', '');
    echo "Mailer: PASS\n";
} catch (\Throwable $e) {
    echo "Mailer: FAIL - " . $e->getMessage() . "\n";
}

echo "Testing MailerQueue...\n";

try {
    MailerQueue::sendMail('Test', 'Test message', '');
    echo "MailerQueue: PASS\n";
} catch (\Throwable $e) {
    echo "MailerQueue: FAIL - " . $e->getMessage() . "\n";
}
