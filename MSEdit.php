<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}
/*

EDIT.PHP

Allows user to edit specific entry in database

*/



// creates the edit record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($FBAccount, $itemName, $price , $amount, $remitDate, $shippingDate, $remitNumber, $isRemited, $serialNumber, $error)

{

	?>
	
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	
	<html>
	
	<head>
	
	<title>Edit Record</title>
	
	</head>
	
	<body>
	<?php
	
	
	if(!$accessToken)
	{
		$fb = new Facebook\Facebook([
				'app_id' => '1540605312908660',
				'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
				'default_graph_version' => 'v2.6',
		]);
	
		if(empty($accessToken)&&!empty($_SESSION['accessToken']))
		{
			$accessToken = $_SESSION['accessToken'];
		}
		else if(!empty($accessToken)&&!empty($_SESSION['accessToken']))
		{
			echo "accessToken error";
			exit;
		}
		$fb->setDefaultAccessToken($accessToken);
	}
	
	try {
		$response = $fb->get('/me');
		$userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$fbAccount = $userNode->getName();
	if(($fbAccount == 'Gill Fang')||
			($fbAccount == 'JoLyn Dai')||
			($fbAccount == '王雅琦')||
			($fbAccount == 'Queenie Tsan')||
			($fbAccount == '熊會買')||
			($fbAccount == '熊哉')||
			($fbAccount == '熊會算')||
			($fbAccount == '古振平'))
	{
		// 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}	
	
	// if there are any errors, display them
	
	if ($error != '')
	
	{
	
		echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
	
	}
	
	?>
	
	
	
	<form action="" method="post">
	
	<input type="hidden" name="SerialNumber" value="<?php echo $serialNumber; ?>"/>
	
	<div>
	
	<p><strong>SerialNumber:</strong> <?php echo $serialNumber; ?></p>
	
	<strong>FB帳號: *</strong> <input type="text" name="FB帳號" value="<?php echo $FBAccount; ?>" /><br/>
	
	<strong>品項: *</strong> <input type="text" name="品項" value="<?php echo $itemName; ?>" /><br/>
	
	<strong>單價: *</strong> <input type="text" name="單價" value="<?php echo $price; ?>" /><br/>
	
	<strong>數量: *</strong> <input type="text" name="數量" value="<?php echo $amount; ?>" /><br/>
	
	<strong>匯款日期: *</strong> <input type="text" name="匯款日期" value="<?php echo $remitDate; ?>" /><br/>
	
	<strong>出貨日期: *</strong> <input type="text" name="出貨日期" value="<?php echo $shippingDate; ?>" /><br/>
	
	<strong>匯款編號: *</strong> <input type="text" name="匯款編號" value="<?php echo $remitNumber; ?>" /><br/>
	
	<strong>確認收款: *</strong> <input type="text" name="確認收款" value="<?php echo $isRemited; ?>" /><br/>
	
	<strong>SerialNumber: *</strong> <input type="text" name="SerialNumber" value="<?php echo $serialNumber; ?>" /><br/>
	
	
	<input type="submit" name="submit" value="Submit">
	
	</div>
	
	</form>
	
	</body>
	
	</html>
	
	<?php

}

// check if the form has been submitted. If it has, process the form and save it to the database

if (isset($_POST['submit']))

{

	// get form data, making sure it is valid
	
	$FBAccount = $_POST['FB帳號'];
	
	$itemName = $_POST['品項'];
	
	$price = $_POST['單價'];
	
	$amount = $_POST['數量'];
	
	$remitDate = $_POST['匯款日期'];
	
	$shippingDate = $_POST['出貨日期'];
	
	$remitNumber = $_POST['匯款編號'];
	
	$isRemited = $_POST['確認收款'];
	
	$serialNumber = $_POST['SerialNumber'];

	// check that firstname/lastname fields are both filled in
	
	if ($FBAccount == '' || $itemName == '' || $price == '' || $amount == '' || $serialNumber == '')
	
	{
	
		// generate error message
		
		$error = 'ERROR: Please fill in all required fields!';
		
		
		
		//error, display form
		
		renderForm($FBAccount, $itemName, $price , $amount, $remitDate, $shippingDate, $remitNumber, $isRemited, $serialNumber, $error);
	
	}
	
	else
	
	{
	
		// save the data to the database
		
		
// 		$sql = "UPDATE `ShippingRecord` SET FB帳號='$FBAccount', 品項='$itemName', 單價='$price', 數量='$amount', 匯款日期='$remitDate', 出貨日期='$shippingDate', 匯款編號='$remitNumber', 確認收款='$isRemited' WHERE SerialNumber='$serialNumber'";
		
// 		echo $sql;
			
		mysql_query("UPDATE `ShippingRecord` SET FB帳號=\"$FBAccount\", 品項=\"$itemName\", 單價=\"$price\", 數量=\"$amount\", 匯款日期=\"$remitDate\", 出貨日期=\"$shippingDate\", 匯款編號=\"$remitNumber\", 確認收款=\"$isRemited\" WHERE SerialNumber=\"$serialNumber\"")
		
		or die(mysql_error());
		
		// once saved, redirect back to the view page
		
		header("Location: MSView.php");
	
	}

}

else

// if the form hasn't been submitted, get the data from the db and display the form

{



// get the 'SerialNumber' value from the URL (if it exists), making sure that it is valid (checing that it is numeric/larger than 0)

	if (isset($_GET['SerialNumber']))
	
	{
	
		// query db
		
		$serialNumber = $_GET['SerialNumber'];
		
		$result = mysql_query("SELECT * FROM ShippingRecord WHERE SerialNumber='$serialNumber'")
		
		or die(mysql_error());
		
		$row = mysql_fetch_array($result);
	
	
	
	// check that the 'SerialNumber' matches up with a row in the databse
	
		if($row)
		
		{
			
			// get data from db
			
			$FBAccount = $row['FB帳號'];
			
			$itemName = $row['品項'];
			
			$price = $row['單價'];
			
			$amount = $row['數量'];
			
			$remitDate = $row['匯款日期'];
				
			$shippingDate = $row['出貨日期'];
				
			$remitNumber = $row['匯款編號'];
				
			$isRemited = $row['確認收款'];
			
			// show form
			
			renderForm($FBAccount, $itemName, $price , $amount, $remitDate, $shippingDate, $remitNumber, $isRemited, $serialNumber, $error);
		
		}
		
		else
		
		// if no match, display result
		
		{
		
			echo "No results!";
		
		}
	
	}
	
	else
	
		// if the 'SerialNumber' in the URL isn't valid, or if there is no 'SerialNumber' value, display an error
	
	{
	
	echo 'Error!';
	
	}

}

?>