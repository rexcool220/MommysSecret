<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$dataArray = $_POST['data'];
	
	$name = $dataArray[0];
	$fbAccount = $dataArray[1];
	$phoneNumber = $dataArray[2];
	$address = $dataArray[3];
	$familyNumber = $dataArray[4];
	$shippingWay = $dataArray[5];
	$shippingFee = $dataArray[6];
	$comment = $dataArray[7];
	$fbID = $dataArray[8];
	$rebate = $dataArray[9];
	$type = $dataArray[10];

 	//$sql = "UPDATE `Members` SET `姓名`='$name', `FB帳號`='$fbAccount', `手機號碼`='$phoneNumber', `郵遞區號＋地址`='$address',`全家店到店服務代號`='$familyNumber' ,`寄送方式`='$shippingWay', `運費`='$shippingFee', `備註`='$comment', `Rebate`='$rebate', `Type`='$type' WHERE `FBID`='$fbID'";
 	
 	$sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `手機號碼`, `郵遞區號＋地址`,`全家店到店服務代號` ,`寄送方式`, `運費`, `備註`, `Rebate`, `Type`, `FBID`)
 	VALUES (\"$name\", \"$fbAccount\", \"$phoneNumber\", \"$address\", \"$familyNumber\", \"$shippingWay\", \"$shippingFee\", \"$comment\", \"$rebate\", \"$type\", \"$fbID\")
 	ON DUPLICATE KEY UPDATE `姓名`='$name', `FB帳號`='$fbAccount', `手機號碼`='$phoneNumber', `郵遞區號＋地址`='$address',`全家店到店服務代號`='$familyNumber' ,`寄送方式`='$shippingWay', `運費`='$shippingFee', `備註`='$comment', `Rebate`='$rebate', `Type`='$type'";
 	
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	echo "更新完成!!";
?>