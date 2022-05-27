<?php

$shop = $_GET['shop'];
$api_key = "fb38ad1f9d93f70851279fa65a2d521d";
$scopes = "read_orders,write_products";
$redirect_uri = "https://839a-87-200-80-170.ngrok.io/shopify/generate_token.php";


$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
die();