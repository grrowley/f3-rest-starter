<?php

class Errors
{
    function display($app)
    {
        $req = \Request::instance();
        $res = \Response::instance();

        $res->set('status', $app->ERROR['code']);
        $res->set('message', $app->ERROR['status']);
        
        if ($app->ERROR['trace']) {
            $res->set('_trace', $app->ERROR['trace']);
        }
        
        if ($app->ERROR['level']) {
            $res->set('_level', $app->ERROR['level']);
        }
        
        switch ($req->get('format', 'json')) {
            case 'php':
                echo $res->php();
                break;
            case 'xml':
                header("Content-Type: text/xml");
                echo $res->xml();
                break;
            case 'jsonp':
                header('Content-Type: application/json');
                echo $req->get('callback', 'myCallback') . '(' . $res->json() . ')';
                break;
            case 'serialized':
                header('Content-Type: text/plain');
                echo $res->serialized();
                break;
            case 'json':
            default:
                header('Content-Type: application/json');
                echo $res->json();
                break;
        }
    }
}