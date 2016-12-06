<?php
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
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/GetBuyingInformationCallBack.php', $permissions);
	
	//echo urldecode($loginUrl);
	$_SESSION['personal'] = $_POST['personal'];
	header("location: ".$loginUrl);
?>