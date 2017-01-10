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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="Admin.css?20170110">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
	  'app_id' => '198155157308846',
	  'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
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
				'app_id' => '198155157308846',
				'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
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
			($fbAccount == 'Queenie Tsan')||
			($fbAccount == '熊會買')||
			($fbAccount == '熊哉')||
    		($fbAccount == '古振平')||
            ($fbAccount == 'Keira Lin'))
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
			        <a href=\"MemberView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-user\"></span> 會員資料
			        </a>				
				</td>
				<td>
			        <a href=\"MSView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-shopping-cart\"></span> 訂單管理
			        </a>			
				</td>				
			</tr>
			<tr>
				<td>
			        <a href=\"ShippingCheckingIndex.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-plane\"></span> 出貨管理
			        </a>				
				</td>		
				<td>
			        <a href=\"RemitChecking.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-piggy-bank\"></span> 對帳管理
			        </a>				
				</td>			
			</tr>
			<tr>
				<td>
			        <a href=\"BuyingInformationByQuery.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-search\"></span> 會員結帳代查詢
			        </a>				
				</td>			
				<td>
			        <a href=\"NotRemitList.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-thumbs-down\"></span> 未匯款清單
			        </a>				
				</td>			
			</tr>		
			<tr>
				<td>
			        <a href=\"FBParser.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-check\"></span> 點單系統
			        </a>			
				</td>			
				<td>
			        <a href=\"TagByItemID.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-tags\"></span> Tag小工具
			        </a>
				</td>			
			</tr>		
			<tr>
				<td>
			        <a href=\"ItemCategoryView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-download-alt\"></span> 到貨管理
			        </a>
				</td>			
				<td>
			        <a href=\"TagByUnknownMembers.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-question-sign\"></span> 失蹤會員小幫手 
			        </a>			
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