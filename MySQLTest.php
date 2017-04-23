<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
?>
<html>
<head>
<title>OnSale</title>
</head>
<body>
<?php
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
			
		//get list of worksheets
		$url = "https://spreadsheets.google.com/feeds/list/1HUp0_0GTBzFT1vkn8rd4fjiN5oRCaG61GPA02hmBl3c/default/private/full";
		//$url = "https://spreadsheets.google.com/feeds/list/$ShippingInformation/default/private/full";
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
			$field = array("", "", "", "", "");
			foreach ($entry->children('gsx', TRUE) as $column)
			{
				$field[$fieldCount] = $column;
				$fieldCount++;
				echo $column;
				echo "<br>";
			}
			
			
// 		$sql = "INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `FBID`, `品項`, `單價`, `數量`)
// 		VALUES (\"$field[0]\", \"$field[1]\", \"$field[2]\", \"$field[3]\", \"$field[4]\", \"$field[5]\")
// 		ON DUPLICATE KEY UPDATE `FB帳號`=\"$field[1]\", `FBID`=\"$field[2]\", `品項`=\"$field[2]\", `單價`=\"$field[3]\", `數量`=\"$field[4]\"";
				
		$sql = "INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `FBID`, `品項`, `單價`, `數量`, `規格`, `Active`, `ItemID`)
		VALUES (NULL, \"$field[0]\", \"$field[1]\", \"柳宗理補貨團\", \"$field[3]\", \"$field[4]\", \"$field[2]\", \"1\", \"804015769756005\")";

			$result = mysql_query($sql,$con);
			if (!$result) {
				echo "$sql<br>";
				die('Invalid query:1 ' . mysql_error());
			}
		}
		echo "<h1>成功更新定單資料</h1>";
	} catch (Exception $e) {
		echo $e->getMessage();
	}
?>
</body>
</html>