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
<link rel="stylesheet" type="text/css" href="MommysSecret.css">
<title>未出貨確認總表</title>
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
			window.history.replaceState( {} , '未出貨確認總表', 'http://mommyssecret.tw/ShippingCheckingIndexCallBack.php' );
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
if(($fbAccount == 'Gill Fang')||
		($fbAccount == 'JoLyn Dai')||
		($fbAccount == '王雅琦')||
		($fbAccount == 'Queenie Tsan')||
		($fbAccount == '熊會買')||
		($fbAccount == '熊哉')||
		($fbAccount == '熊會算')||
		($fbAccount == '古振平'))
{
// 	echo "管理者 : $fbAccount";
}
else
{
	echo "$fbAccount : 你不是管理者";
	exit;
}
	
	$sql = "SELECT ShippingRecord.FB帳號, ShippingRecord.出貨日期, RemitRecord.匯款編號, RemitRecord.Memo, RemitRecord.管理員備註
FROM  `RemitRecord` ,  `ShippingRecord` 
WHERE ShippingRecord.匯款編號 = RemitRecord.匯款編號
AND ShippingRecord.FB帳號
IN (

SELECT DISTINCT ShippingRecord.FB帳號
FROM  `ShippingRecord` 
WHERE ShippingRecord.確認收款 =1
AND ShippingRecord.出貨日期 =  '0000-00-00'
)
GROUP BY RemitRecord.匯款編號
ORDER BY ShippingRecord.出貨日期  ASC , ShippingRecord.匯款日期 ASC;";
	
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$row = mysql_fetch_array($result);
	
	$ShippingCheckingIndex = "<table id=\"shippingCheckingIndex\" width=\"60%\">
	<tr>
	<th>FB帳號 </th>
	<th>最近出貨日期</th>
	<th>最新匯款編號</th>
	<th>最新客戶備註</th>
	<th>最新管理員備註</th>
	</tr>";
	
	while($row = mysql_fetch_array($result))
	{
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<tr>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['FB帳號'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['出貨日期'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['匯款編號'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['Memo'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['管理員備註'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "</tr>";
	}
	$ShippingCheckingIndex = $ShippingCheckingIndex . "</table>";
	
	echo $ShippingCheckingIndex;
	
	mysql_close($con);
?>
<script>
var table = document.getElementById("shippingCheckingIndex");

if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
    	
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 0)//FB帳號
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
// 	var win = window.open('http://mommyssecret.tw/ShippingCheckingCallBack.php?CustomerfbAccount=' +  tableCell.innerHTML, '_blank');
// 	if (win) {
// 	    //Browser has allowed it to be opened
// 	    win.focus();
// 	} else {
// 	    //Browser has blocked it
// 	    alert('Please allow popups for this website');
// 	}
	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", "http://mommyssecret.tw/ShippingCheckingCallBack.php");
	form.setAttribute("target", "view");

	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "CustomerfbAccount");
	hiddenField.setAttribute("value", tableCell.innerHTML);
	form.appendChild(hiddenField);
	document.body.appendChild(form);

	window.open('', 'view');

	form.submit();
}
</script>
</body>
</html>