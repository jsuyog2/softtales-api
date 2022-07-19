<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GetProductImage extends Controller
{
    use CommonFunction;
    function getProductImage(Request $request, $product_id, $value)
    {
        $errors = $this->errors();
        $fileName = 'products/' . $product_id . '/' . $value . '.jpg';
        if (!Storage::disk('images')->exists($fileName)) {
            return array("statusCode" => 500, "message" => $errors['image_not_found']);
            die();
        }
        $file = Storage::disk('images')->get($fileName);
        $type = Storage::disk('images')->mimeType($fileName);
        return response($file, 200)->header('Content-Type',  $type);
    }
}
