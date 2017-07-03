<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

$photo= $_POST['photo'];
$itemID= $_POST['itemID'];
$itemName= $_POST['itemName'];
$itemPrice= $_POST['itemPrice'];
$itemSpec= $_POST['itemSpec'];
$month= $_POST['month'];
$itemCost= $_POST['itemCost'];
$itemWholeSalePrice= $_POST['itemWholeSalePrice'];
$vendor= $_POST['vendor'];
$arriveDate= $_POST['arriveDate'];

$sql = "INSERT INTO `ItemCategory` (`ItemID`,`品項`,`價格`,`規格`,`月份`,`成本`,`批發價`,`廠商`,`到貨日期`,`Photo`,`Active`)
VALUES ( \"$itemID\", \"$itemName\", \"$itemPrice\", \"$itemSpec\", \"$month\", \"$itemCost\", \"$itemWholeSalePrice\", \"$vendor\", \"$arriveDate\", \"NotAvailable.png\", 0)
ON DUPLICATE KEY UPDATE `品項`=\"$itemName\", `價格`=\"$itemPrice\", `月份`=\"$month\", `成本`=\"$itemCost\", `批發價`=\"$itemWholeSalePrice\", `廠商`=\"$vendor\", `到貨日期`=\"$arriveDate\", `Photo`=\"$photo\", `Active`=0";

$result = mysql_query($sql,$con);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}
echo date("Y-m-d");
?>