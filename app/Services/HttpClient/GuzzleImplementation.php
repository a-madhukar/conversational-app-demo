<?php 

namespace App\Services\HttpClient; 

use Log; 
use GuzzleHttp\Pool; 
use GuzzleHttp\Client;
use GuzzleHttp\Promise; 
use GuzzleHttp\Psr7\Request; 
use App\Services\HttpClient\Http; 

class GuzzleImplementation implements Http
{

	/**
	 * Instance of the client
	 *
	 * @var Client $client
	 */
	protected $client; 


	/**
	 * Instance of pool
	 *  
	 * @var Pool $pool 
	 */
	protected $pool; 


	/**
	 * Initialize the variables  
	 *
	 * @param GuzzleHttp\Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client; 

		// $this->pool = $pool; 
	}


	/**
	 * Make a get request with GuzzleHttp  
	 *
	 * @param String $url 
	 * @param Array $parameters
	 * @return $string
	 */
	public function get($url, $parameters = [], $headers = [])
	{
		Log::info("Making a get request");
		Log::info($url, $parameters, $headers);
		Log::info(__LINE__);  
		if(count($headers) > 0)
		{
			$response = $this->client->request('GET',$url,[
			'headers' => $headers,
			],[
			'query' => $parameters
			]);
		}else
		{
			$response = $this->client->request('GET',$url,[
			'query' => $parameters
			]);
		}
		
		// dd($response); 


		$code = $response->getStatusCode(); 

		Log::info("Response Code: ");
		Log::info($code);  
		 // dd($code); 

		if($code == 200)
		{
			// dd((string) $response->getBody()); 
			return (string) $response->getBody(); 

		}else
		{
			abort(403, "Failed to make a GET request to the provided url."); 
		}

	}



	/**
	 * Make a post request to the given url  
	 *
	 * @param String $url 
	 * @param Array $formParams
	 * @return String 
	 */
	public function post($url, $formParams = [], $headers = [])
	{
		$response = $this->client->request('POST', $url, [
				'form_params'=> $formParams
			],[

				'headers' => $headers

			]
		); 


		if($response->getStatusCode() >= 400)
		{
			abort(403, "Failed to make a GET request to the provided url.");
		}
		// return (string) $response->getBody(); 
		return $response; 
	}



	/**
	 * Make concurrent requests to an array or urls 
	 *
	 * 
	 */
	public function concurrentRequests($urls)
	{

		$promises = $this->buildArrayOfAsyncRequests($urls); 

		try{

			// Wait for the requests to complete, even if some of them fail
			$results = Promise\settle($promises)->wait();

			return $this->getArrayOfResults($results); 

		}catch(Exception $e){

			abort(403, $e->getMessage()); 
		}

	}


	public function getArrayOfResults($results)
	{
		// dd($results); 

		$strings = []; 

		foreach ($results as $tag =>  $result) {

			# code...
			if(isset($result['value']))
			{
				$response = $result['value']; 

				if($response->getStatusCode() == 200)
				{
					$string = html_entity_decode((string)$response->getBody()); 

					$strings = array_add($strings, $tag,$string); 
				}
			}
		}

		return $strings; 
	}


	/**
	 * 
	 *
	 * 
	 */
	public function buildArrayOfAsyncRequests($urls)
	{
		// dump($urls); 

		$promises = []; 

		foreach ($urls as $tag =>  $call) {

			$request = $this->client->requestAsync('GET', $call['url'],[

				'query'=>$call['parameters'], 

			]);  

			$promises = array_add($promises, $tag ,$request); 
		}

		return $promises; 
	}

}