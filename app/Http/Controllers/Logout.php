<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Logout extends Controller
{
    use CommonFunction;
    function logout(Request $request)
    {
        $errors = $this->errors();
        $user_id = $request->get('a');
        $token = $request->get('token');
        $verify = DB::select('SELECT user_id FROM device_management WHERE token like ?;', [$token]);
        if ($verify) {
            try {
                $update = DB::table('device_management')
                    ->where('user_id', $user_id)
                    ->where('token', $token)
                    ->update(['blocked' => 1, 'blocked_on' => now()]);
                $request->attributes->add(['status' => 'logout successfully']);
                return array("statusCode" => 200, "message" => $errors['success_logout']);
            } catch (\Throwable $th) {
                $request->attributes->add(['status' => 'logout failed']);
                return array("statusCode" => 500, "message" => $errors['internal_error']);
            }
        } else {
            $request->attributes->add(['status' => 'token not avaliable in database']);
            return array("statusCode" => 500, "message" => $errors['user_not_found_message']);
        }
    }
}
