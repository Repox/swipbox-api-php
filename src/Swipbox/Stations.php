<?php namespace Swipbox;

class Stations
{
	private $_client;
	public function __construct( \Swipbox\Client $client )
	{
		$this->_client = $client;
	}
	
	public function find_nearest($data)
	{
		$request = $this->_client->_execute('find_nearest', $data);		
		return $request->json();		
	}
	
	public function find_active_favorites($data)
	{
		$request = $this->_client->_execute('find_active_favorites', $data);		
		return $request->json();				
	}
	
	public function find_near_to_favorite($data)
	{
		$request = $this->_client->_execute('find_near_to_favorite', $data);		
		return $request->json();						
	}
	
	public function find_by_zip($data)
	{
		$request = $this->_client->_execute('find_by_zip', $data);		
		return $request->json();								
	}
	
	public function get_station_by_id($data)
	{
		$request = $this->_client->_execute('get_station_by_id', $data);
		return $request->json();										
	}

}