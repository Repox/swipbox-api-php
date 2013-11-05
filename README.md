API Client Library for SwipBox
==============================

Introduction
------------

A PHP Client library for easy SwipBox integration, which allows you fast implementation.

You need an account with [SwipBox](http://www.swipbox.com) to use the API.

### Index

* [Requirements](#requirements)
* [Installation](#installation)
	* [With Composer](#composer)
	* [Manual installation](#manual)
* [Getting Started](#gettingstarted)
* [Parcels](#parcels)
	* [Creating a parcel](#create)
	* [Activating a parcel](#activate)
	* [Labels for activated parcels](#get_label)
	* [Cancel parcel](#cancel)
* [Stations](#stations)
	* [Find nearest station](#find_nearest)
	* [Find Active Favorites](#find_active_favorites)
	* [Find stations near to favorite](#find_near_to_favorite)
	* [Find stations by zip](#find_by_zip)
	* [Get station by its pick up ID](#get_station_by_id)
* [Track & Trace](#track)	
* [Errors](#errors)	
	* [Error codes](#error_codes)	




<a name="auth"></a>

<a name="requirements"></a>
Requirements
------------

 * GUID from [SwipBox](http://www.swipbox.com).
 * PHP 5.3+

<a name="installation"></a>
Installation
------------

<a name="composer"></a>
### Composer

For installation with Composer (recommended), add the `repox/swipbox` package to your composer.json.

	{
	    "require": {
	        "repox/swipbox": "1.1.*"
	    }
	}

And enable autoloading by including `vendor/autoload.php`.

<a name="manual"></a>
### Manual installation

Download the latest [stable tag](https://github.com/Repox/swipbox-api-php/archive/1.1.2.zip) and unpack `src/` into a folder and include the files for use.

	<?php
	include 'Swipbox/Client.php';
	include 'Swipbox/Exception.php';

When you do a manual install, you will also need to download [Guzzle](http://guzzlephp.org).<br>
I recommend downloading the [Guzzle Phar](http://guzzlephp.org/guzzle.phar) and including it.

	include 'guzzle.phar';

<a name="gettingstarted"></a>
Getting started
---------------

When you have acquired the GUID for SwipBox account, you start by instantiating a SwipBox client.

	<?php
	use \Swipbox\Client;

	$swipbox = new Client('YOUR-GUID-GOES-HERE');

If you are testing your implemenation, you can add a second parameter with the boolean `true` to the constructor.

	<?php
	use \Swipbox\Client;

	$swipbox = new Client('YOUR-GUID-GOES-HERE', true);

<a name="parcels"></a>
Parcels
=======

<a name="create"></a>
Creating a parcel
-----------------

Using the `create` method we can quickly create a package, by passing an array of parameters as first argument.
The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| first_name | Indicates the first name of the customer | alphanumeric(100)
| address_1 | Indicates the address 1 of the customer | alphanumeric(100)
| city | Indicates the city of the customer | alphanumeric(100)
| zip | Indicates the zip code of the customer | alphanumeric(6)
| country | Indicates country of the customer | alphanumeric(100)
| email | Indicates the email address of the customer | alphanumeric(100)
| mobile_number | Indicates the mobile number of the customer | alphanumeric(8, 11 or 12) Allowed formats: 12345678, +4512345678, 004512345678
| parcel_size | Indicates the size of the parcel | 1 for small, 2 for medium or 3 for large parcel
| test_parcel | Indicates a test parcel | 1 (indicates a test parcel), 0 (indicates a real parcel)
| return_parcel | Indicates if a return parcel is required with the original parcel | 1 (indicates a return parcel is requried), 0 (indicates a return parcel is not requried)

The following parameters are OPTIONAL.

| Parameter | Description | Format
|-----------|-------------|-------
| webshop_order_id | Indicates the webshop order id | alphanumeric(100)
| address_2 | Indicates the address 2 of the customer | alphanumeric(100)
| last_name | Indicates the last name of the customer | alphanumeric(100)
| pick_up_id | Indicates the desired pick up point for the customer | integer(11)

#### Example

	try
	{

		$parcel = $swipbox->create( array(
			'first_name' => 'Niels',
			'last_name' => 'Nielsen',
			'address_1' => 'Fantastisk vej 12',
			'city' => 'København N',
			'zip' => 2200,
			'country' => 'DK',
			'email' => 'niels@example.com',
			'mobile_number' => '12345678',
			'parcel_size' => 1,
			'test_parcel' => 0,
			'return_parcel' => 0,
		));

	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}


`$parcel` now contains a multidimensional array of Parcel ID's and Parcel type ID's.

	echo $parcel['parcels'][0]['parcel_id']; // Integer identifying the Parcel
	echo $parcel['parcels'][0]['parcel_type']; // Integer identifying the Parcel type (1 for normal Parcel, 2 for return Parcel)

Provided you wanted to create a return parcel by passing `1` to the `return_parcel` parameter, you can retrieve the information in index `1` of the multidimensional array.

	// The normal parcel
	echo $parcel['parcels'][0]['parcel_id'];
	echo $parcel['parcels'][0]['parcel_type'];

	// The return parcel
	echo $parcel['parcels'][1]['parcel_id'];
	echo $parcel['parcels'][1]['parcel_type'];


<a name="activate"></a>
Activating a parcel
-------------------

A parcel needs to be activated within 7 days. Otherwise the parcel expires.

To activate the parcel, you can you the `activate` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| parcel_id | Indicates the id of the created parcel | numeric(20)
| format | Indicates the format of the response | 0 for PDF format, 1 for XML format, 2 for JSON format
| output | Indicated the output of the parcel label | 0 for HTTP, 1 for FTP (usage is unknow, just pass 0)

When activating a parcel, the recipient will recieve mail and text notifications.

#### Example

	try {

		$pdf_label = $swipbox->activate( array(
			'parcel_id' => 27181,
			'format' => 0,
			'output' => 0,
			
		));

	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}


`$pdf_label` will now contain binary data (as we passed `0` in the format which indicates a PDF format) which is the parcel label.<br>
You could view the label in browser for printing simply by echoing the data with the appriate headers

	header('Content-Type: application/pdf');
	echo $pdf_label;


<a name="get_label"></a>
Labels for activated parcels
----------------------------

It's useful to get the parcel data or label when you need them.

To get the parcel label or information, you can you the `get_label` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| parcel_id | Indicates the id of the created parcel | numeric(20)
| format | Indicates the format of the response | 0 for PDF format, 1 for XML format, 2 for JSON format
| output | Indicated the output of the parcel label | 0 for HTTP, 1 for FTP (usage is unknow, just pass 0)

When activating a parcel, the recipient will recieve mail and text notifications.

#### Example

	try {

		$pdf_label = $swipbox->get_label( array(
			'parcel_id' => 27181,
			'format' => 0,
			'output' => 0,
			
		));

	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}


`$pdf_label` will now contain binary data (as we passed `0` in the format which indicates a PDF format) which is the parcel label.<br>
You could view the label in browser for printing simply by echoing the data with the appriate headers

	header('Content-Type: application/pdf');
	echo $pdf_label;


<a name="cancel"></a>
Cancel parcel
-------------

Use this to cancel a parcel with the atatus 'Created' or 'Activated'.<br>
**NB: Currently, you cannot get the barcode needed for cancelling a parcel from a parcel that hasn't been activated yet!**


The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| barcode | Indicates the barcode of the parcel | alphanumeric(45)

The barcode can be retrieved by getting the label of a parcel (eg. with the JSON format).<br>
A complete example would look something similar to the following example

#### Example

	try {
		
		$json_label = $swipbox->get_label( array(
			'parcel_id' => 27181,
			'format' => 2,
			'output' => 0,
			
		));

		$result = $swipbox->cancel(array(
			'barcode' => $json_label['label']['barcode'],
		));
			
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

This should successfully cancel a parcel.

<a name="stations"></a>
Stations
========

Stations are pick up destinations for the parcel.<br>
When creating a parcel, you can provide a `pick_up_id` as an optional parameter, specifying the desired pickup destination for the customer.

The following methods are for locating stations.

<a name="find_nearest"></a>
Find nearest station
--------------------

To get stations that are nearest to a specific address, you can you the `find_nearest` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| address_1 | Indicates the address_1 | alphanumeric(100)
| city | Indicates the city | alphanumeric(100)
| zip | Indicates the zip code | alphanumeric(6)
| country | Indicates the country | alphanumeric(100)
| parcel_size | Indicates the size of the parcel | 1 for small, 2 for medium or 3 for large parcel

The following parameters are OPTIONAL.

| Parameter | Description | Format
|-----------|-------------|-------
| address_2 | Indicates the address_2 | alphanumeric(100)
| no_of_stations | Indicates the number of stations to find | integer (defaults to 3)

#### Example

	try {

		$result = $swipbox->find_nearest(array(
			'address_1' => 'Rosenvangs Alle 12',
			'city' => 'Aarhus',
			'zip' => '8000',
			'country' => 'DK',
			'parcel_size' => 1,
			'no_of_stations' => 5,
		));
			
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}


`$result` will now contain a multidimensional array with 5 stations sorted by the nearest to the address specified.

<a name="find_active_favorites"></a>
Find Active Favorites
---------------------

To get stations that are active favorite stations for a specific customer, you can you the `find_active_favorites` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| email | Indicates the email address for the customer | alphanumeric(100)


#### Example

	try {

		$result = $swipbox->find_active_favorites(array(
			'email' => 'niels@example.com',
		));

	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

`$result` will now contain a multidimensional array with 5 stations.

<a name="find_near_to_favorite"></a>
Find stations near to favorite
------------------------------

To get stations that are near the first active favorite station for a specific customer, you can you the `find_near_to_favorite` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| email | Indicates the email address for the customer | alphanumeric(100)

The following parameters are OPTIONAL.

| Parameter | Description | Format
|-----------|-------------|-------
| no_of_stations | Indicates the number of stations to find | integer (defaults to 3)


#### Example

	try {

		$result = $swipbox->find_near_to_favorite(array(
			'email' => 'niels@example.com',
		));

	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

`$result` will now contain a multidimensional array with 3 stations.

<a name="find_by_zip"></a>
Find stations by zip
--------------------

To get stations specific for a zip code, you can use the `find_by_zip` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| zip | Indicates the zip code | alphanumeric(6)


#### Example

	try {

		$result = $swipbox->find_by_zip(array(
			'zip' => '8000',
		));
	
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

`$result` will now contain a multidimensional array with all stations within the specified zip code.

<a name="get_station_by_id"></a>
Get station by its pick ip ID
-----------------------------

To get details for a specific station, you can use the `get_station_by_id` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| pick_up_id | Indicates the id of the pick up point | integer(11)


#### Example

	try {

		$result = $swipbox->get_station_by_id(array(
			'pick_up_id' => '1259',
		));
	
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

`$result` will now contain a multidimensional array with details on the station you specified by it pick up ID.

<a name="track"></a>
Tracking parcels
================

The track and trace function which can identify the events for a parcel.<br>
you can use the `track` method by passing an array of parameters as first argument.

The following parameters are REQUIRED.

| Parameter | Description | Format
|-----------|-------------|-------
| barcode | Indicates the barcode number of the parcel | alphanumeric(45)


#### Example

	try {

		$json_label = $swipbox->get_label( array(
			'parcel_id' => 27181,
			'format' => 2,
			'output' => 0.
		));	

		$track = $swipbox->track(array(
				'barcode' => $json_label['label']['barcode'],
		));
	
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

`$track` will now contain a multidimensional array with details alle events the parcel tracking has recorded.

<a name="errors"></a>
Errors - Handling exceptions
============================

By echoing the exception message you will get the message return from SwipBox.

#### Example

	try {

		$result = $swipbox->find_by_zip(array(
			'non_existing_parameter' => '8000',
		));
	
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getMessage();
	}

This will cause the SwipBox client to thrown an Exception - by handling it like in the example, the result will be echoing the error message (which in this case would be "Zip is missing").

<a name="error_codes"></a>
Error codes
-----------

It is also possible to handle the error code, as these are linked to the error message.<br>

#### Example

	try {

		$result = $swipbox->find_by_zip(array(
			'non_existing_parameter' => '8000',
		));
	
	}
	catch( \Swipbox\Exception $e)
	{
		echo $e->getCode();
	}


Same error as before, but you are getting the error code 14, which corresponds to the same message as before.

The following table show possible error codes and messages.

| Error code | Description
|------------|------------
| 1 | First name is missing
| 2 | Invalid first name
| 3 | Invalid last name
| 4 | Address 1 is missing
| 5 | No stations found
| 6 | Parcel activation failed
| 7 | Parcel creation failed
| 8 | Communication error
| 9 | No track & trace information found
| 10 | Invalid Address 1
| 11 | Invalid Address 2
| 12 | City is missing
| 13 | Invalid city
| 14 | Zip is missing
| 15 | Invalid zip
| 16 | Country is missing
| 17 | Invalid country
| 18 | Email is missing
| 19 | Invalid email
| 20 | Mobile number is missing
| 21 | Invalid mobile number
| 22 | Parcel size missing
| 23 | Invalid parcel size value
| 24 | Test parcel is missing
| 25 | Invalid test parcel value
| 26 | Return parcel is missing
| 27 | Invalid return parcel is missing
| 28 | Invalid webshop id
| 29 | Invalid pick up id
| 30 | Invalid parcel id
| 31 | Parcel ID is missing
| 32 | Invalid format value
| 33 | Parcel format is missing
| 34 | Invalid output value
| 35 | Parcel output missing
| 36 | Invalid barcode
| 37 | Barcode missing
| 38 | Invalid GUID
| 39 | Parcel cancellation failed
| 40 | File delivery failed
| 41 | No free pickup stations found
| 42 | Parcel is already activated
| 42 | Some problem occurred

