<?php

namespace App\Http\Middleware;

use App\Http\Traits\CommonFunction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\RsaPublicKey;
use MiladRahimi\Jwt\Parser;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public $verified = 0;
    use CommonFunction;
    public function handle(Request $request, Closure $next)
    {
        $vaild = false;
        $errors = $this->errors();
        $token = $request->bearerToken();
        if (preg_match('/tt\.+[A-Za-z0-9]+.+[A-Za-z0-9]/', $token)) {
            $vaild = $this->verifyTempToken($request, $token);
        } else if (preg_match('/pt\.+[A-Za-z0-9]+.+[A-Za-z0-9]/', $token)) {
            $vaild = $this->verifyToken($request, $token);
        } else {
            $vaild = false;
        }
        if (!$vaild) {
            $errors = $this->errors();
            $res = array("statusCode" => 500, "message" => $errors['access_token_error_message']);
            return response()->json($res, 500);
        }
        if (!(int)$this->verified) {
            return response()->json(["statusCode" => 500, "message" => $errors['email_not_verified_message']], 500);
        }
        return $next($request);
    }

    function verifyTempToken($request, $token)
    {
        $signer = new HS256(env('HS256_KEY'));
        $parser = new Parser($signer);
        $tokenSend = str_replace("tt", env('JWT_TEMP_ALGO'), $token);
        try {
            $claims = $parser->parse($tokenSend);
            $claims['token'] = $token;
            $request->attributes->add($claims);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    function verifyToken($request, $token)
    {
        $path = env('PUBLIC_KEY_PATH');
        $publicKey = new RsaPublicKey($path);
        $verifier = new RS256Verifier($publicKey);
        $parser = new Parser($verifier);
        $tokenSend = str_replace("pt", env('JWT_ALGO'), $token);
        try {
            $query = DB::select('SELECT * FROM `device_management` WHERE token like ?;', [$token]);
            if ($query[0]->blocked) {
                return false;
            } else {
                $claims = $parser->parse($tokenSend);
                $this->verified = $claims['v'];
                $claims['token'] = $token;
                $request->attributes->add($claims);
                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }
}
