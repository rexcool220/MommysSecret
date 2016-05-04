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
	try {
		$accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	
	
	$customedGoogleForm = $_GET['CustomedGoogleForm'];
	$fbAccount = $_GET['FbAccount'];
	$data = ObtainPageSource($customedGoogleForm.urlencode($fbAccount));
	$googleFormPattern = "/(?<=<form action=\")[^\"]*/";

	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$redirectUrl = "http://localhost/MommysSecret/FBPostWall.php";
	$replacement = "<script type=\"text/javascript\">var submitted=false;</script>
		<iframe name=\"hidden_iframe\" id=\"hidden_iframe\"
		style=\"display:none;\" onload=\"if(submitted)
		{window.location='".$redirectUrl."';}\"></iframe>
		<form action=\"".$customedGoogleForm
		."\" method=\"post\"
		target=\"hidden_iframe\" onsubmit=\"submitted=true;\">";
	
	echo preg_replace("/(<form[^>]*>)/", $replacement, $data);
	
	function ObtainPageSource($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		return $data;
	}
?>
	