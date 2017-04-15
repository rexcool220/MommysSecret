<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
		
	$dataArray = $_POST['data'];
	
	$itemID = $dataArray[1];
	$spec = $dataArray[4];

	$sql = "DELETE FROM `ItemCategory` WHERE `ItemID` = \"$itemID\" AND `規格` = \"$spec\" ";

	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	echo "已刪除 ItemID = $itemID,規格 = $spec";
?>