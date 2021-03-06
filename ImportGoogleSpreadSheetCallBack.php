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
<title>更新登記表</title>
</head>
<body>
<?php
if(!empty($_POST['ShippingInformation'])) {
	$ShippingInformation = $_POST['ShippingInformation'];
	if(preg_match("/(?<=https:\/\/docs.google.com\/spreadsheets\/d\/)[^\/]*/", $ShippingInformation, $matches)) {
		$ssid = $matches[0];
	}
	else {
		echo '登記表網址錯誤<p>';
		exit;
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
			
		//Get wsid from URL
		$url = "https://spreadsheets.google.com/feeds/worksheets/$ssid/private/full";
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
			if($title == "出貨匯款登記表單")
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
		//get list of worksheets
		$url = "https://spreadsheets.google.com/feeds/list/$ssid/$wsid/private/full";
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
			$field = array("", "", "", "", "", "", "", "");
			foreach ($entry->children('gsx', TRUE) as $column)
			{
				$field[$fieldCount] = $column;
				$fieldCount++;
// 				echo $column;
// 				echo "<br>";
			}
			
			
// 		$sql = "INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `FBID`, `品項`, `單價`, `數量`)
// 		VALUES (\"$field[0]\", \"$field[1]\", \"$field[2]\", \"$field[3]\", \"$field[4]\", \"$field[5]\")
// 		ON DUPLICATE KEY UPDATE `FB帳號`=\"$field[1]\", `FBID`=\"$field[2]\", `品項`=\"$field[2]\", `單價`=\"$field[3]\", `數量`=\"$field[4]\"";
				
		$sql = "INSERT INTO `ShippingRecord` (`SerialNumber`, `FB帳號`, `FBID`, `品項`, `單價`, `數量`, `匯款日期`, `出貨日期`)
		VALUES (NULL, \"$field[1]\", \"$field[2]\", \"$field[3]\", \"$field[4]\", \"$field[5]\", \"$field[6]\", \"$field[7]\")
		ON DUPLICATE KEY UPDATE `FB帳號`=\"$field[1]\", `FBID`=\"$field[2]\", `品項`=\"$field[3]\", `單價`=\"$field[4]\", `數量`=\"$field[5]\", `匯款日期`=\"$field[6]\", `出貨日期`=\"$field[7]\"";

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
}
if(!empty($_POST['Members'])) {
	$Members = $_POST['Members'];
	if(preg_match("/(?<=https:\/\/docs.google.com\/spreadsheets\/d\/)[^\/]*/", $Members, $matches)) {
		$Members = $matches[0];
	}
	else {
		echo '會員資料網址錯誤<p>';
		exit;
	}
	
// 	$sql = "TRUNCATE TABLE  `Members`";
// 	$result = mysql_query($sql,$con);
// 	if (!$result) {
// 		die('Invalid query: ' . mysql_error());
// 	}

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
		$url = "https://spreadsheets.google.com/feeds/list/$Members/default/private/full";
		$method = 'GET';
		$headers = ["Authorization" => "Bearer $accessToken"];
		$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
		$resp = $httpClient->request($method, $url);
		$body = $resp->getBody()->getContents();
		$tableXML = simplexml_load_string($body);
			
		foreach ($tableXML->entry as $entry)
		{		  		
			$fieldCount = 0;
			$field = array("", "", "", "", "", "", "", "", "");
			foreach ($entry->children('gsx', TRUE) as $column)
			{
// 				$sql = "$sql\"$column\", ";
// 				$fieldCount++;
				$field[$fieldCount] = $column;
				$fieldCount++;
			}

			$sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `E-Mail`, `手機號碼`, `郵遞區號地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`) 
				VALUES (\"$field[0]\", \"$field[1]\", \"$field[2]\", \"$field[3]\", \"$field[4]\", \"$field[5]\", \"$field[6]\", \"$field[7]\", \"$field[8]\") 
				ON DUPLICATE KEY UPDATE `姓名`=\"$field[0]\", `E-Mail`=\"$field[2]\", `手機號碼`=\"$field[3]\", `郵遞區號地址`=\"$field[4]\",`全家店到店服務代號`=\"$field[5]\", `寄送方式`=\"$field[6]\", `運費`=\"$field[7]\", `備註`=\"$field[8]\"";
			
// 			$sql = substr($sql, 0, -2);
// 			$sql = "$sql);";
// 			echo "$sql<br>";
			$result = mysql_query($sql,$con);
			if (!$result) {
				echo $sql;
				echo "<br>";
				die('Invalid query2: ' . mysql_error());
			}
		}
	echo "<h1>成功更新會員資料</h1>";
}
if(empty($_POST['Members']) && empty($_POST['ShippingInformation']))
{
	if(!$accessToken)
	{
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
	}
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
	$fbAccount = $userNode->getName();
$fbID = $userNode->getId();
	
	$fbAccount = $userNode->getName();
	
	$result = mysql_query("SELECT TYPE FROM `Members` WHERE FBID = $fbID")
	
	or die(mysql_error());
	
	$row = mysql_fetch_array($result);
	
	$type = $row['TYPE'];
	
	if(($type == "管理員") || ($type == "共用帳號"))
	{
		echo "<p hidden id=\"accountType\">$type</p>";
		echo "<p hidden id=\"fbAccount\">$fbAccount</p>";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}
}
?>
<form method="POST" action="">
	 更新登記表：<input type="text" value="" name="ShippingInformation" style="width: 600px;"><p>
	 更新會員資料：<input type="text" value="" name="Members" style="width: 600px;"><p>
	<input type="submit" value="更新"><p>
</form>

</body>
</html>