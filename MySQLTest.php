<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';


header("Content-Type:text/html; charset=utf-8");


	$sql = "SELECT * FROM `ShippingRecord` WHERE Itemid <> 0 AND Active = 0";

	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	while($row = mysql_fetch_array($result))
	{
		$itemID = $row['ItemID'];
		$serailNumber = $row['SerialNumber'];
		$sql1 = "SELECT * FROM  `ItemCategory` WHERE  `ItemID` =  '$itemID'";
		$Result1 = mysql_query($sql,$con);
		if (!$Result1) {
			die('Invalid query: ' . mysql_error());
		}
		
		$row1 = mysql_fetch_array($Result1);
		
		if($row1['到貨數量'] == 0)
		{
			echo $itemID;
			echo "<br>";
			continue;
		}
		else 
		{
			$sql1 = "UPDATE `ShippingRecord` SET `Active` = 1 WHERE SerialNumber = $serailNumber";
			echo $sql1;
			echo "<br>";
		}
		
		
	}
	
	
	
	
?>