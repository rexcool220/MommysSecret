<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
require_once '../ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
$target_dir = "/home/mommysse/public_html/uploads/";
$target_file = $target_dir . $_FILES["my-file-selector"]["name"];
$fileToBeUpload = $_POST['fileToBeUpload'];
if (file_exists($target_file)) { unlink ($target_file); }

$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["my-file-selector"]["tmp_name"]);
	if($check !== false) {
		echo "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		echo "File is not an image.";
		$uploadOk = 0;
		exit;
	}
}
// Check if file already exists
if (file_exists($target_file)) {
	echo "Sorry, file already exists.";
	$uploadOk = 0;
	exit;
}

if($_FILES["my-file-selector"]["tmp_name"] == "")
{
	echo "tmp_name is empty";
}

if ($uploadOk == 0) {
	echo "Sorry, your file was not uploaded.";
	exit;
	// if everything is ok, try to upload file
} else {
	if (move_uploaded_file($_FILES["my-file-selector"]["tmp_name"], $target_file)) {
		echo "The file ". $_FILES["my-file-selector"]["name"]. " has been uploaded.<br>";
		echo "=>";
		echo $target_file;
	} else {
		echo "error" . $_FILES["my-file-selector"]["tmp_name"] . "#" . $target_file;
		exit;
	}
}

	$month = $_POST["month"];
	$closeDate = $_POST["closeDate"];
	$itemName = $_POST["itemName"];
	$itemSpecCounts = intval($_POST["itemSpecCounts"]);
	$itemSpec = array();
	$itemPrice = array();
	$itemCost = array();
	$itemShopPrice = array();
	for($i = 0;$i < $itemSpecCounts;$i++)
	{
		array_push($itemSpec, $_POST["itemSpec".$i]);
		array_push($itemPrice, $_POST["itemPrice".$i]);
		array_push($itemCost, $_POST["itemCost".$i]);
		array_push($itemShopPrice, $_POST["itemShopPrice".$i]);
	}
	$itemComment = $_POST["itemComment"];
	$vendor = $_POST["vendor"];
	$specString = "";
	for($i = 0;$i < $itemSpecCounts;$i++)
	{
		$specString = $specString."[".$itemSpec[$i]."：".$itemPrice[$i]."元]";
	}
	

	
	for($i = 0;$i < $itemSpecCounts;$i++)
	{
		$sql = "INSERT INTO `ItemCategory`(`ItemID`, `品項`, `規格`, `價格`, `成本`, `批發價`, `月份`, `收單日期`, `廠商`, `Photo`) VALUES ('$itemID', '$itemName', '$itemSpec[$i]', '$itemPrice[$i]', '$itemCost[$i]', '$itemShopPrice[$i]', '$month', '$closeDate', '$vendor', '$fileToBeUpload')
		ON DUPLICATE KEY UPDATE `品項`=\"$itemName\", `價格`=\"$itemPrice[$i]\", `成本`=\"$itemCost[$i]\", `批發價`=\"$itemShopPrice[$i]\", `月份`=\"$month\", `收單日期`=\"$closeDate\", `廠商`=\"$vendor\", `Photo`=\"$fileToBeUpload\"";
		$insertResult = mysql_query($sql,$con);
		if (!$insertResult) {
			die('Invalid query: ' . mysql_error());
		}
	}
	echo "From Server".json_encode($_POST)."<br>";
?>