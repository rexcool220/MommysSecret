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
	
	$permissions = ['user_managed_groups','publish_actions']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/SetFacebookAccessTokenCallBack.php', $permissions);
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>