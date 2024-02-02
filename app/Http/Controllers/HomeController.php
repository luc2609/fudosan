<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        try {
            $appVersion = 'Laravel Version: ' . app()->version();
            $mysqlVersion = 'MySQL Version: ';
            DB::connection()->getPdo();
            if (DB::connection()->getDatabaseName())
            {
                $dbConnect = 'Connected to the Database';
                $query = DB::select(DB::raw("select version()"));
                $mysqlVersion .=  $query[0]->{'version()'};
            }
        } catch (Exception $e) {
            $dbConnect = 'Database not connect';
            $mysqlVersion .= 'Unconfimred';
        }

        try {
            Redis::connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', 6379));
            $redisServer = 'Redis working';
        } catch(ConnectionException $e) {
            $redisServer = 'Redis not working';
        }

        return view('home', compact('dbConnect', 'appVersion', 'mysqlVersion', 'redisServer'));
    }
}
