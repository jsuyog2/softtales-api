<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use App\Mail\MailSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Reverify extends Controller
{
    use CommonFunction;
    function reverify(Request $request)
    {
        $errors = $this->errors();
        $verified = $request->get('v');
        $mail = $request->get('u');
        $id = $request->get('a');
        $query = DB::select('SELECT user_id,email,verified FROM user_table WHERE user_id like ?;', [$id]);
        if ($query) {
            $verifiedDB = $query[0]->verified;
            if (!$verified && !$verifiedDB) {
                $data = ['a' => $id, 'v' => $verified, 'u' => $mail];
                $jwt = $this->createTempToken($data);
                $url = env('BASE_URL') . 'verify?access_token=' . $jwt;
                Mail::to($mail)->send(new MailSend($url));
                $request->attributes->add(['status' => "successful mail sent"]);
                return array("statusCode" => 200, "message" => $errors['success_mail_sent']);
            } else {
                $request->attributes->add(['status' => "verification already"]);
                return array("statusCode" => 500, "message" => $errors['email_verified_message']);
                die();
            }
        } else {
            return array("statusCode" => 500, "message" => $errors['user_not_found_message']);
            die();
        }
    }
}
