<?php

if (count($argv) <= 1) {
    exit('Usage: php '.$argv[0].' 1518169637');
}

$timestamp = $argv[1];

if (strlen($timestamp) > 10) {
	$timestamp = substr($timestamp, 0, 10);
}

date_default_timezone_set('Asia/Shanghai');
echo date('Y-m-d H:i:s', $timestamp) . PHP_EOL;
