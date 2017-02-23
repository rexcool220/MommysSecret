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

//$sql = "UPDATE `Members` SET `姓名`='$name', `FB帳號`='$fbAccount', `手機號碼`='$phoneNumber', `郵遞區號＋地址`='$address',`全家店到店服務代號`='$familyNumber' ,`寄送方式`='$shippingWay', `運費`='$shippingFee', `備註`='$comment', `Rebate`='$rebate', `Type`='$type' WHERE `FBID`='$fbID'";

$sql = "UPDATE `RemitRecord` SET `已收款`='$isChecked',`管理員備註`='$adminMemo' WHERE `匯款編號` = $remitNumber";

$result = mysql_query($sql,$con);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}
echo "更新完成!!";
?>