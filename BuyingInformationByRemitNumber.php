<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'ConnectMySQL.php';
	header("Content-Type:text/html; charset=utf-8");
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="MommysSecret.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mommy管理者</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
	<script>
  $( function() {
    $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });    	
  } );
  </script>
</head>
<body>

<form method="get" action="">
	匯款編號：<input type="text" value="" name="remitNumber" style="width: 600px;"><p>
	<input type="submit" value="查詢"><p>
</form>
	
<?php	
if(!empty($_GET['remitNumber'])) {
	$remitNumber = $_GET['remitNumber'];
	$sql = "SELECT * FROM `ShippingRecord` WHERE 匯款編號 = '$remitNumber'";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$RemitTable = "<table border='1'>
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
		$fbAccount = $row['FB帳號'];
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
		$RemitTable = $RemitTable . "<tr>";
		$RemitTable = $RemitTable . "<td>" . $row['SerialNumber'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['FB帳號'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['品項'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['單價'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['數量'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $subTotal . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['匯款日期'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $isReceivedPayment . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['出貨日期'] . "</td>";
		$RemitTable = $RemitTable . "<td>" . $row['匯款編號'] . "</td>";
		$RemitTable = $RemitTable . "</tr>";
		$totalPrice = $totalPrice + $subTotal;
	}
	
	$RemitTable = $RemitTable . "</table>";
	
	$sql = "SELECT * FROM `Members` WHERE FB帳號  = '$fbAccount';";
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
		<th>姓名</th>
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
		<th>手機號碼</th>
		<td>".$row['手機號碼']."</td>
		</tr>
		<tr>
		<th>郵遞區號＋地址</th>
		<td>".$row['郵遞區號＋地址']."</td>
		</tr>
		<tr>
		<th>全家店到店服務代號 </th>
		<td>".$row['全家店到店服務代號']."</td>
		</tr>
		<tr>
		<th>寄送方式 </th>
		<td>".$row['寄送方式']."</td>
		</tr>
		</table>";

	$sql = "SELECT * FROM `RemitRecord` WHERE 匯款編號 = '$remitNumber'";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}	
	$row = mysql_fetch_array($result);
	
	$remitAmountTable = "<br><br><table width=60%>
		<tr>
		<th>應收</th>
		<td>".$moneyToBePaid."</td>
		</tr>
		<tr>
		<th>實收</th>
		<td>".($row['匯款金額'] + $row['PaidRebate'])."</td>
		</tr>
		<th>現金支付</th>
		<td>".$row['匯款金額']."</td>
		</tr>									
		<th>回饋金支付</th>
		<td>".$row['PaidRebate']."</td>
		</tr>					
		</table><br><br>";	
	
	echo $MemberInformation;
	
	echo $remitAmountTable;
	
	echo $RemitTable;
	
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
	