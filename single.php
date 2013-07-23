<?php
header('Content-Type: text/plain');

function doRequest($seconds) {
    $url  = 'http://' . $_SERVER['HTTP_HOST'];
    $url .= dirname($_SERVER['REQUEST_URI']);
    $url .= '/slow.php?sleep=';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . $seconds);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
};

$start = microtime(true);
echo doRequest(250);
echo doRequest(750);
echo doRequest(1250);
echo doRequest(1750);

echo 'Used a total of ' . (microtime(true) - $start) . ' seconds' . PHP_EOL;