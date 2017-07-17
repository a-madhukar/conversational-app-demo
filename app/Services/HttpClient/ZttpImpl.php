<?php 

namespace App\Services\HttpClient; 

use Zttp\Zttp; 

class ZttpImpl implements Http 
{
	

	public function post($url, $params = [])
	{
		info("posting to $url"); 
		info($params);
		 
		return (Zttp::post($url, $params))->json(); 
	}

}