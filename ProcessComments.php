<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
$dataArray = $_POST['data'];

	$latestFBDate = new DateTime("0000-00-00 00:00:00");
	foreach($dataArray as $data)
	{
		$fbAccount = $data['fb帳號'];
		$fbId = $data['fbid'];
		$month = $data['月份'];
		$itemID = $data['itemid'];
		$itemName = $data['品項'];
		$spec = $data['規格'];
		$price = $data['單價'];
		$comment = $data['備註'];
		$amount = $data['數量'];
		
		$fbDate = new DateTime($data['時間']);
		
		if($latestFBDate < $fbDate)
		{
			$latestFBDate = $fbDate;
		}
		
		$sql = "INSERT INTO `ShippingRecord`(`FB帳號`, `品項`, `單價`, `數量`, `SerialNumber`, `FBID`, `備註`, `月份`, `規格`, `ItemID`) 
			VALUES (\"$fbAccount\", \"$itemName\", \"$price\", \"$amount\", NULL, \"$fbId\", \"$comment\", \"$month\", \"$spec\", \"$itemID\")";
	
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}			
	}
	
	$latestFBDateStr = $latestFBDate->format('Y-m-d H:i:s');
	
	$sql = "SELECT ItemID,品項,單價,規格,月份,SUM(數量) FROM `ShippingRecord` WHERE `ItemID` = \"$itemID\" group by 規格";
	
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
		
		$sql = "UPDATE `ItemCategory` SET `需求數量`=\"$amount\",`更新時間`=\"$latestFBDateStr\" WHERE `ItemID` = \"$itemID\" AND `規格`=\"$spec\"";		
		
		$insertResult = mysql_query($sql,$con);
		if (!$insertResult) {
			die('Invalid query: ' . mysql_error());
		}
	}
	echo "上傳完成";
?>