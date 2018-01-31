<?php

// get true path for this file
define('ROOT', str_replace(['\\', '\\\\', '//'], '/', __DIR__ . '/'));

// composer installation check
if (!is_dir(ROOT . 'vendor')) {
    exit('ERROR: Package dependencies could not be located!');
}

// require composer autoloader
require_once(ROOT . 'vendor/autoload.php');

// application
$app = Base::instance();

// load the configuration settings
$app->mset(include(ROOT . 'config.php'));

// set api language directories
foreach (Helper::recursive_glob(ROOT . 'api/*/lang/') as $file) {
    $app->LOCALES .= '|' . str_replace(ROOT, '', $file);
}

// application route configuration
if ($app->RESTFUL && !empty($app->RESTFUL)) {
    foreach ($app->RESTFUL as $route => $callback) {
        $app->route($route, $callback);
    }
    $app->clear('RESTFUL'); // remove the key, most likely no longer needed
}

// start listening for http requests
$app->run();