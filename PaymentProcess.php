<?php
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '198155157308846',
	  'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email'];
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MS/PaymentProcessCallBack.php', $permissions);
	
	header("location: ".$loginUrl);
?>