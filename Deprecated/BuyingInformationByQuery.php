<?php 
require_once __DIR__ . '/vendor/autoload.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
<link rel="stylesheet" type="text/css" href="MommysSecret.css">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>購買清單</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
	$( function() {
		$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });    	
	} );	
</script>	
<title>MommysSecret</title>

</head>

<body>

<?php
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
			'app_id' => '1540605312908660',
			'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
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
		window.history.replaceState( {} , 'BuyingInformationByQuery', 'http://mommyssecret.tw/BuyingInformationByQuery.php' );
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
// 	 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}	

	include('ConnectMySQL.php');
	
	if (!empty($_POST['act']))
	{
		$remitDate = $_POST['remiteDate'];
		$remitLastFiveDigit = $_POST['remitLastFiveDigit'];
		$remitAmont = $_POST['remitAmont'];
		$memo = $_POST['memo'];
		$memberFBAccount = $_POST['memberFBAccount'];
	
		$sql = "INSERT INTO  `RemitRecord` (`匯款編號` ,`匯款末五碼` ,`匯款日期` ,`Memo` ,`已收款` ,`匯款金額` ,`FB帳號`)
		VALUES (NULL ,  '$remitLastFiveDigit',  '$remitDate',  '$memo',  '0',  '$remitAmont',  '$memberFBAccount');";
		$result = mysql_query($sql,$con);
	
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	
		$sql = "UPDATE `ShippingRecord` SET `匯款日期` = '$remitDate', `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord)  WHERE FB帳號 = '$memberFBAccount' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL) ";
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		header("location: http://mommyssecret.tw/BuyingInformationByQuery.php");
	}
	else
	{
		?>
	<script type="text/javascript">
	$( function() {
		var availableAccount =
		<?php
		$sql = "SELECT FB帳號 FROM `Members`;";
		$result = mysql_query($sql,$con);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		while($rows[]=mysql_fetch_array($result));
		$prefix = '';
		foreach ($rows as $r)
		{
			$AcountList .= $prefix . '"' . $r[FB帳號] . '"';
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
	echo "<form method=\"post\" action=\"\">
			<input id=\"AvailibleAccount\" type=\"text\" value=\"\" name=\"memberFBAccount\" class=\"FBSearch\" placeholder=\"FB帳號\"><p>
			<input type=\"submit\" value=\"查詢\"><p>
			</form>";
	if(isset($_POST['memberFBAccount']) || isset($_SESSION['memberFBAccount']))
	{
		if(isset($_POST['memberFBAccount']))
		{
			$_SESSION['memberFBAccount'] = $_POST['memberFBAccount'];
			$memberFBAccount = $_POST['memberFBAccount'];
		}
		elseif (isset($_SESSION['memberFBAccount']))
		{
			$memberFBAccount = $_SESSION['memberFBAccount'];
		}
		
		$sql = "SELECT * FROM `ShippingRecord` WHERE FB帳號 = '$memberFBAccount' AND (匯款日期 = '0000-00-00' || 匯款日期  is NULL);";
		
		$result = mysql_query($sql,$con);
		
		if (!$result)
		{
			die('Invalid query: ' . mysql_error());
		}
		$toRemitTableCount = mysql_num_rows($result);
		
		$toRemitTable = "<table>
		<tr>
	  	<th>SN</th>
		<th>FB帳號 </th>
		<th>品項</th>
		<th>單價</th>
		<th>數量</th>
		<th>金額</th>
		<th>匯款日期</th>
		<th>確認收款</th>
		<th>出貨日期</th>
	  	<th>匯款編號</th>
		</tr>";
		$totalPrice = 0;
		while($row = mysql_fetch_array($result))
		{
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
			$toRemitTable = $toRemitTable . "<tr>";
			$toRemitTable = $toRemitTable . "<td>" . $row['SerialNumber'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['FB帳號'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['品項'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['單價'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['數量'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $subTotal . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['匯款日期'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $isReceivedPayment . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['出貨日期'] . "</td>";
			$toRemitTable = $toRemitTable . "<td>" . $row['匯款編號'] . "</td>";
			$toRemitTable = $toRemitTable . "</tr>";
			$totalPrice = $totalPrice + $subTotal;
		}
		$toRemitTable = $toRemitTable . "</table>";
		
		$sql = "SELECT * FROM `ShippingRecord` WHERE FB帳號 = '$memberFBAccount' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FB帳號 = '$memberFBAccount');";
		
		$result = mysql_query($sql,$con);
		
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
	
		$remitedTableCount = mysql_num_rows($result);
		
		$remitedTable = "<table border='1'>
		<tr>
		<th>SN</th>
		<th>FB帳號 </th>
		<th>品項</th>
		<th>單價</th>
		<th>數量</th>
		<th>金額</th>
		<th>匯款日期</th>
		<th>確認收款</th>
		<th>出貨日期</th>
		<th>匯款編號</th>				
		</tr>";
		while($row = mysql_fetch_array($result))
		{
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
			$remitedTable = $remitedTable . "<tr>";
			$remitedTable = $remitedTable . "<td>" . $row['SerialNumber'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['FB帳號'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['品項'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['單價'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['數量'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $subTotal . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['匯款日期'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $isReceivedPayment . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['出貨日期'] . "</td>";
			$remitedTable = $remitedTable . "<td>" . $row['匯款編號'] . "</td>";
			$remitedTable = $remitedTable . "</tr>";
		}
		$remitedTable = $remitedTable . "</table>";
		
		$sql = "SELECT * FROM `Members` WHERE FB帳號  = '$memberFBAccount';";
		$result = mysql_query($sql,$con);
		
		if (!$result)
		{
			die('Invalid query: ' . mysql_error());
		}
		
		$row = mysql_fetch_array($result);
		if($totalPrice > 6000)
		{
			$shippingFee = 0;
		}
		else
		{
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
		<th>登入的FB帳號</th>
		<td>".$row['登入的FB帳號']."</td>
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
		if($toRemitTableCount > 0)
		{
			echo '<b><font size="6">';
			echo "購買金額 : $totalPrice + 運費 : $shippingFee = 合計匯款金額 : $moneyToBePaid";
			echo '</font></b>';
			echo '<hr align="left" width="1200px" color="#000000" size="4" />';
			echo $toRemitTable;
		?>
		<form action="BuyingInformationByQuery.php" method="post">
		  <input type="hidden" name="act" value="run">
		  <input type="hidden" value="<?php echo $memberFBAccount;?>" name="memberFBAccount">
		  <p><input type="text" name="remiteDate" id="datepicker" placeholder="匯款日期"></p>
		  <p><input type="text" name="remitLastFiveDigit" placeholder="匯款末五碼"></p>
		  <p><input type="text" name="remitAmont" placeholder="匯款金額"></p>
		  <p><input type="text" name="memo" placeholder="Memo"></p>
		  <input type="submit" value="確認已匯款">
		</form>
		<?php
		}
		else 
		{
			echo $remitedTable;
		}
	}
}
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

?>
</body>
</html>