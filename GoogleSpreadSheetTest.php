<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
	
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
			
		$client->setApplicationName("Parse Google form");
	
		$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);
	
		$tokenArray = $client->fetchAccessTokenWithAssertion();
	
		$accessToken = $tokenArray["access_token"];
						
		//
		// Section 2: Uncomment to get list of worksheets
		$ssid = "1ua3bJhO1zQOMmXHyezBxQoJ6zYq-y9amLNsDgAdavqo";
		$url = "https://spreadsheets.google.com/feeds/worksheets/1ua3bJhO1zQOMmXHyezBxQoJ6zYq-y9amLNsDgAdavqo/private/full";
		$method = 'GET';
		$headers = ["Authorization" => "Bearer $accessToken"];
		$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
		$resp = $httpClient->request($method, $url);
		$body = $resp->getBody()->getContents();
		$tableXML = simplexml_load_string($body);
		
		foreach ($tableXML->entry as $entry)
		{
			$id = $entry->id;
			$title = $entry->title;
			if($title == "登記出貨頁面(欄位不能動)")
			{
				if(preg_match("/[a-zA-Z0-9]+$/", $id, $matches)) {
					$wsid = $matches[0];
				}
			}
		}
		if(empty($wsid))
		{
			echo "wsid is empty";
			exit;
		}
		$url = "https://spreadsheets.google.com/feeds/list/$ssid/$wsid/private/full";
		$method = 'GET';
		$headers = ["Authorization" => "Bearer $accessToken"];
		$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
		$resp = $httpClient->request($method, $url);
		$body = $resp->getBody()->getContents();
		$tableXML = simplexml_load_string($body);
		
		$serialNumber = 1;
		foreach ($tableXML->entry as $entry)
		{
			$fieldCount = 0;
			$field = array("", "", "", "", "", "", "");
			foreach ($entry->children('gsx', TRUE) as $column)
			{
				$field[$fieldCount] = $column;
				$fieldCount++;
				echo $column;
				echo "<br>";
			}
		}

	} 
	catch (Exception $e)
	{
		echo $e->getMessage();
	}
		
?>