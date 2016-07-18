<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'ConnectMySQL.php';
	header("Content-Type:text/html; charset=utf-8");
	if(!session_id()) {
	    session_start();
	}
	
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
	
 	$fb->setDefaultAccessToken($accessToken);
 	$fbAccount = GetFBAccount($fb);
 	
 	if($_SESSION['personal'] == 'queenie')
 	{
		$sql = "SELECT * FROM `QueenieShippingRecord` WHERE FB帳號 = '$fbAccount';";
 	}
 	else
 	{
 		$sql = "SELECT * FROM `ShippingRecord` WHERE FB帳號 = '$fbAccount';";
 	}
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
	
	if($_SESSION['personal'] == 'queenie')
	{
		$threshold = 4000;
	}
	else
	{
		$threshold = 6000;
	}
	 
	if($totalPrice > $threshold)
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
	echo '郵遞區號＋地址 : '.$row['郵遞區號＋地址'].'<br>';
	echo '全家店到店服務代號 : '.$row['全家店到店服務代號'].'<br>';
	echo '寄送方式 : '.$row['寄送方式'].'<br>';
	echo '<b><font size="6">';
	
	if($_SESSION['personal'] == 'queenie')
	{
 		echo "匯款帳號:郵局(700)24415440015801<br>";
	}
	
	echo "購買金額 : $totalPrice + 運費 : $shippingFee = 合計匯款金額 : $moneyToBePaid";
	echo '</font></b>';
	echo '<hr align="left" width="1200px" color="#000000" size="4" />';
	echo $table;
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
	