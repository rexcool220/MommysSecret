<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
	$fb = new Facebook\Facebook([
	  'app_id' => '198155157308846',
	  'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email','user_managed_groups'];
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/Members/RebateViewCallBack.php', $permissions);
	header("location: ".$loginUrl);
?>