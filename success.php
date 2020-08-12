<?php

// Note this line needs to change if you don't use Composer:
// require('square-php-sdk/autoload.php');
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Square\Models\CreateOrderRequest;
use Square\Models\CreateCheckoutRequest;
use Square\Models\BatchRetrieveOrdersRequest;
use Square\Models\Order;
use Square\Models\OrderLineItem;
use Square\Models\Money;
use Square\Exceptions\ApiException;
use Square\SquareClient;

// dotenv is used to read from the '.env' file created
$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

// Pulled from the .env file and upper cased e.g. SANDBOX, PRODUCTION.
$upper_case_environment = strtoupper(getenv('ENVIRONMENT'));

// Use the environment and the key name to get the appropriate values from the .env file.
$access_token = getenv($upper_case_environment.'_ACCESS_TOKEN');    
$location_id =  getenv($upper_case_environment.'_LOCATION_ID');

// Initialize the authorization for Square
$client = new SquareClient([
  'accessToken' => $access_token,
  'environment' => getenv('ENVIRONMENT')
]);

$transactionId = $_GET['transactionId'];
echo $transactionId ;

$api_response = $client->getTransactionsApi()->retrieveTransaction($location_id, $transactionId);

$order_id = [];

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();

    $data = json_encode($result);
    $da = json_decode($data);
    foreach ($da as $key => $value) {
        /*echo 'transaction id = '.$value->id."<br>";
        echo 'location id = '.$value->location_id."<br>";
        echo 'created_at = '.$value->created_at."<br>";
        echo 'order_id = '.$value->order_id."<br>";
        echo 'tenders id = '.$value->tenders[0]->id."<br>";
        echo 'customer id = '.$value->tenders[0]->customer_id."<br>";
        echo 'note = '.$value->tenders[0]->note."<br>";
        echo 'amount_money = '.$value->tenders[0]->amount_money->amount."<br>";
        echo 'currency = '.$value->tenders[0]->amount_money->currency."<br>";
        echo 'card_details = '.$value->tenders[0]->card_details->status."<br>";
        echo 'card_brand = '.$value->tenders[0]->card_details->card->card_brand."<br>";
        echo 'card last_4 = '.$value->tenders[0]->card_details->card->last_4."<br>";*/
        //echo $value->tenders[0]->customer_id;

        $order_id = $value->order_id;

        echo "<pre>";    
        print_r($value);
        echo "<pre>";
    }

    /*echo "<pre>";    
    print_r($result);
    echo "<pre>";*/

} else {
    $errors = $api_response->getErrors();
    echo "<pre>";
    print_r($errors);
    echo "<pre>";
}

// get customer details
$customer_id = 'SZSTHQG2M935H0TG8JACNBV514';
$api_response = $client->getCustomersApi()->retrieveCustomer($customer_id);

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();

    $data = json_encode($result);
    $da = json_decode($data);
    echo 'customer id = '.$da->customer->id."<br>";
    echo 'created_at = '.$da->customer->created_at."<br>";
    echo 'updated_at   = '.$da->customer->updated_at."<br>";
    echo 'email_address = '.$da->customer->email_address."<br>";
    

} else {
    $errors = $api_response->getErrors();
    echo "<pre>";
    print_r($errors);
    echo "<pre>";
}

//get orders details
$order_id = [$order_id];
$body = new BatchRetrieveOrdersRequest($order_id);

$api_response = $client->getOrdersApi()->batchRetrieveOrders('LWRJQF9NK0V74', $body);

if ($api_response->isSuccess()) {
    $result = $api_response->getResult();

    $data = json_encode($result);
    $da = json_decode($data);
    
    foreach ($da as $key => $value) {
        foreach ($value as $ke => $val) {
            echo "<pre>";
            print_r($val);
            echo "<pre>";
            echo 'order id = '.$val->id."<br>";
            echo 'Name = '.$val->line_items[0]->name."<br>";
            echo 'quantity = '.$val->line_items[0]->quantity."<br>";
            echo 'total_money = '.$val->line_items[0]->total_money->amount."<br>";
            echo 'currency = '.$val->line_items[0]->total_money->currency."<br>";
        }
    }

} else {
    $errors = $api_response->getErrors();
    echo "<pre>";
    print_r($errors);
    echo "<pre>";
}


?>
