<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
if(!session_id()) {
	session_start();
}
ignore_user_abort(true);
$fb = new Facebook\Facebook([
		'app_id' => '1540605312908660',
		'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
		'default_graph_version' => 'v2.6',
]);
$fbAccount = urldecode($_SESSION['fbAccount']);
$fieldID = $_SESSION['fieldID'];
$facebookID = $_SESSION['facebookID'];
$groupID = $_SESSION['groupID'];
$spreadsheetCount = $_SESSION['spreadsheetCount'];
	
$accessToken = 'EAAV5LCBqPXQBAMdDj83ZCk1RbL6SRX2TpYvjmtww8ED4LJGxVRJDmdZCDwiF5lyP09UJGdtRUfn1BxzHKkM8B8xEDbobSxfoi3eEZCQ6lcjAkRw8iB1PLAkIi2R2K0qAwSDQfC9O0TFVyMcgZCj4qJa8PpCC3U1kmWq8aikc1wZDZD';
								

$fb->setDefaultAccessToken($accessToken);

PublishMessage($fb, ParseGoogleSpreadSheet($fbAccount, $fieldID, $spreadsheetCount), $facebookID);

$UritoPublish = 'https://www.facebook.com/groups/'.
$groupID.
'/permalink/'.
$facebookID.
'/';

header("location: ".$UritoPublish);

function PublishMessage($fb, $message, $facebookID)
{
	# Facebook PHP SDK v5: Publish to User's Timeline
	try {
		$res = $fb->post( '/'.$facebookID.'/comments', array(
				'message' => $message
		));
		$post = $res->getGraphObject();
		return true;
	} catch (Exception $e) {
		echo $e->getMessage();
		return false;
	}
}
function ParseGoogleSpreadSheet($fbAccount, $fieldID, $spreadsheetCount)
{
	
	try {
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
		
		$tokenArray = $client->fetchAccessTokenWithAssertion();
		
		$accessToken = $tokenArray["access_token"];
		// Section 6: Uncomment to parse table data with SimpleXML
	 	$url = "https://spreadsheets.google.com/feeds/list/".$fieldID."/default/private/full";
	 	$method = 'GET';
	 	$headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
	 	$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
	 	$resp = $httpClient->request($method, $url);
	 	$body = $resp->getBody()->getContents();
	 	$tableXML = simplexml_load_string($body);
	 	$result = false; 
		for($timeout = 0; $timeout < 10; $timeout++)
	 	{
			if ((SpreadsheetCount($tableXML, $fbAccount) < $spreadsheetCount))
			{
				sleep(1);
			}
			else {
				$result = true;
				break;
			}
	 	}
	 	if($result == false)
	 	{
			echo $fbAccount;
			echo SpreadsheetCount($tableXML, $fbAccount).'<P>';
			echo $spreadsheetCount.'<P>';
			exit;
	 	}
		
		
		foreach ($tableXML->entry as $entry) {
		  foreach ($entry->children('gsx', TRUE) as $column) {
		  	if(($column->getName() == 'fbaccount') && ($column == $fbAccount))
		  	{
		  		$lastEntry = $entry;
		  	}
		  }
		}
		$returnString = "";
		foreach ($lastEntry->children('gsx', TRUE) as $column) {
			if(empty($column))
				continue;
			    $colName = $column->getName();
			    $colValue = $column;
// 			    echo $colName;
// 			    echo $colValue;
			    $returnString = $returnString ."$colName : $colValue\n";
		}
		 return $returnString;

	 } catch (Exception $e) {
	 	echo $e->getMessage();
	 }
}

function SpreadsheetCount($tableXML, $fbAccount)
{
	$count = 0;
	foreach ($tableXML->entry as $entry) {
		foreach ($entry->children('gsx', TRUE) as $column) {
			if(($column->getName() == 'fbaccount')&&($column == $fbAccount)) {
				$count++;
			}
		}
	}
	return $count;
}
?>