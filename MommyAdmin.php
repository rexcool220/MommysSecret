<?php 
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<link rel="stylesheet" type="text/css" href="Admin.css?20160816">

<title>MommysSecret</title>

</head>

<body>

<?php

if(!isset($_GET['code']))
{
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '1540605312908660',
	  'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email']; // optional
	//$permissions = ['email','publish_actions','user_managed_groups']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MommyAdmin.php', $permissions);
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
}
else 
{
	if(!$accessToken)
	{
		$fb = new Facebook\Facebook([
				'app_id' => '1540605312908660',
				'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
				'default_graph_version' => 'v2.6',
		]);
		$helper = $fb->getRedirectLoginHelper();
		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		
		if(empty($accessToken)&&!empty($_SESSION['accessToken']))
		{
			$accessToken = $_SESSION['accessToken'];
		}
		else if(!empty($accessToken))
		{
			$_SESSION['accessToken'] = $accessToken;
		}
		else if(!empty($accessToken)&&!empty($_SESSION['accessToken']))
		{
			echo "accessToken error";
			exit;
		}
		$fb->setDefaultAccessToken($accessToken);
	}
	
	?>
		<script>
			window.history.replaceState( {} , 'MommysAdmin', 'http://mommyssecret.tw/MommyAdmin.php' );
		</script>
	<?php	
	
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
// 	 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}	

	
	$AdminTable = "
		<table id=\"AdminTable\">
			<tr>
				<td>
					<form action=\"MemberView.php\" method=\"get\" target=\"_blank\">
		 				<input type=\"submit\" value=\"會員資料\">
		 			</form>
				</td>
			</tr>
			<tr>			
				<td>
					<form action=\"MSView.php\" method=\"get\" target=\"_blank\">
		 				<input type=\"submit\" value=\"訂單管理\">
		 			</form>
				</td>			
			</tr>
			<tr>
				<td>
					<form action=\"ShippingCheckingIndex.php\" method=\"get\" target=\"_blank\">
		 				<input type=\"submit\" value=\"出貨管理\">
		 			</form>
				</td>
			</tr>
			<tr>			
				<td>
					<form action=\"RemitChecking.php\" method=\"get\" target=\"_blank\">
		 				<input type=\"submit\" value=\"對帳管理\">
		 			</form>
				</td>			
			</tr>
			<tr>
				<td>
					<form action=\"BuyingInformationByQuery.php\" method=\"get\" target=\"_blank\">
		 				<input type=\"submit\" value=\"會員結帳代查詢\">
		 			</form>
				</td>			
			</tr>			
		</table>";
		echo $AdminTable;
		?>
		</body>
		
		</html>
	<?php
}
?>