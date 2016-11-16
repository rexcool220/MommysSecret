<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$dataArray = $_POST['data'];
	
	$fbAccount = $dataArray[0];
	$itemName = $dataArray[1];
	$price = $dataArray[2];
	$amount = $dataArray[3];
	$remitDate = $dataArray[4];
	$shippingDate = $dataArray[5];
	$serialNumber = $dataArray[6];
	$remitNumber = $dataArray[7];
	$isRemit = $dataArray[8];
	$fbID = $dataArray[9];
	$discount = $dataArray[10];
	$month = $dataArray[11];
	$active = $dataArray[12];
	$spec = $dataArray[13];
	$itemID = $dataArray[14];
	
	
 	$sql = "UPDATE `ShippingRecord` SET `FB帳號`='$fbAccount',`品項`='$itemName',`單價`='$price',`數量`='$amount',`匯款日期`='$remitDate',`出貨日期`='$shippingDate',`匯款編號`='$remitNumber',`確認收款`='$isRemit',`FBID`='$fbID',`Discount`='$discount',`月份`='$month',`Active`='$active',`規格`='$spec',`ItemID`='$itemID' WHERE `SerialNumber`='$serialNumber'";
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	echo "更新完成!!";
?>