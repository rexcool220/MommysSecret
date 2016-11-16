<?php
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '1540605312908660',
	  'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['user_managed_groups','publish_actions']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/SetFacebookAccessTokenCallBack.php', $permissions);
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>