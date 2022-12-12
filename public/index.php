<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// $ipaddress = '';
// if (isset($_SERVER['HTTP_CLIENT_IP']))
//     $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
// else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
//     $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
// else if(isset($_SERVER['HTTP_X_FORWARDED']))
//     $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
// else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
//     $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
// else if(isset($_SERVER['HTTP_FORWARDED']))
//     $ipaddress = $_SERVER['HTTP_FORWARDED'];
// else if(isset($_SERVER['REMOTE_ADDR']))
//     $ipaddress = $_SERVER['REMOTE_ADDR'];
// else
//     $ipaddress = 'UNKNOWN';

// if($ipaddress =='182.75.128.170' || $ipaddress == '127.0.0.1' || $ipaddress == '223.178.213.182')
// {
// }
// else
// {
//     exit("hii");

// }

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is maintenance / demo mode via the "down" command we
| will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
