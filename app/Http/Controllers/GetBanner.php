<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class GetBanner extends Controller
{
    use CommonFunction;
    function getBanner(Request $request, $id)
    {
        $errors = $this->errors();
        if (!isset($id) && empty($id)) {
            return array("statusCode" => 500, "message" => $errors['fill_form_message']);
            die();
        }
        $fileName = $id . '.jpg';
        if (!Storage::disk('images')->exists($fileName)) {
            $request->attributes->add(['status' => "try to fetch profile pic to unauthorized page"]);
            return array("statusCode" => 500, "message" => $errors['page_not_found']);
            die();
        }
        $file = Storage::disk('images')->get($fileName);
        $type = Storage::disk('images')->mimeType($fileName);
        return response($file, 200)->header('Content-Type',  $type);
    }
}
