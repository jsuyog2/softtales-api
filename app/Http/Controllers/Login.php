<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use App\Mail\MailSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Login extends Controller
{
    use CommonFunction;
    function login(Request $request)
    {
        $errors = $this->errors();
        $input = $request->post();

        if (!isset($input['password'], $input['email']) && (empty($input['password']) || empty($input['email']))) {
            return array("statusCode" => 500, "message" => $errors['fill_form_message']);
            die();
        }
        $user = $input['email'];
        $pass = $input['password'];
        $query = DB::select('SELECT user_id,email,password,verified,role FROM user_table WHERE email like ?;', [$user]);
        if ($query) {
            $password = $query[0]->password;
            $user_id = $query[0]->user_id;
            $email = $query[0]->email;
            $data = ['a' => $user_id, 'u' => $email];
            if (Hash::check($pass, $password)) {
                $verified = $query[0]->verified;
                $role = (int)$query[0]->role;
                $data['v'] = $verified;
                $data['r'] = $role;
                if ($verified) {
                    $jwt = $this->createToken($data, ($input['admin'] == 'true' && $role >= 1));
                    $data['status'] = "successful";
                    $request->attributes->add($data);
                    $device_name = $_SERVER['HTTP_USER_AGENT'];
                    $ip = request()->ip();
                    $query = DB::insert('INSERT INTO device_management(user_id, device_name , device_ip, token, created_on, blocked)VALUES(?,?,?,?,NOW(),0);', [$user_id, $device_name, $ip, $jwt]);
                    return array("statusCode" => 200, "role" => $role, "message" => $errors['success_login'], "token" => $jwt);
                    die();
                } else {
                    $jwt = $this->createTempToken($data);
                    $url = env('BASE_URL') . 'verify?access_token=' . $jwt;
                    Mail::to($email)->send(new MailSend($url));
                    $data['status'] = "Unverified Login";
                    $request->attributes->add($data);
                    return array("statusCode" => 250, "message" => $errors['success_unvrified_login'], "token" => $jwt);
                    die();
                }
            } else {
                $data['status'] = "Wrong Password";
                $request->attributes->add($data);
                return array("statusCode" => 500,  "message" => $errors['password_wrong_message']);
                die();
            }
        } else {
            return array("statusCode" => 500, "message" => $errors['user_not_found_message']);
            die();
        }
    }
}
