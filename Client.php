<?php
$message = "Hello Server";
echo "Message To server :".$message;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$connection = socket_connect($socket,'127.0.0.1', 1234); 

// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server  :".$result;
// close socket
socket_close($socket);