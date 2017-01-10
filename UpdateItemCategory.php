<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$dataArray = $_POST['data'];
	
	$itemID = $dataArray[0];
	$itemName = $dataArray[1];
	$price = $dataArray[2];
	$spec = $dataArray[3];
	$month = $dataArray[4];
	$requireAmount = $dataArray[5];
	$arriveAmount = $dataArray[6];
	$cost = $dataArray[7];
	$wholesalePrice = $dataArray[8];
	$vendor = $dataArray[9];
	$arriveDate = $dataArray[10];
	
 	$sql = "UPDATE `ItemCategory` SET `品項`= '$itemName', `單價`= '$price', `月份`= '$month', `需求數量` = '$requireAmount', `到貨數量` = '$arriveAmount', `成本` = '$cost', `批發價` = '$wholesalePrice', `廠商` = '$vendor', `到貨日期` = CURDATE() WHERE `ItemID` = '$itemID' AND `規格` like '$spec%'";
		
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	echo date("Y-m-d");
// 	echo $sql;
?>