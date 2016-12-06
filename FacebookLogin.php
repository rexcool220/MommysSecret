<?php
	require_once __DIR__ . '/vendor/autoload.php';
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
	
	$permissions = ['email']; // optional
	//$permissions = ['email','publish_actions','user_managed_groups']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://MommysSecret.tw/GetFBAccount.php', $permissions);
	 
	$_SESSION['googleFormUrl'] = $_GET['googleFormUrl'];
	$_SESSION['fieldID'] = $_GET['fieldID'];
	$_SESSION['facebookID'] = $_GET['facebookID'];
	$_SESSION['groupID'] = $_GET['groupID'];
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>