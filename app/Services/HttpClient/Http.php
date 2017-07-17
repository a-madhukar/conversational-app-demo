<?php 

namespace App\Services\HttpClient; 

interface Http 
{

	// public function get(); 

	public function post($url); 
}