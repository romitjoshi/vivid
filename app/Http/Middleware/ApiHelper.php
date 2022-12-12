<?php
namespace App\Http\Middleware;
use Closure,Storage;
use Illuminate\Http\Response;
class ApiHelper{
	public function handle($request,Closure $next){
		return $next($request);
	}
	public function terminate($request,$response){
		if(!app()->environment('production')){
			$time = microtime(true);
			Storage::disk('local')->append('logs/requests/'.date('Y/F/d/').strtr(request()->route()->uri(),['api/'=>'','/'=>'_']).'.log',print_r([
				'request'=>[
					'time'=>LARAVEL_START,
					'path'=>request()->path(),
					'url'=>request()->url(),
					'method'=>request()->method(),
					'userAgent'=>request()->userAgent(),
					'ip'=>request()->ip(),
					'json'=>request()->json(),
					'file'=>request()->file(),
					'query'=>request()->query(),
					'header'=>request()->header(),
					'post'=>request()->post(),
					'user'=>request()->user() ? request()->user()->id : ''
				],
				'response'=>array_merge([
					'time'=>$time
				],json_decode($response->getContent(),true) ?? []),
				'time_taken'=>$time - LARAVEL_START
			],true));
		}
	}
}
