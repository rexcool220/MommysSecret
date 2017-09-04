<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
require_once '../ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}

$target_dir = "/home/mommysse/public_html/MS/uploads/";
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
if ($uploadOk == 0) {
	echo "Sorry, your file was not uploaded.";
	exit;
	// if everything is ok, try to upload file
} else {
	if (move_uploaded_file($_FILES["my-file-selector"]["tmp_name"], $target_file)) {
		echo "http://mommyssecret.tw/MS/uploads/" . $_FILES["my-file-selector"]["name"];
	} else {
		echo "error";
		exit;
	}
}
?>