<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $activity_status = $request->get('status');
        if ($activity_status) {
            $id = $request->get('a');
            $ip = request()->ip();
            $activity_name = request()->path();
            $query = DB::insert('INSERT INTO user_activity(user_id , login_ip, activity_name,activity_status,activity_datetime)VALUES (?,?,?, ?,NOW());', [$id, $ip, $activity_name, $activity_status]);
        }
        return $response;
    }
}
