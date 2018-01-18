<?php

class Response extends Prefab
{
    private static $res;

    function __construct()
    {
        self::$res = [
            'status'  => 500,
            'success' => null,
            'message' => null,
            'content' => [],
        ];
    }

    function set($key, $val = null)
    {
        $app = Base::instance();
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Helper::array_set(self::$res, $k, $v);
            }
        } else {
            Helper::array_set(self::$res, $key, $val);
        }
    }

    function php()
    {
        $res = self::finalize();
        return var_export($res, true);
    }

    function xml()
    {
        $res = self::finalize();
        $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
               Helper::array_to_xml($res, $xml);
        return $xml->asXML();
    }

    function json($opts = JSON_NUMERIC_CHECK)
    {
        $res = self::finalize();
        return json_encode($res, $opts);
    }

    function serialized()
    {
        $res = self::finalize();
        return serialize($res);
    }

    private function finalize()
    {
        $res = self::$res;
        if ($res['success'] === null) {
            $res['success'] = intval($res['status']) >= 300 ? false : true;
        }
        if ($res['message'] === null) {
            $res['message'] = Base::instance()->get('LANG.HTTP_RESPONSE.' . $res['status']);
        }
        if (Base::instance()->DEBUG >= 2) {
            $req = Request::instance();
            $res['_request'] = [
                'token' => $req->token(),
                'get'   => $req->get(),
                'post'  => $req->post(),
            ];
        }
        return $res;
    }
}