<?php 
namespace Swipbox;

/**
 * SwipBox PHP API Integration
 *
 * SwipBox is a new shipping option, primarely focused on e-commerce solutions.
 *
 * This is a PHP composer package for simplifying SwipBox integration
 * into PHP based webshops.
 *
 * @author Dan Storm <storm@catalystcode.net>
 * @link http://www.catalystcode.net
 */
 
class Client
{
	private $_http, $_guid;
	
	/**
	 * The constructor needs your Webshop GUID provided by SwipBox
	 * http://www.swipbox.com/
	 *
	 * By setting the second parameter to (boolean) true you enable
	 * testing mode.
	 * 
	 * @param string $guid The Webshop GUID provided by Swipbox
	 * @param boolean $test Set to true if you are testing. Defaults to false
	 */
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
	
	/**
	 * Creates a parcel
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */
	public function create( Array $params )
	{
		return $this->_execute('create', $params);
	}

	/**
	 * Activates a parcel
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Mixed A decoded JSON array, XML document or PDF
	 */	
	public function activate( Array $params )
	{
		return $this->_execute('activate', $params);
	}	

	/**
	 * Gets the label for an activated parcel
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Mixed A decoded JSON array, XML document or PDF
	 */	
	public function get_label( Array $params )
	{
		return $this->_execute('get_label', $params);
	}

	/**
	 * Cancels a parcel based on an activated parcels barcode
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */		
	public function cancel( Array $params )
	{
		return $this->_execute('cancel', $params);
	}

	/**
	 * Find the nearest parcel recieving stations based on specific
	 * address.
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */			
	public function find_nearest( Array $params )
	{
		return $this->_execute('find_nearest', $params);
	}

	/**
	 * Gets favorite parcel stations attached to a customer.
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */		
	public function find_active_favorites( Array $params )
	{
		return $this->_execute('find_active_favorites', $params);
	}

	/**
	 * Gets parcel stations near to the first favorite stations
	 * attached to a customer.
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */		
	public function find_near_to_favorite( Array $params )
	{
		return $this->_execute('find_near_to_favorite', $params);
	}

	/**
	 * Simpler method to get parcel stations by only providing a zip code.
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */						
	public function find_by_zip( Array $params )
	{
		return $this->_execute('find_by_zip', $params);
	}

	/**
	 * Get the details of a specific station
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */						
	public function get_station_by_id( Array $params )
	{
		return $this->_execute('get_station_by_id', $params);
	}

	
	/**
	 * Track the parcels trip
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */			
	public function track( Array $params )
	{
		return $this->_execute('track', $params);
	}		
	/**
	 * Gets parcel stations near to the first favorite stations
	 * attached to a customer.
	 *
	 * @param Array $params An array of parameters as specified in the docs.
	 * @throws \Swipbox\Exception
	 * @return Array A decoded JSON array
	 */				
	private function _execute($service_name, $data)
	{
		try
		{
			$response = $this->_http->get($service_name.'?'.http_build_query(array_merge($data, array('guid' => $this->_guid))))->send();		
		}
		catch(Exception $e)
		{
			return false;
		}
		
		//application/json
		//application/xml
		//application/pdf
			if($response->isSuccessful())
			{
				if(stristr($response->getContentType(), 'application/json'))
				{
					$data = $response->json();
					if(isset($data['error']))
					{
						throw new \Swipbox\Exception($data['error']['description'], $data['error']['code']);
					}
					return $data;
				}
				elseif(stristr($response->getContentType(), 'application/xml'))
				{
					return $response->xml();
				}
				elseif(stristr($response->getContentType(), 'application/pdf'))
				{
					return $response->getBody();
				}
			}
			
			return false;
	}
	
	
	
}