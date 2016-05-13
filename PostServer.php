<?php
require_once __DIR__ . '/vendor/autoload.php';

include_once "./vendor/google/apiclient/examples/templates/base.php";

$fb = new Facebook\Facebook([
		'app_id' => '1540605312908660',
		'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
		'default_graph_version' => 'v2.6',
]);

$accessToken = 'EAAV5LCBqPXQBAA9YnVQESknv1ZAHvvgiZCy0GNmo6HoE3nNqtTZB1bIBXowYe6iDTKHVqj9klX9848DzqHcIZBrQTVfmMyHw8EDTjNsmrJZBWfJHFnQOmQp9Tb7GXuyMWfC24yli4MXTDdAoth4oB420DukWrw8Ql9DKzOzB7DAZDZD';

$fb->setDefaultAccessToken($accessToken);

set_time_limit(0);

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not createsocket\n");

$result = socket_bind($socket, '127.0.0.1', '1234') or die("Could not bind tosocket\n");

$result = socket_listen($socket, 3) or die("Could not set up socketlistener\n");


while (true) {
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");

	$input = socket_read($spawn, 1024) or die("Could not read input\n");
	
	$input = trim($input);
	
	echo "Client Message : ".$input;
	
	
	if(PublishMessage($fb, ParseGoogleSpreadSheet($input))){
		socket_write($spawn, 'Successed', strlen ('Successed')) or die("Could not write output\n");
	}
	else {
		$input = 'Send '.$input.
		socket_write($spawn, 'Fail', strlen ('Fail')) or die("Could not write output\n");
	}
	
	// 
	socket_close($spawn);
}
socket_close($socket);

function PublishMessage($fb, $message)
{
	# Facebook PHP SDK v5: Publish to User's Timeline
	try {
		$res = $fb->post( '/626284500859523/comments', array(
				'message' => $message
		));
		$post = $res->getGraphObject();
		return true;
	} catch (Exception $e) {
		echo $e->getMessage();
		return false;
	}
}
function ParseGoogleSpreadSheet($fbaccount)
{
	$client = new Google_Client();
	
	putenv("GOOGLE_APPLICATION_CREDENTIALS=Mommyssecret-e24d4b121c15.json");
	
	if ($credentials_file = checkServiceAccountCredentialsFile()) {
		// set the location manually
		$client->setAuthConfig($credentials_file);
	} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
		// use the application default credentials
		$client->useApplicationDefaultCredentials();
	} else {
		echo missingServiceAccountDetailsWarning();
		return;
	}
	
	$client->setApplicationName("Sheets API Testing");
	
	$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);
	
	$fileId = "1tLQ4_ZfSPI3fGn6PDI1fIhYcMOIcTDxa_h7sY1Ghcw0";
	
	$tokenArray = $client->fetchAccessTokenWithAssertion();
	
	$accessToken = $tokenArray["access_token"];
	
	// Section 6: Uncomment to parse table data with SimpleXML
	$url = "https://spreadsheets.google.com/feeds/list/$fileId/default/private/full";
	$method = 'GET';
	$headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
	$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
	$resp = $httpClient->request($method, $url);
	$body = $resp->getBody()->getContents();
	$tableXML = simplexml_load_string($body);
	foreach ($tableXML->entry as $entry) {
	  foreach ($entry->children('gsx', TRUE) as $column) {
	  	if(($column->getName() == 'fbaccount') && ($column == $fbaccount))
	  	{
	  		$lastEntry = $entry;
	  	}
	  }
	}
	$returnString = "";
	foreach ($lastEntry->children('gsx', TRUE) as $column) {
		    $colName = $column->getName();
		    $colValue = $column;
		    echo $colName;
		    echo $colValue;
		    $returnString = $returnString ."$colName : $colValue\n";
	}
	 return $returnString;
}


?>