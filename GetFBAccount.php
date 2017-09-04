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
		$accessToken = $helper->getAccessToken('http://mommyssecret.tw/MS/GetFBAccount.php');
		//$accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	//echo $accessToken;
	
	$fb->setDefaultAccessToken($accessToken);
	
	$_SESSION['fbAccount'] = urlencode(GetFBAccount($fb));
	
	$customedGoogleForm = 'http://mommyssecret.tw/MS/CustomedGoogleForm.php';
	
	
	
	header("location: ".$customedGoogleForm);

	function GetFBAccount($fb)
	{
		try {
			$response = $fb->get('/me');
			$userNode = $response->getGraphUser();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		return $userNode->getName();
	}
?>
	