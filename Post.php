<?php
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	$message = 'post by program';
	try {
		$response = (new FacebookRequest(
				$session, 'POST', '/623873191100654/feed', array(
						'message'       => $message,
				)
				))->execute()->getGraphObject();
				echo "Posted with id: " . $response->getProperty('id');
	} catch(FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
	}