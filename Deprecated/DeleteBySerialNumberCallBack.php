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
<title>刪除定單</title>
</head>
<body>
<?php
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
			'app_id' => '198155157308846',
			'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
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
 			window.history.replaceState( {} , '刪除定單', 'http://mommyssecret.tw/DeleteBySerialNumberCallBack.php' );
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
	echo "管理者 : $fbAccount";
}
else
{
	echo "$fbAccount : 你不是管理者";
	exit;
}
?>
<form method="POST" action="">
	定單編號：<input type="text" value="" name="SerialNumber" style="width: 600px;"><p>
	<input type="submit" value="查詢"><p>
</form>

</body>
</html>
<?php 

if(!empty($_POST['SerialNumber'])) {
	$SerialNumber = $_POST['SerialNumber'];
	$sql = "SELECT * FROM `ShippingRecord` WHERE SerialNumber = '$SerialNumber';";

	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	$SelectedTable = "<table border='1'>
		<tr>
		<th>定單編號</th>
		<th>FB帳號 </th>
		<th>品項</th>
		<th>單價</th>
		<th>數量</th>
		<th>匯款日期</th>
		<th>確認收款</th>
		<th>出貨日期</th>
	  	<th>匯款編號</th>
		</tr>";
	while($row = mysql_fetch_array($result))
	{
		$isReceivedPayment = ($row['確認收款'] == 0)?"否":"已收";
		$SelectedTable = $SelectedTable . "<tr>";
		$SelectedTable = $SelectedTable . "<td>" . $row['SerialNumber'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['FB帳號'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['品項'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['單價'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['數量'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['匯款日期'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $isReceivedPayment . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['出貨日期'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>" . $row['匯款編號'] . "</td>";
		$SelectedTable = $SelectedTable . "<td>
			
		<form action=\"DeleteBySerialNumberCallBack.php\" method=\"POST\">
	 		<input type=\"hidden\" name=\"act\" value=\"run\">
	 		<input type=\"hidden\" value=\"".$row['SerialNumber']."\" name=\"SerialNumber\">
	 		<input type=\"submit\" value=\"確認刪除\">
	 	</form>
		</td>";
		$SelectedTable = $SelectedTable . "</tr>";
	}
	$SelectedTable = $SelectedTable . "</table>";
}

if (!empty($_POST['act'])) {
 	$SerialNumber = $_POST['SerialNumber'];

	$sql = "DELETE FROM `ShippingRecord` WHERE `SerialNumber` = '$SerialNumber'";
	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	header("location: http://mommyssecret.tw/DeleteBySerialNumberCallBack.php");
}

echo $SelectedTable;

mysql_close($con);
?>
</body>
</html>