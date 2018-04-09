<?php // callback.php

require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');
require("phpMQTT.php");

$server = "m13.cloudmqtt.com";     // change if necessary
$port = 12427;                     // change if necessary
$username = "view";                   // set your username
$password = "1234";                   // set your password
$client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()
$topic="/IOT";
$message = "Hello CloudMQTT2!";

/*
$mqtt = new bluerhinos\phpMQTT($server, $port, "ClientID".rand());
if ($mqtt->connect(true, NULL, $username, $password)) {
    $mqtt->publish($topic, $message, 0);
    echo "Published message: " . $message;
    $mqtt->close();
*/

$mqtt = new bluerhinos\phpMQTT($server, $port, "ClientID".rand());
$access_token = 'deQ0R28QTXVYrTOwfnh+BOF0FvGIxSHVG3k4fIe2cLld9WZVs0UvUqdk0ZEC54PSjxjQRthwmhRqbx9hwicXEDn8itwyyAlMkGmogPmYHJsL1N6jGou+oMrlMXikTzHKDU3c7F+gGN1+tAzbi6zK1AdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			$text = $event['message']['text'];			
			if (preg_match("/hello/", $text)) {
				$text = "มีอะไรให้จ่าวิสรับใช้ครับ";
			} else {
				$text = "ผิดคำสั่งครับ";
			}
			
			if (preg_match("/เปิดทีวี/", $text)) {     //หากในแชตที่ส่งมามีคำว่า เปิดทีวี ก็ให้ส่ง mqtt ไปแจ้ง server เราครับ
				if ($mqtt->connect(true, NULL, $username, $password)) {
				    $mqtt->publish($topic, "LEDON", 0);
				    //echo "Published message: " . $message;
				    $mqtt->close();
				}				
				$text = "เปิดทีวีให้แล้วคร้าบบบบ";
			}
			
			if (preg_match("/ปิดทีวี/", $text)) {     //หากในแชตที่ส่งมามีคำว่า เปิดทีวี ก็ให้ส่ง mqtt ไปแจ้ง server เราครับ
				if ($mqtt->connect(true, NULL, $username, $password)) {
				    $mqtt->publish($topic, "LEDOFF", 0);
				    //echo "Published message: " . $message;
				    $mqtt->close();
				}
				$text = "ปิดทีวีให้แล้วคร้าบบบบ";
			}			
			// Get text sent
			//$text = $event['source']['userId'];
			
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
	}
}
echo "OK";
