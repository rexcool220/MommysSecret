<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}
/*

NEW.PHP

Allows user to create a new entry in the database

*/



// creates the new record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($name, $FBAccount, $eMail , $phoneNumber, $address,  $familyNumber, $shippingWay, $shippingFee, $memo, $shippingAgent, $FBID, $error)

{

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>New Record</title>

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
$loginFBAccount = $userNode->getName();
if(($loginFBAccount == 'Gill Fang')||
		($loginFBAccount == 'JoLyn Dai')||
		($loginFBAccount == '王雅琦')||
		($loginFBAccount == 'Queenie Tsan')||
		($loginFBAccount == '熊會買')||
		($loginFBAccount == '熊哉')||
		($loginFBAccount == '熊會算')||
		($loginFBAccount == '古振平'))
{
	// 	echo "管理者 : $loginFBAccount";
}
else
{
	echo "$loginFBAccount : 你不是管理者";
	exit;
}	

// if there are any errors, display them

if ($error != '')

{

echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';

}

?>



<form action="" method=POST>

<div>
	
	<strong>姓名: *</strong> <input type="text" name="姓名" value="<?php echo $name; ?>" /><br/>
	
	<strong>FB帳號: *</strong> <input type="text" name="FB帳號" value="<?php echo $FBAccount; ?>" /><br/>
	
	<strong>FBID: *</strong> <input type="text" name="FBID" value="<?php echo $FBID; ?>" /><br/>
	
	<strong>E-Mail:</strong> <input type="text" name="E-Mail" value="<?php echo $eMail; ?>" /><br/>
	
	<strong>手機號碼:</strong> <input type="text" name="手機號碼" value="<?php echo $phoneNumber; ?>" /><br/>
	
	<strong>郵遞區號＋地址:</strong> <input type="text" name="郵遞區號＋地址" value="<?php echo $address; ?>" /><br/>
	
	<strong>全家店到店服務代號: *</strong> <input type="text" name="全家店到店服務代號" value="<?php echo $familyNumber; ?>" /><br/>
	
	<strong>寄送方式: *</strong> <input type="text" name="寄送方式" value="<?php echo $shippingWay; ?>" /><br/>
	
	<strong>運費: *</strong> <input type="text" name="運費" value="<?php echo $shippingFee; ?>" /><br/>
	
	<strong>備註:</strong> <input type="text" name="備註" value="<?php echo $memo; ?>" /><br/>
	
	<strong>合併寄送人帳號:</strong> <input type="text" name="合併寄送人帳號" value="<?php echo $shippingAgent; ?>" /><br/>
	
	<input type="submit" name="submit" value="Submit">

</div>

</form>

</body>

</html>

<?php

}

// check if the form has been submitted. If it has, start to process the form and save it to the database

if (isset($_POST['submit']))

{

	// get form data, making sure it is valid
	
	$name = $_POST['姓名'];
		
	$FBAccount = $_POST['FB帳號'];
	
	$FBID = $_POST['FBID'];
	
	$eMail = $_POST['E-Mail'];
	
	$phoneNumber = $_POST['手機號碼'];
	
	$address = $_POST['郵遞區號＋地址'];
	
	$familyNumber = $_POST['全家店到店服務代號'];
	
	$shippingWay = $_POST['寄送方式'];
	
	$shippingFee = $_POST['運費'];
	
	$memo = $_POST['備註'];
	
	$shippingAgent = $_POST['合併寄送人帳號'];

	// check that firstname/lastname fields are both filled in
	
	if ($name == '' || $FBAccount == '' || $familyNumber == '' || $shippingWay == '' || $shippingFee == '')
	
	{

	// generate error message
	
	$error = 'ERROR: Please fill in all required fields!';

	// if either field is blank, display the form again
	
	renderForm($name, $FBAccount, $eMail , $phoneNumber, $address, $familyNumber, $shippingWay, $shippingFee, $memo, $shippingAgent, $FBID, $error);
	}

else

{

// save the data to the database

// $sql = "INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `品項`, `單價`, `數量`) VALUES ('$serialNumber', '$FBAccount', '$itemName', '$price', '$amount')"; , $FBID

// echo $sql;

mysql_query("INSERT INTO `Members` (`姓名`, `FB帳號`, `E-Mail`, `手機號碼`, `郵遞區號＋地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`, `合併寄送人帳號`, `FBID`) VALUES ('$name', '$FBAccount', '$eMail', '$phoneNumber', '$address', '$familyNumber', '$shippingWay','$shippingFee', '$memo', '$shippingAgent', '$FBID')")

or die(mysql_error());

// once saved, redirect back to the view page

header("Location: MemberView.php");

}

}

else

// if the form hasn't been submitted, display the form

{

renderForm('','','');

}

?>