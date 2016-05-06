<?php
set_time_limit(0);
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not createsocket\n");

$result = socket_bind($socket, '127.0.0.1', '1234') or die("Could not bind tosocket\n");

$result = socket_listen($socket, 3) or die("Could not set up socketlistener\n");


while (true) {
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
	echo " accept incoming connection\n";
	// read client input
	$input = socket_read($spawn, 1024) or die("Could not read input\n");
	// clean up input string
	$input = trim($input);
	echo "Client Message : ".$input;
	// reverse client input and send back
	$output = strrev($input) . "\n";
	socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
	// close sockets
	socket_close($spawn);
}
socket_close($socket);
?>