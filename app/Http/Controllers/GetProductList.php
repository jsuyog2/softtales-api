<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetProductList extends Controller
{
    use CommonFunction;
    public function getProductList()
    {
        $query = DB::select('SELECT * FROM product_table');
        return array("statusCode" => 200, "data" => $query);
    }
}
