<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>FBParser</title>
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="MommysSecret.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="MommysSecret.css?20160825">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>  
</head>
<body>
<?php 
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
			'app_id' => '1540605312908660',
			'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
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

	if(empty($accessToken)&&!empty($_SESSION['accessToken']))
	{
		$accessToken = $_SESSION['accessToken'];
	}
	else if(!empty($accessToken))
	{
		$_SESSION['accessToken'] = $accessToken;
	}
	else if(!empty($accessToken)&&!empty($_SESSION['accessToken']))
	{
		echo "accessToken error";
		exit;
	}
	$fb->setDefaultAccessToken($accessToken);
}
?>
	<script>
		window.history.replaceState( {} , 'PaymentProcess', 'http://mommyssecret.tw/FBParserCallBack.php' );
	</script>
<?php
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
	if(($fbAccount == '熊會買')||
		($fbAccount == '熊哉'))
	{
	    	 
//         echo $userNode->getId();	
	}
	else
	{
	    echo "$fbAccount : 你不是管理者";
	    exit;
	}
	
	
// 	$ssid = "145r0XELzQQUtjIFk7KqRBXJAEMFrRc9zn1xkuB3H_-4";

// 	$client = new Google_Client();

// 	putenv("GOOGLE_APPLICATION_CREDENTIALS=Mommyssecret-e24d4b121c15.json");

// 	if ($credentials_file = checkServiceAccountCredentialsFile()) {
// 		// set the location manually
// 		$client->setAuthConfig($credentials_file);
// 	} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
// 		// use the application default credentials
// 		$client->useApplicationDefaultCredentials();
// 	} else {
// 		echo missingServiceAccountDetailsWarning();
// 		return;
// 	}
		
// 	$client->setApplicationName("Parse Google form");

// 	$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);

// 	$tokenArray = $client->fetchAccessTokenWithAssertion();

// 	$googleAccessToken = $tokenArray["access_token"];
		
// 	//Get wsid from URL
// 	$url = "https://spreadsheets.google.com/feeds/worksheets/$ssid/private/full";
// 	$method = 'GET';
// 	$headers = ["Authorization" => "Bearer $googleAccessToken"];
// 	$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// 	$resp = $httpClient->request($method, $url);
// 	$body = $resp->getBody()->getContents();
// 	$tableXML = simplexml_load_string($body);
	
// 	foreach ($tableXML->entry as $entry)
// 	{
// 		$id = $entry->id;
// 		$title = $entry->title;
// 		if($title == "點單表單")
// 		{
// 			if(preg_match("/[a-zA-Z0-9]+$/", $id, $matches)) {
// 				$wsid = $matches[0];
// 			}
// 		}
// 	}
// 	if(empty($wsid))
// 	{
// 		echo "wsid is empty";
// 		exit;
// 	}
	
// 	$url = "https://spreadsheets.google.com/feeds/list/$ssid/$wsid/private/full";
// 	$method = 'GET';
// 	$headers = ["Authorization" => "Bearer $googleAccessToken"];
// 	$httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// 	$resp = $httpClient->request($method, $url);
// 	$body = $resp->getBody()->getContents();
// 	$tableXML = simplexml_load_string($body);
	
	//get feed
	
	try {
	    $response = $fb->get("/607414496082801/feed?fields=id,created_time,message&since=". date("Y-m-d", strtotime("-1 months")). "&offset=0");
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
	    echo 'Graph returned an error: ' . $e->getMessage();
	    exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
	    exit;
	}
	$result = $response->getDecodedBody();
 
	$pagesEdge = $response->getGraphEdge();
	
	echo "<table id=\"ItemsList\" width=\"60%\">
	<tr>
	<th>ID</th>
	<th>開團日期</th>
	<th>收單日期</th>
	<th>品項</th>
	<th>規格</th>
	<th>單價</th>
	</tr>";
	
	
	do {
	    foreach ($pagesEdge as $page) {
	    	
	    	preg_match("/(\d+)_(\d+)/", $page['id'], $matches);
	    	$id = $matches[2];
	    	preg_match("/([^\n]+)\n([^\n]+)\n([^\n]+)\n/", $page['message'], $matches);
	    	$dueDate = $matches[2];
	    	$itemName = $matches[3];
	    	
	    	
	    	echo "<tr>";
	    	echo "<td>".$id."</td>";
			echo "<td>".$page['created_time']->format('Y-m-d')."</td>";
			echo "<td>".$dueDate."</td>";
	        echo "<td>".$itemName."</td>";
	        echo "<td>".$itemSpec."</td>";
	        echo "<td>".$itemPrice."</td>";
			echo "</tr>";

	    }
	} while ($pagesEdge = $fb->next($pagesEdge));	
	echo "</table>";

?>
<script>
var table = document.getElementById("ItemsList");

if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
    	
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 0)//FBID
			{
		        table.rows[i].cells[j].onclick = function ()
		        {
		            tableText(this);
		        };
			}
		}
    }
}

function tableText(tableCell) {

	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", "http://mommyssecret.tw/FBParserComment.php");
	form.setAttribute("target", "view");

	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "ID");
	hiddenField.setAttribute("value", tableCell.innerHTML);
	form.appendChild(hiddenField);
	document.body.appendChild(form);

	window.open('', 'view');

	form.submit();
}
</script>