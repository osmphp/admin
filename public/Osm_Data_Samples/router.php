<?php

declare(strict_types=1);

if (php_sapi_name() !== 'cli-server') {
    // don't run this file under Apache or Nginx
    die('Available under built-in Web server only.');
}

$__path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (!is_file(__DIR__ . $__path)) {
    // if there is no such file, run the application and let it render the response
    require __DIR__ . '/index.php';
    return;
}

if (pathinfo($__path, PATHINFO_EXTENSION) == 'php') {
    // if it's PHP file, execute it

    /** @noinspection PhpIncludeInspection */
    require __DIR__ . $__path;
    return;
}

// otherwise, just serve the existing file
return false;

