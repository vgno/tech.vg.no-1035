<?php
require __DIR__ . '/vendor/autoload.php';

use Guzzle\Http\Client,
    Guzzle\Common\Exception\MultiTransferException;

header('Content-Type: text/plain');

$host  = 'http://' . $_SERVER['HTTP_HOST'];
$path  = dirname($_SERVER['REQUEST_URI']);
$path .= '/slow.php?sleep=';

$client = new Client($host);

try {
    $start = microtime(true);
    $responses = $client->send(array(
        $client->get($path . 250),
        $client->get($path . 750),
        $client->get($path . 1250),
        $client->get($path . 1750),
    ));

    foreach ($responses as $response) {
        echo $response->getBody();
    }

    echo 'Used a total of ' . (microtime(true) - $start) . ' seconds' . PHP_EOL;
} catch (MultiTransferException $e) {
    echo 'The following exceptions were encountered:' . PHP_EOL;
    foreach ($e as $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}