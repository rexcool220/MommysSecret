<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}

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



// check if the 'id' variable is set in URL, and check that it is valid

if (isset($_GET['SerialNumber']))

{

// get id value

$serialNumber = $_GET['SerialNumber'];



// delete the entry

$result = mysql_query("DELETE FROM ShippingRecord WHERE SerialNumber='$serialNumber'")

or die(mysql_error());



// redirect back to the view page

header("Location: MSView.php");

}

else

// if id isn't set, or isn't valid, redirect back to view page

{

header("Location: MSView.php");

}



?>