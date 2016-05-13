<?php
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
$fbAccount = urldecode($_SESSION['fbAccount']);

echo urldecode(urlencode('古振平')).'：';
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$connection = socket_connect($socket,'127.0.0.1', 1234); 

// send string to server
socket_write($socket, $fbAccount, strlen($fbAccount)) or die("Could not send data to server\n");
// get server response
if(socket_read ($socket, 1024) or die("Could not read server response\n") == true) {
	echo '已成功訂購';
}
else {
	echo '訂購失敗請聯絡管理員';
}
// close socket
socket_close($socket);