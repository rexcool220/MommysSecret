<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'ConnectMySQL.php';
	header("Content-Type:text/html; charset=utf-8");
?>
<html>
<head>
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
	FB帳號：<input type="text" value="" name="fbAccount" style="width: 600px;"><p>
	<input type="submit" value="查詢"><p>
</form>

</body>
</html>
	
<?php	
if(!empty($_GET['fbAccount'])) {
	$fbAccount = $_GET['fbAccount'];
	$sql = "SELECT * FROM `QueenieShippingRecord` WHERE FB帳號 = '$fbAccount' AND 匯款日期 = '0000-00-00';";
	
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	
	$table = "<table border='1'>
	<tr>
	<th>FB帳號 </th>
	<th>品項</th>
	<th>單價</th>
	<th>數量</th>
	<th>金額</th>
	<th>匯款日期</th>
	<th>出貨日期</th>
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
		$subTotal = $row['單價'] * $row['數量'];
		$table = $table . "<tr>";
		$table = $table . "<td>" . $row['FB帳號'] . "</td>";
		$table = $table . "<td>" . $row['品項'] . "</td>";
		$table = $table . "<td>" . $row['單價'] . "</td>";
		$table = $table . "<td>" . $row['數量'] . "</td>";
		$table = $table . "<td>" . $subTotal . "</td>";
		$table = $table . "<td>" . $row['匯款日期'] . "</td>";
		$table = $table . "<td>" . $row['出貨日期'] . "</td>";
		$table = $table . "</tr>";
		$totalPrice = $totalPrice + $subTotal;
	}
	$table = $table . "</table>";
	
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
	
	echo '姓名 :'.$row['姓名'].'<br>';
	echo 'FB帳號 :'.$row['FB帳號'].'<br>';
	echo '登入的FB帳號 :'.$fbAccount.'<br>';
	echo 'E-Mail : '.$row['E-Mail'].'<br>';
	echo '手機號碼 : '.$row['手機號碼'].'<br>';
	echo '郵遞區號地址 : '.$row['郵遞區號地址'].'<br>';
	echo '全家店到店服務代號 : '.$row['全家店到店服務代號'].'<br>';
	echo '寄送方式 : '.$row['寄送方式'].'<br>';
	echo '<b><font size="6">';
	echo "購買金額 : $totalPrice + 運費 : $shippingFee = 合計匯款金額 : $moneyToBePaid";
	echo '</font></b>';
	echo '<hr align="left" width="1200px" color="#000000" size="4" />';
	echo $table;
	if (!empty($_GET['act'])) {
		$remitDate = $_GET['date'];
		$sql = "UPDATE `QueenieShippingRecord` SET `匯款日期` = '$remitDate'  WHERE FB帳號 = '$fbAccount' AND 出貨日期 = '0000-00-00'";
		
		$result = mysql_query($sql,$con);
	
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		header("location: "."http://mommyssecret.tw/MS/BuyingInformationByQueryForQueenie.php?fbAccount=".$fbAccount);
		
	} else {
	
	?>
	<form action="BuyingInformationByQueryForQueenie.php" method="get">
	  <input type="hidden" name="act" value="run">
	  <input type="submit" value="確認已匯款">
	  <input type="hidden" value="<?php echo $fbAccount;?>" name="fbAccount">
	  <p>匯款日期: <input type="text" name="date" id="datepicker"></p>
	</form>
	<?php
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
}
?>
	