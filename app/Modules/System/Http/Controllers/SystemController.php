<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;

use DB;

use Illuminate\Support\Facades\Config;


class SystemController extends Controller
{
    public function switchEnvironment()
    {

        Config::set("database.connections.mysql", [
            'driver' => 'mysql',
            "host" => "127.0.0.1",
            "database" => "korvion_testing",
            "username" => "root",
            "password" => env('DB_PASSWORD', ''),
            "port" => '3306',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);
        return "success  Switch Success";
    }

    public function oopsScreen(){

        return view('generic.oops');}

}
