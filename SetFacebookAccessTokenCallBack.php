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
	
	if(GetFBAccount($fb) == '古振平')
	{
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connection = socket_connect($socket,'127.0.0.1', 1234);
		$message = 'SetAccessToken,'.$accessToken;
		// send string to server
		socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
		// get server response
		if(socket_read ($socket, 1024) or die("Could not read server response\n") == true) {
			echo 'AccessToken is set';
		}
		else {
			echo 'AccessToken setting fail';
		}
	}
	else
	{
		echo 'You are not admin';
	}

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
	