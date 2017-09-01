<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

require_once '../ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

$dataArray = $_POST['data'];

$remitNumber = $dataArray[0];
$fbAccount = $dataArray[1];
$fbID = $dataArray[2];
$remitedAmount = $dataArray[3];
$ExpectedRemitAmount = $dataArray[4];
$lastFiveDitgit = $dataArray[5];
$remitedDate = $dataArray[6];
$memo = $dataArray[7];
$adminMemo = $dataArray[8];
$isChecked = $dataArray[9];

$isChecked = $isChecked == "否" ? 0 : 1;

$sql = "UPDATE `RemitRecord` SET `已收款`=\"$isChecked\",`管理員備註`=\"$adminMemo\" WHERE `匯款編號` = $remitNumber";

$result = mysql_query($sql,$con);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

$sql = "UPDATE `ShippingRecord` SET `確認收款` = \"$isChecked\"  WHERE 匯款編號 = $remitNumber AND (ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true)";

$result = mysql_query($sql,$con);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

echo "更新完成!!";
?>