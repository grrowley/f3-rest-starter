<?php

namespace Base;

use \DB\Cortex;

abstract class Model extends Cortex
{
    function __construct()
    {
        $app = Base::instance();
        if ($app->exists('DATA', $db)) {
            switch ($db['type']) {
                case 'value':
                    # code...
                    break;
                
                case 'mysql':
                default:
                    
                    break;
            }
        }
        parent::__construct();
    }
}