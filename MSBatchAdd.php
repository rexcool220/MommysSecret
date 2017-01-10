<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$dataArray = $_POST['data'];
	
	foreach($dataArray as $data)
	{
		$fbAccount = $data['FB帳號'];
		$itemName = $data['品項'];
		$price = $data['單價'];
		$amount = $data['數量'];
		$remitDate = $data['匯款日期'];
		$shippingDate = $data['出貨日期'];
		$serialNumber = $data['序號'];
		$remitNumber = $data['匯款編號'];
		$isRemit = $data['確認收款'];
		$fbID = $data['FBID'];
		$comment = $data['備註'];
		$month = $data['月份'];
		$active = $data['Active'];
		$spec = $data['規格'];
		$itemID = $data['ItemID'];
		
		$sql = "INSERT INTO `ShippingRecord`(`FB帳號`, `品項`, `單價`, `數量`, `SerialNumber`, `FBID`, `備註`, `月份`, `規格`, `ItemID`)
		VALUES ('$fbAccount', '$itemName', '$price', '$amount', NULL, '$fbID', '$comment', '$month', '$spec', '$itemID')";
	
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	}
// 	echo $sql;
	echo "處理完成!!";
?>