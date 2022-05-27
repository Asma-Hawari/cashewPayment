<?php

require_once("inc/functions.php");

const SHOP = "cashewpayment";
const TOKEN = "SWplI7gKAckAlF9QfAvv9yrI3grYsSkw";
$query = array(
	"Content-type" => "application/json"
);

$shopifyProducts = shopify_call(TOKEN, SHOP, "/admin/products.json", array(), 'GET');

$shopifyProducts = json_decode($shopifyProducts['response'], TRUE);

$magentoProducts = getProductsFromMagento();

$magentoProducts = json_decode($magentoProducts['response']['items'], TRUE);
//$product_id = $shopifyProducts['products'][0]['id'];

foreach ($shopifyProducts as $shopifyProduct) {
    foreach ($magentoProducts as $magentoProduct) {
        if ($shopifyProduct['sku'] == $magentoProduct['sku']) {
            $this->updateShopifyProduct($magentoProduct, $magentoProduct);
        }
    }
}

function updateShopifyProduct($shopifyProduct, $magentoProduct){
    $productId = $shopifyProduct['id'];
    //Shopify Status
    //active: The product is ready to sell and is available to customers on the online store, sales channels, and apps. By default, existing products are set to active.
    //archived: The product is no longer being sold and isn't available to customers on sales channels and apps.
    //draft: The product isn't ready to sell and is unavailable to customers on sales channels and apps. By default, duplicated and unarchived products are set to draft.
    //Magento Status Enabled , Disabled => 1 or 2
    $status = 'draft';
    if ($magentoProduct['status'] == 1 ) {
        $status = 'active';
    }
    else{
        $status = 'archived';
    }

    $modify_data = array(
        "product" => array(
            "id" => $productId,
            "title" => $magentoProduct['name'],
            "metafields_global_title_tag" => $magentoProduct['name'],
            "metafields_global_description_tag" => getDescription($magentoProduct["custom_attributes"]),
            "status"=> $status,
            "variants" => [[
                "id" => $productId,
                "price" => $magentoProduct['price'],
                "sku" => "Updating the Product SKU",
                "inventory_quantity" =>  getQty($magentoProduct['sku'])
            ],])
    );

    $modified_product = shopify_call(TOKEN, SHOP, "/admin/products/" . $productId . ".json", $modify_data, 'PUT');

    $modified_product_response = $modified_product['response'];
    if($modified_product_response){
        return true;
    }
}