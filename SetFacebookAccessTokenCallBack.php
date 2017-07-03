<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'ConnectMySQL.php';
	if(!session_id()) {
	    session_start();
	}
	
	$fb = new Facebook\Facebook([
    'app_id' => '198155157308846',
	'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
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
	if(GetFacebookID($fb) == '1180667211952066')
	{
		$sql = "INSERT INTO AccessToken (AccessToken, CreatTime, ExpireTime) VALUES ('1AAV5LCBqPXQBAAWZCNTqTzCul53l5XwPeDNbaxxazcpJrASUiZC3HaepFeVieVJUVyjyvTf0AQwqS3o97rL9YKSIdDHSnM2L1H2KEM7LqM9fySRexJEvXzuD7vkBIEjkuiGKZAjMU1bAS8cocSlj9Y9UnvtjCAZD', CURRENT_TIMESTAMP, '0000-00-00 00:00:00');";
		$result = mysql_query($sql,$con) or die("Fail to insert AccessToken to DB");
		echo 'Insert AccessToken success';
	}
	else
	{
		echo 'You are not admin';
	}

	function GetFacebookID($fb)
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
		return $userNode['id'];
	}
?>
	