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
<title>未匯款品項</title>
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
			window.history.replaceState( {} , '出貨確認表', 'http://mommyssecret.tw/ShippingCheckingCallBack.php' );
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
		($fbAccount == '古振平')||
        ($fbAccount == 'Keira Lin'))
{
// 	echo "管理者 : $fbAccount";
}
else
{
	echo "$fbAccount : 你不是管理者";
	exit;
}

if(isset($_POST['CustomerFBID'])) {

    $CustomerFBID = $_POST['CustomerFBID'];

    $_SESSION['CustomerFBID'] = $_POST['CustomerFBID'];

}



if(isset($CustomerFBID)) {

    $sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$CustomerFBID' AND 匯款日期= '0000-00-00' ORDER BY SerialNumber;";

    $result = mysql_query($sql,$con);

    if (!$result) {
        die('Invalid query: ' . mysql_error());
    }
    $NotRemitCount = mysql_num_rows($result);

    $NotRemitTable = $NotRemitTable . "<table>
	<tr>
  	<th>SN</th>
	<th>FB帳號 </th>
	<th>FBID </th>
	<th>品項</th>
	<th>單價</th>
	<th>數量</th>
	<th>金額</th>
	<th>匯款日期</th>
	<th>確認收款</th>
	<th>出貨日期</th>
  	<th>匯款編號</th>
	<th></th>
	</tr>";
    $totalPrice = 0;
    while($row = mysql_fetch_array($result))
    {
        $toShowShippingCheckBox = false;
        if($row['出貨日期'] == "0000-00-00")
        {
            $row['出貨日期'] = "";
        }
        if($row['匯款日期'] == "0000-00-00")
        {
            $row['匯款日期'] = "";
        }

        $isReceivedPayment = ($row['確認收款'] == 0)?"否":"已收";

        $subTotal = $row['單價'] * $row['數量'];
        
        $NotRemitTable = $NotRemitTable . "<tr>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['SerialNumber'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['FB帳號'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['FBID'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['品項'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['單價'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['數量'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $subTotal . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['匯款日期'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $isReceivedPayment . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['出貨日期'] . "</td>";
        $NotRemitTable = $NotRemitTable . "<td>" . $row['匯款編號'] . "</td>";
        $NotRemitTable = $NotRemitTable . "</tr>";
    }
    echo $NotRemitTable;
}
