<?php

$url = 'https://' . $_SERVER["SERVER_NAME"] . str_replace('/scripts/avgCreate', '/tomcat/psychomorph/averageImages2', $_SERVER['REQUEST_URI']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
$data = curl_exec($ch);
curl_close($ch);

header("Access-Control-Allow-Origin: *");
echo $data;

?>