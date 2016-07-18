<?php
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
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/GetBuyingInformationCallBack.php', $permissions);
	
	//echo urldecode($loginUrl);
	$_SESSION['personal'] = $_GET['personal'];
	header("location: ".$loginUrl);
?>