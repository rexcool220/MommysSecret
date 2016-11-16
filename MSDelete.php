<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
		
	$dataArray = $_POST['data'];
	
	$serialNumber = $dataArray[6];

	$sql = "DELETE FROM `ShippingRecord` WHERE `SerialNumber` = $serialNumber";

	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	echo "已刪除 $serialNumber";
?>