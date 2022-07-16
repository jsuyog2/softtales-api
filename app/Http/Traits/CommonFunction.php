<?php

namespace App\Http\Traits;

use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Signer;
use MiladRahimi\Jwt\Cryptography\Keys\RsaPrivateKey;
use MiladRahimi\Jwt\Generator;

trait CommonFunction
{
    public function createTempToken($data)
    {
        $data['exp'] = time() + 900;
        $signer = new HS256(env('HS256_KEY'));
        $generator = new Generator($signer);
        $jwt = $generator->generate($data);
        $jwtSend = str_replace(env('JWT_TEMP_ALGO'), "tt", $jwt);
        return $jwtSend;
    }
    public function createToken($data, $exp = null)
    {
        $path = env('PRIVATE_KEY_PATH');
        $privateKey = new RsaPrivateKey($path);
        $signer = new RS256Signer($privateKey);
        if ($exp) {
            $data['exp'] = time() + (60 * 60);
        } else {
            $data['exp'] = time() + (60 * 60 * 24 * 365);
        }
        $data['iat'] = time();
        $generator = new Generator($signer);
        $jwt = $generator->generate($data);
        $jwtSend = str_replace(env('JWT_ALGO'), "pt", $jwt);
        return $jwtSend;
    }

    public function errors()
    {
        $jsonString = file_get_contents(base_path('resources/errors.json'));
        $errors = json_decode($jsonString, true);
        return $errors;
    }
}
