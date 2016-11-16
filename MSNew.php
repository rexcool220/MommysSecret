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

function renderForm($FBAccount, $FBID, $itemName, $price, $discount, $amount, $month, $active, $spec, $itemID, $error)

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
			'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
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
		($fbAccount == 'Queenie Tsan')||
		($fbAccount == '熊會買')||
		($fbAccount == '熊哉')||
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



<form action="" method=POST>

<div>

<strong>FB帳號: *</strong> <input type="text" name="FB帳號" value="<?php echo $FBAccount; ?>" /><br/>

<strong>FBID: *</strong> <input type="text" name="FBID" value="<?php echo $FBID; ?>" /><br/>

<strong>品項: *</strong> <input type="text" name="品項" value="<?php echo $itemName; ?>" /><br/>

<strong>單價: *</strong> <input type="text" name="單價" value="<?php echo $price; ?>" /><br/>

<strong>折扣: </strong> <input type="text" name="折扣" value="<?php echo $discount; ?>" /><br/>

<strong>數量: *</strong> <input type="text" name="數量" value="<?php echo $amount; ?>" /><br/>

<strong>月份: </strong> <input type="text" name="月份" value="<?php echo $month; ?>" /><br/>

<strong>Active: </strong> <input type="text" name="Active" value="<?php echo $active; ?>" /><br/>

<strong>規格: </strong> <input type="text" name="規格" value="<?php echo $spec; ?>" /><br/>

<strong>ItemID: </strong> <input type="text" name="ItemID" value="<?php echo $itemID; ?>" /><br/>

<p>* required</p>

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

$FBAccount = $_POST['FB帳號'];

$FBID = $_POST['FBID'];

$itemName = $_POST['品項'];

$price = $_POST['單價'];

$discount = $_POST['折扣'];

$amount = $_POST['數量'];

$month = $_POST['月份'];

$active = $_POST['Active'];

$spec = $_POST['規格'];

$itemID = $_POST['ItemID'];

// check to make sure both fields are entered

if ($FBAccount == '' || $FBID == '' || $itemName == '' || $price == '' || $amount == '')

{

// generate error message

$error = 'ERROR: Please fill in all required fields!';



// if either field is blank, display the form again

renderForm($FBAccount, $FBID, $itemName, $price, $discount, $amount, $month, $active, $spec, $itemID, $error);

}

else

{

// save the data to the database

mysql_query("INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `FBID`, `品項`, `單價`, `Discount`, `數量`, `月份`, `Active`, `規格`, `ItemID`) VALUES (NULL, '$FBAccount', '$FBID', '$itemName', '$price', '$discount', '$amount', '$month', '$active', '$spec', '$itemID')")
or die(mysql_error());

// once saved, redirect back to the view page

header("Location: MSView.php");

}

}

else

// if the form hasn't been submitted, display the form

{

renderForm('', '', '', '', '', '', '', '', '', '', '');

}

?>