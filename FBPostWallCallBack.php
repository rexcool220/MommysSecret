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
	
	$fbAccount = PublishMessage($fb);
	
	$next = 'https://www.facebook.com/';
	
	$logoutUrl = $helper->getLogoutUrl($accessToken, $next);
	
	echo '<a href="' . htmlspecialchars($logoutUrl) . '">Log out with Facebook!</a>';
	
	//header("location: ".$RedirectUrl);

	function PublishMessage($fb)
	{
		# Facebook PHP SDK v5: Publish to User's Timeline
		try {
			$res = $fb->post( '/622296324591674/feed', array(
					'message' => 'Test1234'
			));
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		try {
			$post = $res->getGraphObject();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		var_dump( $post );
	}
?>
	