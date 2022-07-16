<?php

namespace App\Http\Middleware;

use App\Http\Traits\CommonFunction;
use Closure;
use Illuminate\Http\Request;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Validator\BaseValidator;

class withoutVerifyTempToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    use CommonFunction;
    public function handle(Request $request, Closure $next)
    {
        $vaild = false;
        $token = $request->bearerToken();
        if (preg_match('/tt\.+[A-Za-z0-9]+.+[A-Za-z0-9]/', $token)) {
            $signer = new HS256(env('HS256_KEY'));
            $validator = new BaseValidator();
            $parser = new Parser($signer, $validator);
            $tokenSend = str_replace("tt", env('JWT_TEMP_ALGO'), $token);
            try {
                $claims = $parser->parse($tokenSend);
                $claims['token'] = $token;
                $request->attributes->add($claims);
                $vaild = true;
            } catch (\Throwable $th) {
                echo $th->getMessage();
                $vaild = false;
            }
        } else {
            $vaild = false;
        }
        if (!$vaild) {
            $errors = $this->errors();
            $res = array("statusCode" => 500, "message" => $errors['access_token_error_message']);
            return response()->json($res, 500);
        }
        return $next($request);
    }
}
