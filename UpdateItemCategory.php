<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$dataArray = $_POST['data'];
	
	$itemID = $dataArray[1];
	$itemName = $dataArray[2];
	$price = $dataArray[3];
	$spec = $dataArray[4];
	$month = $dataArray[5];
	$requireAmount = $dataArray[6];
	$arriveAmount = $dataArray[7];
	$cost = $dataArray[8];
	$wholesalePrice = $dataArray[9];
	$vendor = $dataArray[10];
	$arriveDate = $dataArray[11];
	$active = $dataArray[12];
	
 	$sql = "UPDATE `ItemCategory` SET `品項`= '$itemName', `單價`= '$price', `月份`= '$month', `需求數量` = '$requireAmount', `到貨數量` = '$arriveAmount', `成本` = '$cost', `批發價` = '$wholesalePrice', `廠商` = '$vendor', `到貨日期` = CURDATE(), `Active` = '$active' WHERE `ItemID` = '$itemID' AND `規格` like '$spec%'";
		
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	echo date("Y-m-d");
// 	echo $active;
?>