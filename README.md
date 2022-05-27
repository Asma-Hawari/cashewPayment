## cashewPayment
Create a Shopify App Which Will read Products from Magento 2 Catalog an d sync in Shopify 

### This Task needs to be achieved using the Magento 2.x REST API

Products  must synch every 1 Hour 

All Product data must sync such as :

1- Price
2- Name
3- Description 
4- Inventory 
5- Product Configuration 

### The Solution : 

this repository has three scripts 
1- install.php ( responsible for installing the app on a developemnt store)
2- generate_token ( this is the script for generating the Access token so we can use it in the app to run the admin apis)
3- Index.php ( this is the main script will run when the app is installed)

There are two APIs one for getting all the products in Shopify and the other one is for getting all the products in Magento 
The MAP between the products is done by SKU
To run this scrip every hour , a scheduled cron job must be added in the server to run the app every one hour

