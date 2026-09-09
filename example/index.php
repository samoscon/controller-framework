<?php

include __DIR__ . '/vendor/autoload.php';

// Upload classes within your project automatically
spl_autoload_register(function ($class_name) {
    if (preg_match('/\\\\/', $class_name)) {
        $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR .
            'MVCFramework' . DIRECTORY_SEPARATOR .
            $class_name . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

try {
    controllerframework\controllers\Controller::run();
} catch (\Throwable $e) {
    http_response_code(500);

    echo '<h1>Application initialization failed</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}
