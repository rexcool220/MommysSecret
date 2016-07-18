<?php
header("Content-Type:text/html; charset=utf-8");
$dbhost = 'localhost';
$dbuser = 'mommysse_Admin';
$dbpass = 'rakasa983';
$dbname = 'mommysse_MommysSecret';
$con = mysql_connect($dbhost, $dbuser, $dbpass) or die('Could not connect: ' . mysql_error());

$db_selected = mysql_select_db($dbname, $con) or die ("Can\'t use ".$dbname.": " . mysql_error());
mysql_query("SET NAMES utf8");