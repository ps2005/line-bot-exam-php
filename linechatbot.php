<?php
require(“vendor/autoload.php”);
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;

require(“phpMQTT.php”);

//$mqtt = new phpMQTT(“	m13.cloudmqtt.com”, 1883, “phpMQTT Pub Example”); //เปลี่ยน www.yourmqttserver.com ไปที่ mqtt server ที่เราสมัครไว้นะครับ
$mqtt = new phpMQTT(“	m13.cloudmqtt.com”, 22427, “phpMQTT Pub Example”); //เปลี่ยน www.yourmqttserver.com ไปที่ mqtt server ที่เราสมัครไว้นะครับ

$token = “deQ0R28QTXVYrTOwfnh+BOF0FvGIxSHVG3k4fIe2cLld9WZVs0UvUqdk0ZEC54PSjxjQRthwmhRqbx9hwicXEDn8itwyyAlMkGmogPmYHJsL1N6jGou+oMrlMXikTzHKDU3c7F+gGN1+tAzbi6zK1AdB04t89/1O/w1cDnyilFU=”; //นำ token ที่มาจาก line developer account ของเรามาใส่ครับ

$httpClient = new CurlHTTPClient($token);
$bot = new LINEBot($httpClient, [‘channelSecret’ => $token]);
// webhook
$jsonStr = file_get_contents(‘php://input’);
$jsonObj = json_decode($jsonStr);
print_r($jsonStr);
foreach ($jsonObj->events as $event) {
if(‘message’ == $event->type){
// debug
//file_put_contents(“message.json”, json_encode($event));
$text = $event->message->text;

if (preg_match(“/สวัสดี/”, $text)) {
$text = “มีอะไรให้จ่าวิสรับใช้ครับ”;
}

if (preg_match(“/เปิดทีวี/”, $text)) {     //หากในแชตที่ส่งมามีคำว่า เปิดทีวี ก็ให้ส่ง mqtt ไปแจ้ง server เราครับ
if ($mqtt->connect()) {
$mqtt->publish(“/IOT”,”TV”); // ตัวอย่างคำสั่งเปิดทีวีที่จะส่งไปยัง mqtt server
$mqtt->close();
}
$text = “เปิดทีวีให้แล้วคร้าบบบบ”;
}
if (preg_match(“/ปิดทีวี/”, $text) and !preg_match(“/เปิดทีวี/”, $text)) {
if ($mqtt->connect()) {
$mqtt->publish(“/IOT”,”TV”);
$mqtt->close();
}
$text = “จ่าปิดทีวีให้แล้วนะครับ!!”;
}
$response = $bot->replyText($event->replyToken, $text); // ส่งคำ reply กลับไปยัง line application

}
}

?>
