<?php
$ms    = (int) isset($_GET['sleep']) ? $_GET['sleep'] : 1000;
$start = microtime(true);

usleep($ms * 1000);

$end = microtime(true);

echo 'I started at ' . $start . ' and ended at ' . $end . ' (zZz: ' . ($end - $start) . ' seconds)' . PHP_EOL;
