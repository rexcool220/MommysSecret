<?php
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	if($_GET['googleFormUrl'] == '')
	{
		echo 'Google form url is empty';
		exit;
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '1540605312908660',
	  'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://localhost/MommysSecret/GetFBAccount.php', $permissions);
	 
	$_SESSION['googleFormUrl'] = $_GET['googleFormUrl'];
	$_SESSION['fieldID'] = $_GET['fieldID'];
	$_SESSION['facebookID'] = $_GET['facebookID'];
	$_SESSION['groupID'] = $_GET['groupID'];
	
	
	header("location: ".$loginUrl);
?>