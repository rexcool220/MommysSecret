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
<title>匯款出貨確認表</title>
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
	echo "管理者 : $fbAccount";
}
else
{
	echo "$fbAccount : 你不是管理者";
	exit;
}


$sql = "SELECT * FROM  `RemitRecord` ORDER BY 匯款編號  DESC ;";

$result = mysql_query($sql,$con);

if (!$result) {
	die('Invalid query: ' . mysql_error());
}
$remitCheckingTableCount = mysql_num_rows($result);

$remitCheckingTable = "<table id=\"remitCheckingTable\">
	<tr>
	<th>匯款編號 </th>
	<th>FB帳號</th>
	<th>匯款金額</th>
	<th>應匯款金額</th>
	<th>匯款末五碼</th>
	<th>匯款日期</th>
	<th>Memo</th>
	<th>已收款</th>
	<th>管理員備註</th>				
	<th></th>
	</tr>";
while($row = mysql_fetch_array($result))
{
	$isRemited = $row['已收款'] == 0 ? "否" : "已收";
	$isShipped = $row['已出貨'] == 0 ? "否" : "已出";
	$remitCheckingTable = $remitCheckingTable . "<tr>";
	$remitCheckingTable = $remitCheckingTable . "<td><a href=\"BuyingInformationByRemitNumber.php?remitNumber=".$row['匯款編號']."\" target=\"_blank\">".$row['匯款編號']."</a></td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['FB帳號'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款金額'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['應匯款金額'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款末五碼'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款日期'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['Memo'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>$isRemited</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['管理員備註'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>
			
	<form action=\"RemitCheckingCallBack.php\" method=\"get\">
 		<input type=\"hidden\" name=\"RemitChecked\" value=\"run\">
 		<input type=\"hidden\" value=\"".$row['匯款編號']."\" name=\"remitNumber\">
 		<input type=\"submit\" value=\"確認已匯款\" >
 	</form>		
	</td>";
	$remitCheckingTable = $remitCheckingTable . "</tr>";
}
$remitCheckingTable = $remitCheckingTable . "</table>";

if (!empty($_GET['RemitChecked'])) {
	$remitNumber = $_GET['remitNumber'];
	
	$sql = "UPDATE `RemitRecord` SET `已收款` = '1'  WHERE 匯款編號 = $remitNumber";
	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$sql = "UPDATE `ShippingRecord` SET `確認收款` = '1'  WHERE 匯款編號 = $remitNumber";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	header("location: http://mommyssecret.tw/RemitCheckingCallBack.php");
}

if (!empty($_GET['remitNumberLink'])) {
	$remitNumberLink = $_GET['remitNumberLink'];
	$managerMemo = $_GET['managerMemo'];
	if(preg_match("/(?<=remitNumber=)[0-9]+/", $remitNumberLink, $matches)) {
		$remitNumber = $matches[0];
	}
	else {
		echo 'Some thing error<p>';
		exit;
	}
	$sql = "UPDATE `RemitRecord` SET `管理員備註` = '$managerMemo'  WHERE 匯款編號 = $remitNumber";
	
	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	header("location: http://mommyssecret.tw/RemitCheckingCallBack.php");
}

echo $remitCheckingTable;



mysql_close($con);
?>
<script>
var table = document.getElementById("remitCheckingTable");
if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 8)//管理員備註
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
    var memo = prompt("輸入管理員備註");
	var remitNumberLink = table.rows[tableCell.parentNode.rowIndex].cells[0].innerHTML;
	window.location.replace("http://mommyssecret.tw/RemitCheckingCallBack.php?managerMemo=" + memo + "&remitNumberLink=" + remitNumberLink);
}
</script>
</body>
</html>