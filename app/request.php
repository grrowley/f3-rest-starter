<?php

class Request extends Prefab
{
    private static $req;

    function __construct()
    {
        // framework instance
        $app = Base::instance();

        // base request array
        $req = [
            'get'  => $app->GET,
            'post' => $app->POST
        ];
        
        // incoming request body parser
        if ($app->BODY) {
            if (Helper::is_json($app->BODY)) {
                $BODY = json_decode($app->BODY, TRUE);
            } else {
                parse_str($app->BODY, $BODY);
            }
            $req['post'] = array_merge($req['post'], $BODY ?: []);
        }
        
        // scope $req
        self::$req = $req;
    }

    function get($key = null, $default = null)
    {
        if ($key === null) {
            return self::$req['get'];
        } else {
            return Helper::array_get(self::$req['get'], $key) ?: $default;
        }
    }

    function post($key = null, $default = null)
    {
        if ($key === null) {
            return self::$req['post'];
        } else {
            return Helper::array_get(self::$req['post'], $key) ?: $default;
        }
    }

    function token($throw = false)
    {
        // framework instance
        $app = Base::instance();

        // check for internal request and set guest token
        if (!self::external() && !$app->exists('SESSION.token')) {
            $app->set('SESSION.token', Token::encode([
                'uid'      => null,
                'avatar'   => Helper::get_avatar('guest'),
                'rights'   => ['guest'],
            ], $app->APPKEY, 'HS512', false));
        }
        
        // check request origin
        if ($app->exists('HEADERS.Authorization', $token) || $app->exists('HEADERS.X-Requested-With', $token) || $app->exists('SESSION.token', $token)) {
            return Token::decode($token, $app->APPKEY, 'HS512', false);
        } else {
            return Token::decode($guest, $app->APPKEY, 'HS512', false);
        }

        // default return
        return false;
    }

    private static function external()
    {
        // framework instance
        $app = Base::instance();
        
        // check for external request indication
        if ($app->get('HEADERS.X-Origin') || $app->get('HEADERS.Origin') || $app->AJAX) {
            return true;
        }

        // assume it's an internal request
        return false;
    }
}