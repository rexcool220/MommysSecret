<?php
$message = "Hello Server";
echo "Message To server :".$message;
// create socket
$socket = socket_create(AF_UNIX, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
try {
	$result = socket_connect($socket, 'http://localhost/MommysSecret/mysock') or die("Could not connect to server\n");	
} catch (Exception $e) {
	echo $e->getMessage();
}

// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server  :".$result;
// close socket
socket_close($socket);