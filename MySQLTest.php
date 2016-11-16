<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");
	
	$sql = "SELECT distinct ItemID
		FROM  `ShippingRecord`
		WHERE  `ItemID` <>  ''
		AND  `ItemID` <>  '0'";

	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	while($row = mysql_fetch_array($result))
	{
		$itemID = $row['ItemID'];
		
		$sql = "SELECT ItemID, 品項, 單價, 規格, 月份, SUM(數量)
		FROM  `ShippingRecord`
		WHERE  `ItemID` = '$itemID'
		GROUP BY 規格";
		$Result1 = mysql_query($sql,$con);
		if (!$Result1) {
			die('Invalid query: ' . mysql_error());
		}
		
		while($row = mysql_fetch_array($Result1))
		{
			$itemID = $row['ItemID'];
			$itemName = $row['品項'];
			$price = $row['單價'];
			$spec = $row['規格'];
			$month = $row['月份'];
			$amount = $row['SUM(數量)'];
		
			$sql = "INSERT INTO `ItemCategory`(`ItemID`, `品項`, `單價`, `規格`, `月份`, `需求數量`) VALUES ('$itemID', '$itemName', '$price', '$spec', '$month', '$amount')";
			echo $sql;
			$insertResult = mysql_query($sql,$con);
			if (!$insertResult) {
				die('Invalid query: ' . mysql_error());
			}
		}
		
	}
	
	
	
	
?>