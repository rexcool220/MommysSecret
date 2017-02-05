<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

require_once '../ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

$loginFBAccount = $_POST['loginFBAccount'];
$customberFBAccount = $_POST['customberFBAccount'];
$customerFBID = $_POST['customerFBID'];
$amount = $_POST['amount'];
$comment = $_POST['comment'];

	$sql = "SELECT `Rebate` FROM `Members` WHERE `FBID` = '$customerFBID'";
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	$row = mysql_fetch_array($result);
	
	$currentRebate = intval($row['Rebate']);
	
	$updateRebated = $currentRebate + intval($amount);
	
	$sql = "UPDATE `Members` SET `Rebate`='$updateRebated' WHERE `FBID` = '$customerFBID'";
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$sql = "INSERT INTO `RebateRecord`(`登入帳號`, `顧客FB帳號`, `顧客FBID`, `修改金額`, `備註`, `修改日期`, `RebateNumber`, `目前回饋金`)
	VALUES ('$loginFBAccount','$customberFBAccount','$customerFBID','$amount','$comment',CURDATE(),NULL,'$updateRebated')";
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	
	echo "修改完成，請重新整理頁面!";
?>