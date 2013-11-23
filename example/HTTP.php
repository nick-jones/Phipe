<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Phipe\Watcher;
use \Phipe\Connection\Connection;

$watcher = new Watcher();

$watcher->on('connect', function(Connection $connection) {
	echo 'Connected. Writing data..' . PHP_EOL;

	$data = "GET / HTTP/1.0\r\n".
		"Host: www.example.com\r\n".
		"Connection: Close\r\n\r\n";

	$connection->write($data);

	echo $data . '----------' . PHP_EOL;
});

$watcher->on('read', function(Connection $connection) {
	echo 'Data read..' . PHP_EOL;

	echo $connection->read();
});

$watcher->on('eof', function(Connection $connection) {
	echo PHP_EOL;
	echo 'EOF' . PHP_EOL;
});

$watcher->on('disconnect', function(Connection $connection) {
	echo PHP_EOL;
	echo 'Disconnected' . PHP_EOL;
});

$phipe = new \Phipe\Application([
	'connections' => [
		['host' => '93.184.216.119', 'port' => 80]
	],
	'observers' => [
		$watcher
	],
	'factory' => new \Phipe\Connection\Event\EventFactory(),
	'reconnect' => FALSE
]);

$phipe->execute();