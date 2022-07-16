<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\CommonFunction;

class UserResponse extends Controller
{
    use CommonFunction;
    function userResponse(Request $request)
    {
        $errors = $this->errors();
        $verified = $request->get('v');
        $user_id = $request->get('a');
        $query = DB::select('SELECT CONCAT(u.fname," ",u.lname) as name,u.fname,u.lname,u.email,u.cnumber,u.gender,u.role,u.tccheck FROM user_table u WHERE u.user_id like ?;', [$user_id]);
        if ($query) {
            $name = ucwords($query[0]->name);
            $email = $query[0]->email;
            $cnumber = $query[0]->cnumber;
            $fname = ucwords($query[0]->fname);
            $lname = ucwords($query[0]->lname);
            $gender = $query[0]->gender;
            $role = null;
            if ($query[0]->role) {
                $role = $query[0]->role;
            }
            $tccheck = $query[0]->tccheck;
            $request->attributes->add(['status' => 'get all user data']);
            return array("statusCode" => 200, "data" => array("name" => $name, "fname" => $fname, "lname" => $lname, "email" => $email, "number" => $cnumber, "gender" => $gender, "role" => $role, "tccheck" => $tccheck));
        } else {
            return array("statusCode" => 500, "message" => $errors['user_not_found_message']);
        }
    }
}
