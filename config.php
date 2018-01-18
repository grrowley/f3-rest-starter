<?php

$config = [

    // package information
    'PACKAGE'  => 'Rest API',
    'VERSION'  => '1.0.0',
    'AUTHORS'  => 'Grant Rowley <me@grrowley.com>',

    // debug level (0-3)
    'DEBUG'    => 3,

    // unique 16 character encrypting key (key this private)
    'APPKEY'   => '6esW@tar&Fethe48',

    // application file paths
    'AUTOLOAD' => 'app/|api/',

    // language settings
    'LANGUAGE' => 'en',
    'PREFIX'   => 'LANG.',
    'LOCALES'  => 'app/lang/',

    // cross origin request settings
    'CORS' => [
        'origin'  => '*',
        'headers' => 'Authorization, X-Requested-With, Origin, X-Origin, Content-Type, Content-Length'
    ],

    // database connection
    'DATA'   => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'database',
        'user' => 'root',
        'pswd' => 'root',
        'pfix' => ''
    ],
    
    // rest route configuration
    'RESTFUL'  => [
        'GET    /api/@version/@controller'     => '@version\routes\@controller->get',
        'GET    /api/@version/@controller/@id' => '@version\routes\@controller->get',
        'PUT    /api/@version/@controller'     => '@version\routes\@controller->put',
        'PUT    /api/@version/@controller/@id' => '@version\routes\@controller->put',
        'POST   /api/@version/@controller'     => '@version\routes\@controller->post',
        'POST   /api/@version/@controller/@id' => '@version\routes\@controller->post',
        'DELETE /api/@version/@controller'     => '@version\routes\@controller->delete',
        'DELETE /api/@version/@controller/@id' => '@version\routes\@controller->delete'
    ],

    // restful error response
    'ONERROR'  => 'Errors->display'
];