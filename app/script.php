<?php

use App\client;
use App\User;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "hello world\n";
$phoneNum = "788160088";
$clinicAccount = client::where('clinic_phone', 'like', '%' . trim($phoneNum) . '%')->get()->first();
$docClient = client::where('phone', 'like', '%' . trim($phoneNum) . '%')->get()->first();

dd($docClient. "=========" . $clinicAccount ."========" . $phoneNum);
print_r($clinicAccount->name);
//print_r($docClient->name);
echo "end!\n";
