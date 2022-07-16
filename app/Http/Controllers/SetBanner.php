<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class SetBanner extends Controller
{
    use CommonFunction;
    function setBanner(Request $request)
    {
        $errors = $this->errors();
        $input = $request->post();
        if (!isset($input['banner_id']) && empty($input['banner_id'])) {
            return array("statusCode" => 500, "message" => $errors['fill_form_message']);
            die();
        }
        $banner_id = $input['banner_id'];
        $file = $request->file('banner_pic');
        $fileArray = array('banner_pic' => $file);
        $rules = array('banner_pic' => 'required|image|mimes:jpg|max:500000');
        $validator = Validator::make($fileArray, $rules);
        if ($validator->fails()) {
            return array("statusCode" => 500, "message" => $errors['image_not_valid']);
        } else {
            $verified = $request->get('v');
            $role = (int)$request->get('r');
            if (!$verified) {
                return array("statusCode" => 500, "message" => $errors['email_not_verified_message']);
            } else {
                if ($role >= 1) {
                    $fileName = $banner_id . '.jpg';
                    if (Storage::disk('images')->exists($fileName)) {
                        unlink(storage_path('app/images/') . strtolower($banner_id . '.' . $file->getClientOriginalExtension()));
                    }
                    $path = $file->move(storage_path('app/images/'), strtolower($banner_id . '.' . $file->getClientOriginalExtension()));
                    if ($path) {
                        $request->attributes->add(['status' => 'upload db update banner to ' . $path]);
                        return array("statusCode" => 200, "message" => $errors['image_upload']);
                    } else {
                        $request->attributes->add(['status' => 'error to upload banner to ' . $path]);
                        return array("statusCode" => 500, "message" => $errors['image_not_update']);
                    }
                } else {
                    return array("statusCode" => 500, "message" => $errors['unauthorized_message']);
                }
            }
        };
    }
}
