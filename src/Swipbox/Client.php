<?php namespace Swipbox;

class Client
{
	private $_http, $_guid;
	
	public function __construct($guid, $test = false)
	{	
		if( $test )
		{
			$endpoint = "http://service.test.swipbox.com/api_v1/";
		}
		else
		{
			$endpoint = "http://service.swipbox.com/api_v1/";
		}

		$this->_http = new \Guzzle\Http\Client($endpoint);		
		$this->_guid = $guid;
	}
	
	public function parcel()
	{
		return new \Swipbox\Parcel($this);
	}
	
	public function stations()
	{
		return new \Swipbox\Stations($this);
	}
		
		
	public function _execute($service_name, $data)
	{
		$request = $this->_http->get($service_name.'?'.http_build_query(array_merge($data, array('guid' => $this->_guid))))->send();		
		return $request;		
	}
	
	
	
}