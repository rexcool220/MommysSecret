<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

$isOpen= $_POST['isOpen'];

$sql = "UPDATE `Setting` SET `isOpen`= $isOpen";

$result = mysql_query($sql,$con);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

echo "更新完成!!";
// echo $sql;
?>