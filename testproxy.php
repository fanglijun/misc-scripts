<?php

define('DEFAULT_PORT', 3128);

if (count($argv) == 3) {
	$ip = $argv[1];
	$port = $argv[2];
} else if (count($argv) == 2) {
	$parts = explode(':', $argv[1]);
	if (count($parts) == 2) {
		$ip = $parts[0];
		$port = $parts[1];
	} else {
		$ip = $parts[0];
		$port = DEFAULT_PORT;
	}
}
if (empty($ip)) {
	exit('Usage: php '.$argv[0].' ip:port  or  php '.$argv[0].' ip[ port]');
}

$options = [
	'http' => [
		'proxy' => 'tcp://'.$ip.':'.$port,
		'request_fulluri' => true,
		'timeout' => 5
	],
];
$context = stream_context_create($options);

$response = file_get_contents('http://myip.ipip.net', false, $context);
echo $response;
