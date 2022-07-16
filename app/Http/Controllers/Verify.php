<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Parser;

class Verify extends Controller
{
    use CommonFunction;
    function verify(Request $request)
    {
        $errors = $this->errors();
        $token = $request['access_token'];
        if (empty($token) && !isset($token)) {
            return view('verification', ["message" => $errors['access_token_not_found_message']]);
            die();
        }
        $signer = new HS256(env('HS256_KEY'));
        $parser = new Parser($signer);
        $tokenSend = str_replace("tt", env('JWT_TEMP_ALGO'), $token);
        try {
            $claims = $parser->parse($tokenSend);
            if ($claims) {
                $verified = $claims['v'];
                $user_id = $claims['a'];
                $query = DB::select('SELECT user_id,email,verified FROM user_table WHERE user_id like ?;', [$user_id]);
                if ($query) {
                    $verifiedDB = $query[0]->verified;
                    if (!$verified && !$verifiedDB) {
                        $update = DB::update('UPDATE user_table SET verified=true where user_id = ?;', [$user_id]);
                        if ($update) {
                            $request->attributes->add(['a' => $user_id, 'status' => "successful"]);
                            return view('verification', ["message" => $errors['success_verification']]);
                            die();
                        } else {
                            $request->attributes->add(['a' => $user_id, 'status' => "failed"]);
                            return view('verification', ["message" => $errors['internal_error']]);
                            die();
                        }
                    } else {
                        $request->attributes->add(['a' => $user_id, 'status' => "verification already"]);
                        return view('verification', ["message" => $errors['email_verified_message']]);
                        die();
                    }
                } else {
                    return view('verification', ["message" => $errors['user_not_found_message']]);
                    die();
                }
            } else {
                return view('verification', ["message" => $errors['access_token_error_message']]);
                die();
            }
        } catch (\Throwable $th) {
            return view('verification', ["message" => $errors['access_token_error_message']]);
            die();
        }
    }
}
