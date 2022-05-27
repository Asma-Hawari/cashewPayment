<?php

require_once("inc/functions.php");

$api_key = "fb38ad1f9d93f70851279fa65a2d521d";
$shared_secret = "4162211c2f345dd9d61dce22dba2fe48";
$params = $_GET;
$hmac = $_GET['hmac'];

$params = array_diff_key($params, array('hmac' => ''));
ksort($params);

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

if (hash_equals($hmac, $computed_hmac)) {

	$query = array(
		"client_id" => $api_key,
		"client_secret" => $shared_secret,
		"code" => $params['code']
	);

	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$result = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($result, true);
	$access_token = $result['access_token'];
    echo $access_token;

} else {
	die('This request is NOT from Shopify!');
}