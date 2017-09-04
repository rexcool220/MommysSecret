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
	  'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email','user_managed_groups']; // optional
	//$permissions = ['email','publish_actions','user_managed_groups']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MS/TagByItemIDCallBack.php', $permissions);
	 
	$_SESSION['googleFormUrl'] = $_GET['googleFormUrl'];
	$_SESSION['fieldID'] = $_GET['fieldID'];
	$_SESSION['facebookID'] = $_GET['facebookID'];
	$_SESSION['groupID'] = $_GET['groupID'];
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>