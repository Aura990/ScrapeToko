<?php
require 'vendor/autoload.php';
$client = new \GuzzleHttp\Client();
$url = 'https://www.tokopedia.com/ayafrozen/product?q=&sort=23';
$response = $client->get($url, [
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    ]
]);
$html = (string)$response->getBody();
echo 'Length: ' . strlen($html) . PHP_EOL;
echo 'Has product-image: ' . (strpos($html, 'product-image') !== false ? 'Yes' : 'No') . PHP_EOL;
