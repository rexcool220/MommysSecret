<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
$itemID = $_POST['itemID'];


	$sql = "DELETE FROM `ShippingRecord` WHERE `ItemID` = $itemID";

	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$sql = "UPDATE `ItemCategory` SET `需求數量`= 0, `Active` = '0' WHERE `ItemID` = $itemID";
	
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	echo "已刪除 $itemID";
?>