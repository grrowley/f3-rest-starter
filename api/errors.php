<?php

class Errors
{
    function renderError($app)
    {
        $request  = \Request::instance();
        $response = \Response::instance();

        $response->set('status', $app->ERROR['code']);
        $response->set('message', $app->ERROR['status']);
        
        if ($app->ERROR['trace']) {
            $response->set('_trace', $app->ERROR['trace']);
        }
        
        if ($app->ERROR['level']) {
            $response->set('_level', $app->ERROR['level']);
        }
        
        switch ($request->get('format', 'json')) {
            case 'php':
                echo $response->php();
                break;
            case 'xml':
                header("Content-Type: text/xml");
                echo $response->xml();
                break;
            case 'jsonp':
                header('Content-Type: application/json');
                echo $request->get('callback', 'myCallback') . '(' . $response->json() . ')';
                break;
            case 'serialized':
                header('Content-Type: text/plain');
                echo $response->serialized();
                break;
            case 'json':
            default:
                header('Content-Type: application/json');
                echo $response->json();
                break;
        }
    }
}