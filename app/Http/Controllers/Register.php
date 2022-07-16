<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use App\Mail\MailSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Register extends Controller
{
    use CommonFunction;
    function register(Request $request)
    {
        $input = $request->post();
        $errors = $this->errors();
        if (!isset($input['password'], $input['email'], $input['tcCheck'], $input['cpassword']) && (empty($input['password']) || empty($input['email'] || empty($input['tcCheck'] || $input['cpassword'])))) {
            return array("statusCode" => 500, "message" => $errors['fill_form_message']);
            die();
        }

        if ($input['password'] != $input['cpassword']) {
            return array("statusCode" => 500, "message" => $errors['password_match_message']);
            die();
        }

        if (strlen($input['password']) < 8) {
            return array("statusCode" => 500, "message" => $errors['password_validate_message']);
            die();
        }
        $tcCheck = $input['tcCheck'] === 'true' ? true : false;
        if ($tcCheck) {
            $email = $input['email'];
            $number = $input['contactNo'];
            $query = DB::select('SELECT EXISTS(select 1 from user_table where email like :email) as emailexist,
                                    EXISTS(select 1 from user_table where cnumber like :number) as mobileexist', ['email' => $email, 'number' => $number]);
            if ($query[0]->emailexist && $query[0]->mobileexist) {
                return array("statusCode" => 500, "message" =>  $errors['user_found_message']);
                die();
            } else {
                $pass = $input['password'];
                $password = Hash::make($pass);
                try {
                    $insert = DB::insert('INSERT INTO user_table(password, email, cnumber, verified, created_on,tccheck)
                                        VALUES(?,?,?,false,NOW(),?);', [$password, $email, $number, $tcCheck]);
                                     
                    $query = DB::select('SELECT user_id ,email,verified FROM user_table WHERE email like ?;', [$email]);
                    if ($query) {
                        $user_id  = $query[0]->user_id;
                        $verified = $query[0]->verified;
                        $data = ['a' => $user_id, 'v' => $verified, 'u' => $email];
                        $jwt = $this->createTempToken($data);
                        $url = env('BASE_URL') . 'verify?access_token=' . $jwt;
                        Mail::to($email)->send(new MailSend($url));
                        $data['status'] = "successful";
                        $request->attributes->add($data);
                        return array("statusCode" => 200, "message" =>  $errors['success_registration'], "token" => $jwt);
                        die();
                    } else {
                        return array("statusCode" => 500, "message" => $errors['internal_error']);
                        die();
                    }
                } catch (\Throwable $th) {
                    return array("statusCode" => 500, "message" => $errors['internal_error']);
                    die();
                }
            }
        } else {
            return array("statusCode" => 500, "message" => $errors['tc_not_checked_message']);
            die();
        }
    }
}
