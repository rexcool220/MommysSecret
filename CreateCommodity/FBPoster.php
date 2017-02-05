<?php
	require_once dirname(__DIR__).'/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	if($_GET['googleFormUrl'] == '')
	{
		echo 'Google form url is empty';
		//exit;
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '198155157308846',
	  'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email','user_managed_groups','publish_actions']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/CreateCommodity/FBPosterCallBack.php', $permissions);
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>