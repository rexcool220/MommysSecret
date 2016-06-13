<?php
require_once __DIR__ . '/vendor/autoload.php';

include_once "./vendor/google/apiclient/examples/templates/base.php";

header("Content-Type:text/html; charset=utf-8");
	if(!session_id()) {
		session_start();
	}
	
	$_SESSION['spreadsheetCount'] = getSpreadSheetCount();
	
	$customedGoogleForm = $_SESSION['googleFormUrl'];
	
	$fbAccount =  $_SESSION['fbAccount'];
	
	//$customedGoogleForm = 'https://docs.google.com/forms/d/1kCA1gdJDOD0X0hPfHdW4E9z0k7HPuBl0AaimQLnpAnw/viewform?entry.743012400=';
	
	$data = ObtainPageSource($customedGoogleForm.$fbAccount);
	
	$redirectUrl = "http://MommysSecret.tw/PostToFB.php";
	
	if(preg_match("/(?<=<form action=\")[^\"]*/", $data, $matches)) {
		$googleResponseUrl = $matches[0];
	}
	else {
		echo 'not matched';
	}
	
	if(preg_match("/<form[\s\S]*form>/", $data, $matches)) {
		$stringBetweenTagForm = $matches[0];
		echo htmlspecialchars($temp);
	}
	else {
		echo 'not matched';
	}
	
	$replacement = "<script type=\"text/javascript\">var submitted=false;</script>
		<iframe name=\"hidden_iframe\" id=\"hidden_iframe\"
		style=\"display:none;\" onload=\"if(submitted)
		{window.location='".$redirectUrl."';}\"></iframe>
		<form action=\"".$googleResponseUrl
		."\" method=\"post\"
		target=\"hidden_iframe\" onsubmit=\"submitted=true;\">";
	
	$stringRefine = str_replace("<div style=\"text-align: center;\"", "<div style=\"text-align: left;\"",$data);
	$stringRefine = preg_replace("/<div class=\"required-message\">[^>]+>/", '', $stringRefine); 
	echo '<body topmargin="100" leftmargin="100">';
	echo preg_replace("/(<form[^>]*>)/", $replacement, $stringRefine);
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
	
	function getSpreadSheetCount()
	{
		try {
			$fieldID = $_SESSION['fieldID'];
			
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
			
			$url = "https://spreadsheets.google.com/feeds/list/".$fieldID."/default/private/full";
			
			$method = 'GET';
			
			$headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
			
			$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
			
			$resp = $httpClient->request($method, $url);
			
			$body = $resp->getBody()->getContents();
			
			$tableXML = simplexml_load_string($body);
			
			$count = 0;
			foreach ($tableXML->entry as $entry) {
				foreach ($entry->children('gsx', TRUE) as $column) {
					if(($column->getName() == 'fbaccount')&&($column == urldecode($_SESSION['fbAccount']))) {
						$count++;
					}
				}
			}
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $count;
	}
 
	