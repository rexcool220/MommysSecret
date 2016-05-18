<?php
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
$fbAccount = urldecode($_SESSION['fbAccount']);
$fieldID = $_SESSION['fieldID'];
$facebookID = $_SESSION['facebookID'];
$groupID = $_SESSION['groupID'];
$spreadsheetCount = $_SESSION['spreadsheetCount']; 
$message = 'OrderFromClient,'.$fbAccount.','.$fieldID.','.$facebookID.','.$spreadsheetCount;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$connection = socket_connect($socket,'127.0.0.1', 1234); 

// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
if(socket_read ($socket, 1024) or die("Could not read server response\n") == true) {
	echo '已成功訂購';
	header("location: ".'https://www.facebook.com/groups/'.$groupID.'/permalink/'.$facebookID.'/');
}
else {
	echo '訂購失敗請聯絡管理員';
}
// close socket
socket_close($socket);