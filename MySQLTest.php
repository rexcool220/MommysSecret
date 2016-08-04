<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'ConnectMySQL.php';
	header("Content-Type:text/html; charset=utf-8");
	if(!session_id()) {
	    session_start();
	}
?>
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
	function validateForm() 
	{
		    var remiteDate = document.forms["RemitForm"]["remiteDate"].value;
		    var remitLastFiveDigit = document.forms["RemitForm"]["remitLastFiveDigit"].value;
		    var remitAmont = document.forms["RemitForm"]["remitAmont"].value;
		    if (remiteDate == null || remiteDate == "" || remitLastFiveDigit == null || remitLastFiveDigit == "" || remitAmont == null || remitAmont == "")
		    {
		        alert("請檢查匯款日期、匯款金額、匯款末五碼都要填喔");
		        return false;
		    }
	}
	</script>
	
	
</head>
<body>
<?php 
	
 	$fbAccount = 'Orange Tai';
 	$sql = "SELECT * FROM `ShippingRecord` WHERE FB帳號 = '$fbAccount' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL);";
 	
 	$result = mysql_query($sql,$con);
 	
 	if (!$result) {
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
 	
 	$sql = "SELECT * FROM `ShippingRecord` WHERE FB帳號 = '$fbAccount' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FB帳號 = '$fbAccount');";
 	
 	$result = mysql_query($sql,$con);
 	
 	if (!$result) {
 		die('Invalid query: ' . mysql_error());
 	}
 	
 	$remitedTableCount = mysql_num_rows($result);
 	
 	$remitedTable = "<table>
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
 	
 	$sql = "SELECT * FROM `Members` WHERE FB帳號  = '$fbAccount';";
 	$result = mysql_query($sql,$con);
 	
 	if (!$result) {
 		die('Invalid query: ' . mysql_error());
 	}
 	
 	$row = mysql_fetch_array($result);
 	$name = $row['姓名'];
 	$eMail = $row['E-Mail'];
 	$phoneNumber = $row['手機號碼'];
 	$address = $row['郵遞區號＋地址'];
 	$familyNumber = $row['全家店到店服務代號'];
 	$shippingWay = $row['寄送方式'];
 	$shippingFee = $row['運費'];
 	$agentAccount = $row['合併寄送人帳號'];
 	$memberFlag = true;
 	
 	if(($name == null || $name == "") ||
 		($eMail == null || $eMail == "") ||
 		($phoneNumber == null || $phoneNumber == "") ||
 		($address == null || $address == "") ||
 		($familyNumber == null || $familyNumber == "") ||
 		($shippingWay == null || $shippingWay == "") ||
 		($shippingFee == null || $shippingFee == "") ||
 		(($shippingWay == "合併寄貨") && (($agentAccount == null) || ($agentAccount == ""))))
 	{
 		$memberFlag = false;//表示不要讓客戶匯款
 	}
 	
 	if($totalPrice > 6000)
 	{
 		$actualShippingFee = 0;
 	}
 	else {
 		$actualShippingFee = $shippingFee;
 	}
 	if($totalPrice == 0)
 	{
 		$moneyToBePaid = 0;
 		$actualShippingFee = 0;
 	}
 	else
 	{
 		$moneyToBePaid = $totalPrice + $actualShippingFee;
 	}
 	if (!empty($_POST['act'])) {
 		$remitDate = $_POST['remiteDate'];
 		$remitLastFiveDigit = $_POST['remitLastFiveDigit'];
 		$remitAmont = $_POST['remitAmont'];
 		$memo = $_POST['memo'];
 	
//  		$sql = "INSERT INTO  `RemitRecord` (`匯款編號` ,`匯款末五碼` ,`匯款日期` ,`Memo` ,`已收款` ,`匯款金額` ,`FB帳號` ,`應匯款金額`)
//  		VALUES (NULL ,  '$remitLastFiveDigit',  '$remitDate',  '$memo',  '0',  '$remitAmont',  '$fbAccount' ,'$moneyToBePaid');";
//  		$result = mysql_query($sql,$con);
 	
//  		if (!$result) {
//  			die('Invalid query: ' . mysql_error());
//  		}
 	
//  		$sql = "UPDATE `ShippingRecord` SET `匯款日期` = '$remitDate', `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord)  WHERE FB帳號 = '$fbAccount' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL)";
//  		$result = mysql_query($sql,$con);
//  		if (!$result) {
//  			die('Invalid query: ' . mysql_error());
//  		}
 		header("location: http://mommyssecret.tw/GetBuyingInformationCallBack.php");
 	}
 	else 
 	{
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
		<td>".$fbAccount."</td>
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
		<th>全家店到店服務代號 </th>
		<td>".$row['全家店到店服務代號']."</td>
		</tr>
		<tr>
		<th>寄送方式 </th>
		<td>".$row['寄送方式']."</td>
		</tr>
		<tr>
		<th>合併寄送人帳號 </th>
		<td>".$row['合併寄送人帳號']."</td>
		</tr> 				
		</table>
 		<input type=\"button\" value=\"修改個人資料\" onclick=\"window.open('http://mommyssecret.tw/MemberInformation.php')\" />
		<br><br>";
 		
 		echo $MemberInformation;	

 	
 		if($memberFlag == true)
 		{
	 		if($toRemitTableCount > 0)
	 		{
	 			echo '<b><font size="6">';
	 			echo "購買金額 : $totalPrice + 運費 : $actualShippingFee = 合計匯款金額 : $moneyToBePaid";
	 			echo '</font></b>';
	 			echo '<hr align="left" width="1200px" color="#000000" size="4" />';
	 			echo $toRemitTable;
	 			?>
			 		<form name="RemitForm" action="GetBuyingInformationCallBack.php" onsubmit="return validateForm()" method="get">
			 			<input type="hidden" name="act" value="run">
			 			<input type="hidden" value="<?php echo $fbAccount;?>" name="fbAccount">
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
 		else 
 		{
 			echo '<b><font size="6">';
 			echo "您的會員資料不完整，請填寫完所有欄位後再做匯款喔 :)";
 			echo '</font></b>';
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
	