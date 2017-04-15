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
	
 	$sql = "INSERT INTO `ItemCategory` (`ItemID`,`品項`,`價格`,`規格`,`月份`,`需求數量`,`到貨數量`,`成本`,`批發價`,`廠商`,`到貨日期`,`Photo`,`Active`) 
 	VALUES (\"$itemID\", \"$itemName\", \"$price\", \"$spec\", \"$month\", \"$requireAmount\", \"$arriveAmount\", \"$cost\", \"$wholesalePrice\", \"$vendor\", \"$arriveDate\", \"NotAvailable.png\", \"$active\")
	ON DUPLICATE KEY UPDATE `品項`=\"$itemName\", `價格`=\"$price\", `月份`=\"$month\",`需求數量`=\"$requireAmount\" ,`到貨數量`=\"$arriveAmount\", `成本`=\"$cost\", `批發價`=\"$wholesalePrice\", `廠商`=\"$vendor\", `到貨日期`=CURDATE(), `Photo`=\"NotAvailable.png\", `Active`=\"$active\"";
 	
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	echo date("Y-m-d");
// 	echo $sql;
?>