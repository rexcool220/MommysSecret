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
	
	$fb->setDefaultAccessToken($accessToken);
	
	$fbAccount = GetFBAccount($fb);
	
	$googleFormUrl = 'https://docs.google.com/forms/d/1kCA1gdJDOD0X0hPfHdW4E9z0k7HPuBl0AaimQLnpAnw/viewform?entry.743012400=';
	
	$customedGoogleForm = 'http://localhost/MommysSecret/CustomedGoogleForm.php';
	
	$RedirectUrl = $customedGoogleForm.'?CustomedGoogleForm='.$googleFormUrl.'&FbAccount='.urlencode($fbAccount);
	
	header("location: ".$RedirectUrl);

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
	