<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
$dataArray = $_POST['data'];

	foreach($dataArray as $data)
	{
		$time = $data['時間'];
		$fbAccount = $data['fb帳號'];
		$fbId = $data['fbid'];
		$month = $data['月份'];
		$itemID = $data['itemid'];
		$itemName = $data['品項'];
		$spec = $data['規格'];
		$price = $data['單價'];
		$discount = $data['折扣'];
		$amount = $data['數量'];
		$sql = "INSERT INTO `ShippingRecord`(`FB帳號`, `品項`, `單價`, `數量`, `SerialNumber`, `FBID`, `Discount`, `月份`, `規格`, `ItemID`) 
			VALUES ('$fbAccount', '$itemName', '$price', '$amount', NULL, '$fbId', '$discount', '$month', '$spec', '$itemID')";
	
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}			
	}
	
	$sql = "SELECT ItemID,品項,單價,規格,月份,SUM(數量) FROM `ShippingRecord` WHERE `ItemID` = '$itemID' group by 規格";
	
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	while($row = mysql_fetch_array($result))
	{
		$itemID = $row['ItemID'];
		$itemName = $row['品項'];
		$price = $row['單價'];
		$spec = $row['規格'];
		$month = $row['月份'];
		$amount = $row['SUM(數量)'];
		
		$sql = "INSERT INTO `ItemCategory`(`ItemID`, `品項`, `單價`, `規格`, `月份`, `需求數量`) VALUES ('$itemID', '$itemName', '$price', '$spec', '$month', '$amount')";
		$insertResult = mysql_query($sql,$con);
		if (!$insertResult) {
			die('Invalid query: ' . mysql_error());
		}
	}
	echo "上傳完成";
?>