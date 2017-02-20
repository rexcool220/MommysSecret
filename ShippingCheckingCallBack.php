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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="MommysSecret.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>  
<title>出貨確認表</title>
</head>
<body>
<!-- <form method="POST" action=""> -->
<!-- 	<input type="text" value="" name="CustomerfbAccount" class="FBSearch" placeholder="FB帳號"><p> -->
<!-- 	<input type="submit" value="查詢"><p> -->
<!-- </form> -->
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
		echo "$fbAccount : 你沒有權限";
		exit;
	}

?>
	
	<script type="text/javascript">
	$( function() {
		var availableAccount =
		<?php
		$sql = "SELECT FB帳號, FBID FROM `Members`;";
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		while($rows[]=mysql_fetch_array($result));
		$prefix = '';
		foreach ($rows as $r)
		{
			$AcountList .= $prefix . '"' . $r[FB帳號].",".$r[FBID] . '"';
			$prefix = ', ';
		}
		echo "[$AcountList];";
		?>
	    $( "#AvailibleAccount" ).autocomplete({
	      source: availableAccount
	    });
  	} );
	</script>
  	<?php		
	echo "<br><form method=\"post\" action=\"\">
			<input id=\"AvailibleAccount\" type=\"text\" value=\"\" name=\"memberFBAccountID\" class=\"FBSearch\" placeholder=\"FB帳號\"><p>
			<input type=\"submit\" value=\"查詢\"><p>
			</form>";

if(isset($_POST['memberFBAccountID']) || isset($_SESSION['memberFBAccountID']))
{
    if(isset($_POST['memberFBAccountID']))
    {
        $_SESSION['memberFBAccountID'] = $_POST['memberFBAccountID'];
        $memberFBAccountID = $_POST['memberFBAccountID'];
    }
    elseif (isset($_SESSION['memberFBAccountID']))
    {
        $memberFBAccountID = $_SESSION['memberFBAccountID'];
    }
     
    $memberFBAccountIDArray = explode(',', $memberFBAccountID);
     
    $memberFBAccount = $memberFBAccountIDArray[0];
    $CustomerFBID = $memberFBAccountIDArray[1];
    $_SESSION['CustomerFBID'] = $CustomerFBID;
}


if(isset($_SESSION['CustomerFBID']))
{
	$CustomerFBID = $_SESSION['CustomerFBID'];
}


if(isset($_POST['CustomerFBID'])) {
	
	$CustomerFBID = $_POST['CustomerFBID'];

	$_SESSION['CustomerFBID'] = $_POST['CustomerFBID'];
	
}

if(isset($CustomerFBID)) {
//      var_dump($_POST["SerialNumbersChecked"]);
//      var_dump($_POST["SerialNumbersAll"]);
	if (isset($_POST["SerialNumbersAll"])) {
		$SerialNumbersChecked = $_POST["SerialNumbersChecked"];
		$SerialNumbersAll = $_POST["SerialNumbersAll"];
		for($i=0;$i<Count($SerialNumbersChecked);$i++) {
			$sql = "UPDATE `ShippingRecord` SET `出貨日期` = CURDATE()  WHERE SerialNumber = '$SerialNumbersChecked[$i]' AND (ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true)";
			$result = mysql_query($sql,$con);
	
			if (!$result) {
				die('Invalid query: ' . mysql_error());
			}
		}
		foreach ($SerialNumbersAll as $serialNumber)
		{
		    if(in_array($serialNumber, $SerialNumbersChecked) == false)
		    {
		        $sql = "UPDATE `ShippingRecord` SET `出貨日期` = '0000-00-00'  WHERE SerialNumber = '$serialNumber' AND (ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true)";
		        $result = mysql_query($sql,$con);
		        
		        if (!$result) {
		            die('Invalid query: ' . mysql_error());
		        }
		        
		    }
		}
		header("location: http://mommyssecret.tw/ShippingCheckingCallBack.php?CustomerFBID=$CustomerFBID");
	}
	
	//$sql = "SELECT * FROM `ShippingRecord`,`RemitRecord` WHERE ShippingRecord.FBID = '$CustomerFBID' AND ShippingRecord.匯款編號  = RemitRecord.匯款編號   AND ShippingRecord.(ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true) ORDER BY 出貨日期;";
	
	$sql = "SELECT 
		ShippingRecord.SerialNumber,
		ShippingRecord.確認收款,
		ShippingRecord.FB帳號,
		ShippingRecord.FBID,
		ShippingRecord.品項,
		ShippingRecord.規格,
		ShippingRecord.單價,
		ShippingRecord.數量,
		ShippingRecord.匯款日期,
		ShippingRecord.出貨日期,
		ShippingRecord.匯款編號,
		RemitRecord.匯款金額 FROM `ShippingRecord`,`RemitRecord` WHERE ShippingRecord.FBID = '$CustomerFBID' 
		AND ShippingRecord.匯款編號
		IN (
			SELECT ShippingRecord.匯款編號
			FROM  `ShippingRecord`
			WHERE ShippingRecord.確認收款 =1
			AND ShippingRecord.匯款日期 > DATE_SUB( CURDATE( ) , INTERVAL 12 WEEK) AND ShippingRecord.FBID = '$CustomerFBID'
			)
		AND ShippingRecord.匯款編號  = RemitRecord.匯款編號   AND ShippingRecord.(ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true) ORDER BY 出貨日期;";
	
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	$toShippingTableCount = mysql_num_rows($result);
	$toShippingTable = "<form action=\"ShippingCheckingCallBack.php\" method=\"post\">
		<input type='submit' value=\"確定出貨!\">
		<input type=\"hidden\" value=\"$CustomerFBID\" name=\"CustomerFBID\">";
	$toShippingTable = $toShippingTable . "<table>
	<tr>
  	<th>SN</th>
	<th>FB帳號 </th>
    <th>FBID </th>
	<th>品項</th>
	<th>規格</th>
	<th>單價</th>
	<th>數量</th>
	<th>金額</th>
	<th>匯款日期</th>
	<th>確認收款</th>
	<th>出貨日期</th>
  	<th>匯款編號</th>
    <th>匯款金額</th>
	<th></th>
	</tr>";
	$totalPrice = 0;
	while($row = mysql_fetch_array($result))
	{
		$checked = true;
		if($row['出貨日期'] == "0000-00-00")
		{
			$row['出貨日期'] = "";
			$checked = false;
		}
		if($row['匯款日期'] == "0000-00-00")
		{
			$row['匯款日期'] = "";
		}
		
		$isReceivedPayment = ($row['確認收款'] == 0)?"否":"已收";
		
		$subTotal = $row['單價'] * $row['數量'];
		$toShippingTable = $toShippingTable . "<tr>";
		$toShippingTable = $toShippingTable . "<td>" . $row['SerialNumber'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['FB帳號'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['FBID'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['品項'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['規格'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['單價'] . "</td>";
		if($row['數量'] > 1)
		{
			$toShippingTable = $toShippingTable . "<td><font color=\"red\">" . $row['數量'] . "</font></td>";
		}
		else 
		{
			$toShippingTable = $toShippingTable . "<td>" . $row['數量'] . "</td>";
		}
		$toShippingTable = $toShippingTable . "<td>" . $subTotal . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['匯款日期'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $isReceivedPayment . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['出貨日期'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['匯款編號'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>" . $row['匯款金額'] . "</td>";
		$toShippingTable = $toShippingTable . "<td>";
		if($checked == false)
		{
			$toShippingTable = $toShippingTable . "<input type=\"checkbox\" name=\"SerialNumbersChecked[]\" value=\"".$row['SerialNumber']."\" style=\"WIDTH: 40px; HEIGHT: 40px\">";
		}
		else
		{
			$toShippingTable = $toShippingTable . "<input type=\"checkbox\" name=\"SerialNumbersChecked[]\" value=\"".$row['SerialNumber']."\" checked style=\"WIDTH: 40px; HEIGHT: 40px\">";
		}
		$toShippingTable = $toShippingTable . "<input type=\"hidden\" name=\"SerialNumbersAll[]\" value=\"".$row['SerialNumber']."\" checked style=\"WIDTH: 40px; HEIGHT: 40px\"></td>";
		$toShippingTable = $toShippingTable . "</tr>";
		$totalPrice = $totalPrice + $subTotal;
	}
	$toShippingTable = $toShippingTable . "</table>";
	$toShippingTable = $toShippingTable . "</form>";
	
	
	$sql = "SELECT * FROM `Members` WHERE FBID  = '$CustomerFBID';";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$row = mysql_fetch_array($result);
	if($totalPrice > 6000)
	{
		$shippingFee = 0;
	}
	else {
		$shippingFee = $row['運費'];
	}
	if($totalPrice == 0)
	{
		$moneyToBePaid = 0;
		$shippingFee = 0;
	}
	else
	{
		$moneyToBePaid = $totalPrice + $shippingFee;
	}
	
	$MemberInformation = "<table id=\"Member\">
		<tr>
		<th>真實姓名</th>
		<td>".$row['姓名']."</td>
		</tr>
		<tr>
		<th>FB帳號</th>
		<td>".$row['FB帳號']."</td>
		</tr>
		<tr>
		<th>FBID</th>
		<td>".$row['FBID']."</td>
		</tr>
		<tr>
		<th>E-Mail</th>
		<td>".$row['E-Mail']."</td>
		</tr>
		<tr>
		<th>手機號碼</th>
		<td>".$row['手機號碼']."</td>
		</tr>
		<tr>
		<th>郵遞區號＋地址</th>
		<td>".$row['郵遞區號＋地址']."</td>
		</tr>
		<tr>
		<th>全家店到店 店名+地址 </th>
		<td>".$row['全家店到店服務代號']."</td>
		</tr>
		<tr>
		<th>寄送方式 </th>
		<td>".$row['寄送方式']."</td>
		</tr>
		</table>";
		
	echo $MemberInformation;
	
	echo $toShippingTable;
	
	$conn->close();
 	
	function GetFBAccount($fb)
	{
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
		return $userNode->getName();
	}
}
?>
</body>
</html>