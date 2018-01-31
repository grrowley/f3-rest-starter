<?php

class Token extends Prefab
{
    static function encode($payload, $secret, $algo = 'HS256', $throw = true)
    {
        $header = array('typ' => 'JWT', 'alg' => $algo);

        $segments = array(
            Token::urlsafeB64Encode(json_encode($header)),
            Token::urlsafeB64Encode(json_encode($payload))
        );

        $signing_input = implode('.', $segments);

        $signature = Token::sign($signing_input, $secret, $algo, $throw);
        $segments[] = Token::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    static function decode($jwt, $secret = null, $algo = null, $throw = true, $array = true)
    {
        $tks = explode('.', $jwt);

        if (count($tks) != 3) {
            // throw new Exception('Wrong number of segments');
            return false;
        }

        list($headb64, $payloadb64, $cryptob64) = $tks;

        if (null === ($header = json_decode(Token::urlsafeB64Decode($headb64)))) {
            if ($throw === true) {
                throw new Exception('Invalid segment encoding');
            }
            return false;
        }

        if (null === $payload = json_decode(Token::urlsafeB64Decode($payloadb64))) {
            if ($throw === true) {
                throw new Exception('Invalid segment encoding');
            }
            return false;
        }

        $sig = Token::urlsafeB64Decode($cryptob64);

        if (isset($secret)) {

            if (empty($header->alg)) {
                if ($throw === true) {
                    throw new DomainException('Empty algorithm');
                } else {
                    return false;
                }
            }

            if (!Token::verify($sig, "$headb64.$payloadb64", $secret, $algo, $throw)) {
                if ($throw === true) {
                    throw new UnexpectedValueException('Signature verification failed');
                } else {
                    return false;
                }
            }
            
        }

        return $array === true ? get_object_vars($payload) : $payload;
    }

    private static function verify($signature, $input, $secret, $algo, $throw)
    {
        switch ($algo) {
            case'HS256':
            case'HS384':
            case'HS512':
                return Token::sign($input, $secret, $algo, $throw) === $signature;

            case 'RS256':
                return (boolean) openssl_verify($input, $signature, $secret, OPENSSL_ALGO_SHA256);

            case 'RS384':
                return (boolean) openssl_verify($input, $signature, $secret, OPENSSL_ALGO_SHA384);

            case 'RS512':
                return (boolean) openssl_verify($input, $signature, $secret, OPENSSL_ALGO_SHA512);

            default:
                if ($throw === true) {
                    throw new Exception("Unsupported or invalid signing algorithm.");
                } else {
                    return false;
                }
        }
    }

    private static function sign($input, $secret, $algo, $throw)
    {
        switch ($algo) {

            case 'HS256':
                return hash_hmac('sha256', $input, $secret, true);

            case 'HS384':
                return hash_hmac('sha384', $input, $secret, true);

            case 'HS512':
                return hash_hmac('sha512', $input, $secret, true);

            case 'RS256':
                return Token::generateRSA($input, $secret, OPENSSL_ALGO_SHA256, $throw);
            case 'RS384':
                return Token::generateRSA($input, $secret, OPENSSL_ALGO_SHA384, $throw);
            case 'RS512':
                return Token::generateRSA($input, $secret, OPENSSL_ALGO_SHA512, $throw);

            default:
                if ($throw === true) {
                    throw new Exception("Unsupported or invalid signing algorithm.");
                } else {
                    return false;
                }
        }
    }

    private static function generateRSA($input, $secret, $algo, $throw)
    {
        if (!openssl_sign($input, $signature, $secret, $algo)) {
            if ($throw === true) {
                throw new Exception("Unable to sign data.");
            } else {
                return false;
            }
        }

        return $signature;
    }

    private static function urlSafeB64Encode($data)
    {
        $b64 = base64_encode($data);
        $b64 = str_replace(array('+', '/', '\r', '\n', '='),
                array('-', '_'),
                $b64);

        return $b64;
    }

    private static function urlSafeB64Decode($b64)
    {
        $b64 = str_replace(array('-', '_'),
                array('+', '/'),
                $b64);

        return base64_decode($b64);
    }
}