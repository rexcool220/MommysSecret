<?php
	header("Content-Type:text/html; charset=utf-8");
	require_once __DIR__ . '/vendor/autoload.php';
	include_once "./vendor/google/apiclient/examples/templates/base.php";
?>
<html>
<head>
<title>MommyAdmin</title>
</head>
<body>

<form method="POST" action="">
	Step 1 貼上Google 表單的網址：<input type="text" value="" name="googleFormUrl" style="width: 600px;"><p>
	Step 2 貼上Google表單統計網址：<input type="text" value="" name="googleFormResponseUrl" style="width: 600px;"><p>
	Step 3 貼上Facebook開團網址：<input type="text" value="" name="FacebookUrl" style="width: 600px;"><p>
	Step 4 ：<input type="submit" value="產生網址"><p>
<?php 	
if(!empty($_POST)) {
    $googleFormUrl = $_POST['googleFormUrl'];
    $googleFormResponseUrl = $_POST['googleFormResponseUrl'];
    $FacebookUrl = $_POST['FacebookUrl'];

    if(preg_match("/[^=]*=/", $googleFormUrl, $matches)) {
    	$googleFormUrl = $matches[0];
    }
    else {
    	echo 'Google 表單的網址錯誤<p>';
    	exit;
    }
    if(preg_match("/(?<=https:\/\/docs.google.com\/spreadsheets\/d\/)[^\/]*/", $googleFormResponseUrl, $matches)) {
    	$googleFormResponseUrl = $matches[0];
    }
    else {
    	echo 'Google表單統計網址錯誤<p>';
    	exit;
    }
    if(preg_match("/(?<=groups\/)[0-9]+/", $FacebookUrl, $matches)) {
    	$groupID = $matches[0];
    }
    else {
    	echo 'Facebook開團網址錯誤<p>';
    	exit;
    }
    if(preg_match("/(?<=permalink\/)[0-9]+/", $FacebookUrl, $matches)) {
    	$FacebookID = $matches[0];
    }
    else {
    	echo 'Facebook開團網址錯誤<p>';
    	exit;
    }
    $combinedUrl = 'http://MommysSecret.tw/FacebookLogin.php?googleFormUrl='.
    $googleFormUrl.'&fieldID='.
    $googleFormResponseUrl.'&facebookID='.
    $FacebookID.'&groupID='.
    $groupID;
    
    echo $combinedUrl;

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
		exit;
	}

	$client->setApplicationName("Sheets API Testing");

	$client->setScopes(['https://www.googleapis.com/auth/urlshortener']);

	$tokenArray = $client->fetchAccessTokenWithAssertion();

	$accessToken = $tokenArray["access_token"];

	$service = new Google_Service_Urlshortener($client);

	$url = new Google_Service_Urlshortener_Url();
	$url->longUrl = $combinedUrl;
	try {
		$short = $service->url->insert($url);
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	echo "<a href=\"".$short['id']."\">".$short['id']."</a>";
}
?>

</form>

</body>
</html>