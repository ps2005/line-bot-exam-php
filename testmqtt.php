<?php
require('vendor/autoload.php');
require("phpMQTT.php");

$server = "m13.cloudmqtt.com";     // change if necessary
$port = 12427;                     // change if necessary
$username = "view";                   // set your username
$password = "1234";                   // set your password
$client_id = "phpMQTT-subscriber"; // make sure this is unique for connecting to sever - you could use uniqid()

$topic="/IOT";
$message = "Hello CloudMQTT!";

$mqtt = new phpMQTT($server, $port, $client_id);
if ($mqtt->connect(true, NULL, $username, $password)) {
    $mqtt->publish($topic, $message, 0);
    echo "Published message: " . $message;
    $mqtt->close();
}else{
    echo "Fail or time out<br />";
}
