<?php
set_time_limit(3);
// create socket
$socket = socket_create(AF_UNIX, SOCK_STREAM, 0) or die("Could not create socket\n");
echo "Create socket\n";
// bind socket to port
$result = socket_bind($socket, './mysock') or die("Could not bind to socket\n");
echo "bind to socket\n";
// start listening for connections
$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
echo "set up socket listener\n";

// accept incoming connections
// spawn another socket to handle communication
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
socket_close($socket);
?>