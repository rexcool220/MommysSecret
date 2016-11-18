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
	  'app_id' => '1540605312908660',
	  'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email','user_managed_groups']; // optional
	//$permissions = ['email','publish_actions','user_managed_groups']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/ItemCategoryViewCallBack.php', $permissions);
	 
	$_SESSION['googleFormUrl'] = $_GET['googleFormUrl'];
	$_SESSION['fieldID'] = $_GET['fieldID'];
	$_SESSION['facebookID'] = $_GET['facebookID'];
	$_SESSION['groupID'] = $_GET['groupID'];
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
?>