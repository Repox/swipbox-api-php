<?php namespace Swipbox;

class Parcel
{
	private $_client;
	public function __construct( \Swipbox\Client $client )
	{
		$this->_client = $client;
	}

	public function create($data)
	{		
		$request = $this->_client->_execute('create', $data);
		return $request->json();
	}
	
	public function track($data)
	{
		$request = $this->_client->_execute('track', $data);
		return $request->json();		
	}

	public function activate($data)
	{
		$request = $this->_client->_execute('activate', $data);
		return $request->json();		
	}
	
	public function get_label($data)
	{
		$request = $this->_client->_execute('get_label', $data);
		return $request->json();		
	}
	
	public function cancel($data)
	{
		$request = $this->_client->_execute('cancel', $data);		
		return $request->json();				
	}
	
		
}
