<?php

const BASE_URL = 'https://839a-87-200-80-170.ngrok.io/rest/V1';

/**
 * @param $token
 * @param $shop
 * @param $api_endpoint
 * @param $query
 * @param $method
 * @param $request_headers
 * @return array|string
 */
function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {

	$url = "https://" . $shop . ".myshopify.com" . $api_endpoint;
	if (!is_null($query) && in_array($method, array('GET', 	'DELETE'))) $url = $url . "?" . http_build_query($query);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, TRUE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

	$request_headers[] = "";
	if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

	if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
		if (is_array($query)) $query = http_build_query($query);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
	}
    
	// Send request to Shopify and capture any errors
	$response = curl_exec($curl);
	$error_number = curl_errno($curl);
	$error_message = curl_error($curl);

	// Close cURL to be nice
	curl_close($curl);

	// Return an error is cURL has a problem
	if ($error_number) {
		return $error_message;
	} else {

		// No error, return Shopify's response by parsing out the body and the headers
		$response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

		// Convert headers into an array
		$headers = array();
		$header_data = explode("\n",$response[0]);
		$headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
		array_shift($header_data); // Remove status, we've already set it above
		foreach($header_data as $part) {
			$h = explode(":", $part);
			$headers[trim($h[0])] = trim($h[1]);
		}

		// Return headers and Shopify's response
		return array('headers' => $headers, 'response' => $response[1]);

	}
    
}

/**
 * @return mixed
 */
function generateAdminFromMagento(){
    $adminUrl=BASE_URL.'/integration/admin/token';
    $userName='asma';
    $password = 'asma123';
    $ch = curl_init();
    $data = array("username" => $userName, "password" => $password);
    $dataString = json_encode($data);
    $ch = curl_init($adminUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataString))
    );
    $token = curl_exec($ch);
    $token = json_decode($token);
    curl_close($ch);
    return $token;
}

/**
 * @return array|mixed
 */
function getProductsFromMagento(){
    $productUrl=BASE_URL.'/products?searchCriteria[page_size]=20';

    $ch = curl_init($productUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".generateAdminFromMagento()
        )
    );
    $productList = curl_exec($ch);
    $err      = curl_error($ch);
    $products = json_decode($productList);
    curl_close($ch);
    if ($err) {
        return [];
    }
    return $products;
}

/**
 * @param $sku
 * @return array|mixed
 */
function getQty($sku){
    $productUrl=BASE_URL.'/products/'.$sku;

    $ch = curl_init($productUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".generateAdminFromMagento()
        )
    );
    $productList = curl_exec($ch);
    $err      = curl_error($ch);
    $product = json_decode($productList["extension_attributes"]["stock_item"], true);
    curl_close($ch);
    if ($err) {
        return [];
    }
    return $product["qty"];
}

/**
 * @param $customAttributes
 * @return mixed|void
 */
function getDescription($customAttributes){
    foreach ($customAttributes as $attribute) {
        if($attribute["attribute_code"] == 'description') {
           return  $attribute["value"];
        }
    }
}