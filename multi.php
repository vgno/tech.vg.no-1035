<?php
header('Content-Type: text/plain');

$url  = 'http://' . $_SERVER['HTTP_HOST'];
$url .= dirname($_SERVER['REQUEST_URI']);
$url .= '/slow.php?sleep=';

$urls = array(
    $url . 250,
    $url . 750,
    $url . 1250,
    $url . 1750
);

function doRequests($urls) {
    $multi = curl_multi_init();
    $channels = array();

    // Loop through the URLs so request, create curl-handles,
    // attach the handle to our multi-request
    foreach ($urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_multi_add_handle($multi, $ch);

        $channels[$url] = $ch;
    }

    // While we're still active, execute curl
    $active = null;
    do {
        $mrc = curl_multi_exec($multi, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active && $mrc == CURLM_OK) {
        // Wait for activity on any curl-connection
        if (curl_multi_select($multi) == -1) {
            continue;
        }

        // Continue to exec until curl is ready to
        // give us more data
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }

    // Loop through the channels and retrieve the received
    // content, then remove the handle from the multi-handle
    $results = array();
    foreach ($channels as $url => $channel) {
        $results[$url] = curl_multi_getcontent($channel);
        curl_multi_remove_handle($multi, $channel);
    }

    // Close the multi-handle and return our results
    curl_multi_close($multi);
    return $results;
};

$start = microtime(true);
$results = doRequests($urls);

foreach ($results as $result) {
    echo $result;
}

echo 'Used a total of ' . (microtime(true) - $start) . ' seconds' . PHP_EOL;