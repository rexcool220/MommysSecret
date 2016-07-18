<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");

ParseGoogleSpreadSheet($con);

mysql_close($con);

function ParseGoogleSpreadSheet($con)
{
	$sql = "TRUNCATE TABLE  `Members`";
	$result = mysql_query($sql,$con);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

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
			
			
// 		//		Section 2: Uncomment to get list of worksheets
		$url = "https://spreadsheets.google.com/feeds/list/1pP0eo_CLzSYI-Mf90WeyM7bwqJ92oBAo4Ev0GcV7EP8/default/private/full";
		$method = 'GET';
		$headers = ["Authorization" => "Bearer $accessToken"];
		$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
		$resp = $httpClient->request($method, $url);
		$body = $resp->getBody()->getContents();
		$tableXML = simplexml_load_string($body);
			
		foreach ($tableXML->entry as $entry)
		{		  		
			$sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `E-Mail`, `手機號碼`, `郵遞區號＋地址`, `全家店到店服務代號`, `寄送方式`, `運費`) VALUES (";
			$fieldCount = 0;
			foreach ($entry->children('gsx', TRUE) as $column)
			{
				$sql = "$sql\"$column\", ";
				$fieldCount++;
			}
			$sql = substr($sql, 0, -2);
			$sql = "$sql);";
			echo "$sql<br>";
			$result = mysql_query($sql,$con);
			if (!$result) {
				die('Invalid query: ' . mysql_error());
			}
		}

}
// $sql = "SELECT * FROM AccessToken order by CreatTime Desc limit 0,1;";
// $result = mysql_query($sql,$con);
// $row = mysql_fetch_array( $result );
// echo $row['AccessToken'];

// mysql_close($con);
