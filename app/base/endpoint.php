<?php

namespace Base;

abstract class Endpoint
{
    protected $token;

    protected $request;
    protected $respose;

    protected $access = ['*'];
    
    function __construct($app, $params)
    {
        $this->request  = \Request::instance();
        $this->response = \Response::instance();
        $this->token    = $this->request->token();
        // run initilization
        $this->init($app, $params);
    }

    function init($app, $params)
    {
        return;
    }

    function beforeRoute($app, $params)
    {
        if ($this->access && $this->token['rights']) {
            if (!\Helper::check_access($this->access, $this->token['rights'])) {
                $app->error(401, $app->get('LANG.HTTP_REPONSE.401'));
            }
        }
    }

    function afterRoute($app, $params)
    {
        switch ($this->request->get('format', 'json')) {
            case 'php':
                echo $this->response->php();
                break;
            case 'xml':
                header("Content-Type: text/xml");
                echo $this->response->xml();
                break;
            case 'jsonp':
                header('Content-Type: application/json');
                echo $this->request->get('callback', 'myCallback') . '(' . $this->response->json() . ')';
                break;
            case 'serialized':
                header('Content-Type: text/plain');
                echo $this->response->serialized();
                break;
            case 'json':
            default:
                header('Content-Type: application/json');
                echo $this->response->json();
                break;
        }
        return $app->abort();
    }
}