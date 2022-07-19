<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddProduct extends Controller
{
    use CommonFunction;
    function addProduct(Request $request)
    {
        $errors = $this->errors();
        $data = $request->all();
        $validator = Validator::make($data, [
            'product_image' => 'required|max:500000', //|image|mimes:jpg
            'product_image.*' => 'image|mimes:jpg',
            'product_name' => 'required',
            'product_description' => 'required',
            'product_price' => 'required',
            'sale_price' => 'required',
            'product_quantity' => 'required',
            'product_sku' => 'required',
            'product_category' => 'required',
            'product_subcategory' => 'required',
            'product_weight' => 'required',
            'product_length' => 'required',
            'product_width' => 'required',
            'product_height' => 'required',
            'product_tags' => 'required'
        ]);
        if ($validator->fails()) {
            return array("statusCode" => 500, "message" => $errors['fill_form_message']);
            die();
        }
        $verified = $request->get('v');
        $role = (int)$request->get('r');
        if (!$verified) {
            return array("statusCode" => 500, "message" => $errors['email_not_verified_message']);
            die();
        }
        if ($role <= 0) {
            return array("statusCode" => 500, "message" => $errors['unauthorized_message']);
            die();
        }
        $query = DB::select('SELECT * FROM product_table WHERE product_name = ? OR product_sku = ?', [$request['product_name'], $request['product_sku']]);
        if (count($query) >= 1) {
            $request->attributes->add(['status' => 'trying to add same product']);
            return array("statusCode" => 500, "message" => $errors['product_exist']);
            die();
        }
        $insert = DB::table('product_table')->insert(
            [
                'product_name' => $request['product_name'],
                'product_price' =>  $request['product_price'],
                'product_sale_price' =>  $request['product_sale_price'],
                'product_sku' =>  $request['product_sku'],
                'product_description' =>  $request['product_description'],
                'product_category' =>  $request['product_category'],
                'product_subcategory' =>  $request['product_subcategory'],
                'product_quantity' =>  $request['product_quantity'],
                'product_status' =>  $request['product_status'],
                'product_weight' =>  $request['product_weight'],
                'product_length' =>  $request['product_length'],
                'product_width' =>  $request['product_width'],
                'product_height' =>  $request['product_height'],
                'product_tags' =>  $request['product_tags'],
                'product_created_on' =>  NOW()
            ]
        );
        $id = DB::getPdo()->lastInsertId();
        $lastKey = [];
        if ($request->hasfile('product_image')) {
            foreach ($request->file('product_image') as $key => $file) {
                $path = $file->move(storage_path('app/images/products/' . $id), strtolower(($key + 1) . '.' . $file->getClientOriginalExtension()));
                array_push($lastKey, strtolower(env('BASE_URL') . 'getProductImage/' . $id . '/' . ($key + 1)));
            }
        }
        $update = DB::update('update product_table set product_image = ? where product_id = ?', [implode(',', $lastKey), $id]);
        if ($update) {
            $request->attributes->add(['status' => 'product_added']);
            return array("statusCode" => 200, "message" => $errors['product_added']);
        } else {
            $request->attributes->add(['status' => 'product_added failed']);
            return array("statusCode" => 200, "message" => $errors['product_not_added']);
        }
        die();
    }
}
