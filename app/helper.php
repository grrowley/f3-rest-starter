<?php

class Helper extends Prefab
{
    static function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    static function check_access($access = ['*'], $rights = ['guest'])
    {
        $app = Base::instance();
        if (!is_array($access)) {
            $access = $app->split($access);
        }
        if (!is_array($rights)) {
            $rights = $app->split($rights);
        }
        if (!in_array('*', $access)) {
            return count(array_diff($access, $rights)) < count($access) ? true : false;
        }
        return true;
    }

    static function get_avatar($id, $local = true)
    {
        $dir = ROOT . 'api/uploads/avatars/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $avi = $dir . md5(trim('/', $id)) . '.png';
        if (!file_exists($avi)) {
            $img = new Image();
            $img->identicon(md5(trim('/', $id)), 200, rand(2, 7));
            Base::instance()->write($avi, $img->dump('png', 9));
        }
        return str_replace(ROOT, $local === true ? '/' : $local, $avi);
    }

    static function array_get(&$array, $key)
    {
        $pieces = explode('.', $key);
        foreach ($pieces as $piece) {
            if (!is_array($array) || !array_key_exists($piece, $array)) {
                return NULL;
            }
            $array = &$array[$piece];
        }
        return $array;
    }

    static function array_set(&$array, $key, $value = FALSE)
    {
        $locale = &$array;
        $pieces = explode('.', $key);
        foreach ($pieces as $piece) {
            $locale = &$locale[$piece];
        }
        return $locale = $value;
    }

    static function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item'.$key;
            }
            if (is_array($value)) {
                $sub = $xml->addChild($key);
                self::array_to_xml($value, $sub);
            } else {
                $xml->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
}