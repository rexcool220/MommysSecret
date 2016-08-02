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
	<title>Mommy's Secret + File</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
	
	<script>
	function validateForm() {
	    var MemberName = document.forms["MemberInformationForm"]["MemberName"].value;
	    var fbAccount = document.forms["MemberInformationForm"]["fbAccount"].value;
	    var EMail = document.forms["MemberInformationForm"]["EMail"].value;
	    var PhoneNumber = document.forms["MemberInformationForm"]["PhoneNumber"].value;
	    var Address = document.forms["MemberInformationForm"]["Address"].value;
	    var FamilyNumber = document.forms["MemberInformationForm"]["FamilyNumber"].value;
	    var ShippingWay = document.forms["MemberInformationForm"]["ShippingWay"].value;
	    var ShippingFee = document.forms["MemberInformationForm"]["ShippingFee"].value;
	    var AgentAccount = document.forms["MemberInformationForm"]["AgentAccount"].value;
	    
	    if (MemberName == null || MemberName == "" || 
	    		fbAccount == null || fbAccount == "" || 
	    	    EMail == null || EMail == "" ||
	    	    PhoneNumber == null || PhoneNumber == "" ||
	    	    Address == null || Address == "" ||
	    	    FamilyNumber == null || FamilyNumber == "" ||
	    	    ShippingWay == null || ShippingWay == "" ||
	    	    ShippingFee == null || ShippingFee == "")
	    {
	        alert("請檢查欄位");
	        return false;
	    }

	    if((ShippingWay == "合併寄貨") && ((AgentAccount == null) || (AgentAccount == "")))
	    {
	    	alert("合併寄貨需填寫合併寄送人帳號");
	        return false;
	    }
	}
	function myFunction() {
	    var shippingFee = 0;
		if(document.getElementById("ShippingWayId").value == "店到店")
		{
			shippingFee = 60;
		}
		else if(document.getElementById("ShippingWayId").value == "貨運")
		{
			shippingFee = 90;
		}
		else if(document.getElementById("ShippingWayId").value == "Zoo")
		{
			shippingFee = 20;
		}
		else if(document.getElementById("ShippingWayId").value == "Bon Vivant")
		{
			shippingFee = 20;
		}
		else if(document.getElementById("ShippingWayId").value == "印不停")
		{
			shippingFee = 20;
		}
		else if(document.getElementById("ShippingWayId").value == "合併寄貨")
		{
			shippingFee = 0;
		}
	    
	    document.getElementById("ShippingFeeId").value = shippingFee;
	}
	$( function()
	{
		$( document ).tooltip(
			{
	            position:{
	                at:"right+100% bottom ",
	                my:"right top",
	            }
	        }
		);
	} );
	</script>
	<style>
		select {
		    width: 100%;
		    box-sizing: border-box;
		    border: 2px solid #ccc;
		    border-radius: 4px;
		    font-size: 16px;
		    background-color: white;
		    background-position: 10px 10px;
		    background-repeat: no-repeat;
		    padding: 12px 20px 12px 12px;
	    }
	    label {
			display: inline-block;
		    width: 5em;
	  	}
		.ui-tooltip {
		    background: #ffffcc;
		}	  	
	</style>
</head>
<body>
<?php 
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
 	$fbAccount = GetFBAccount($fb);
 	
 	$sql = "SELECT * FROM `Members` WHERE FB帳號  = '$fbAccount';";
 	$result = mysql_query($sql,$con);
 	
 	if (!$result) {
 		die('Invalid query: ' . mysql_error());
 	}
 	
 	$row = mysql_fetch_array($result);

 	$MemberInformation = "
	<form name=\"MemberInformationForm\" action=\"MemberInformationCallBack.php\" onsubmit=\"return validateForm()\" method=\"get\">
   	<input type=\"hidden\" name=\"act\" value=\"run\">
	<table id=\"Member\">
    <tr>
		<th>FB帳號</th> 			
	    <td>
				<input type=\"text\" name=\"fbAccount\" readonly=\"readonly\" value=\"".$fbAccount."\"style=\"width:100%;\">
	    </td>	
	</tr>	    	    		
	<tr>
		<th>姓名(*必填)</th>
	    <td>
			<input type=\"text\" name=\"MemberName\" value=\"".$row['姓名']."\"style=\"width:100%;\">	    	    		
	    </td>			
	</tr>
    <tr>
		<th>E-Mail(*必填)</th> 
	    <td>
			<input type=\"text\" name=\"EMail\" title=\"請填寫登錄FB的Mail，因為本名或是FB帳號有可能重複，請留Mail方便我們再次確認喔!\" value=\"".$row['E-Mail']."\"style=\"width:100%;\">
	    </td>	
	</tr>
    <tr>
		<th>手機號碼(*必填)</th>       		
	    <td>
			<input type=\"text\" name=\"PhoneNumber\" value=\"".$row['手機號碼']."\"style=\"width:100%;\">
	    </td>	
	</tr>
	<tr>		
		<th>郵遞區號＋地址(*必填)</th>         		
	    <td>
			<input type=\"text\" name=\"Address\" title=\"請務必寫郵遞區號，如104Ｘ縣市Ｘ區ＸＸ路Ｘ段Ｘ號Ｘ樓\" value=\"".$row['郵遞區號＋地址']."\"style=\"width:100%;\">
	    </td>	
	</tr>
	<tr>			
		<th>全家店到店服務代號(*必填)<br><a target=\"_blank\" href=\"http://www.famiport.com.tw/shop.asp\">http://www.famiport.com.tw/shop.asp</a></th>         		
				
	    <td>
			<input type=\"text\" name=\"FamilyNumber\" title=\"全家店到店服務代號（5碼）＋店名  ex:10833全家文化店\" value=\"".$row['全家店到店服務代號']."\"style=\"width:100%;\">
	    </td>	
	</tr>
	<tr>			
		<th>寄送方式(*必填)</th>         		
	    <td>
			<select id=\"ShippingWayId\" onchange=\"myFunction()\" name=\"ShippingWay\">
	    		<option selected>".$row['寄送方式']."</option>
				<option value=\"店到店\">店到店</option>
				<option value=\"貨運\">貨運</option>
				<option value=\"Zoo\">Zoo</option>
				<option value=\"Bon Vivant\">Bon Vivant</option>
				<option value=\"印不停\">印不停</option>
	    		<option value=\"合併寄貨\">合併寄貨</option>
			</select>
	    </td>	        		
	</tr>
		<tr>			
		<th>運費</th>         		
	    <td>
			<input type=\"text\" id=\"ShippingFeeId\" name=\"ShippingFee\" readonly=\"readonly\" value=\"".$row['運費']."\"style=\"width:100%;\">
	    </td>	
	</tr>
	<tr>			
		<th>合併寄送帳號(請合併出貨姊妹的同意喔!)</th>         		
	    <td>
  			<input id=\"AgentAccount\" type=\"text\" name=\"AgentAccount\" title=\"合併寄送指由<<同一個人匯款收貨>>，XXX會幫我匯款收貨(請留XXX的FB帳號)謝謝喔!\" value=\"".$row['合併寄送人帳號']."\"style=\"width:100%;\">
	    </td>	
	</tr> 	 			
	<tr>			
		<th>備註</th>         		
	    <td>
			<input type=\"text\" name=\"Memo\" value=\"".$row['備註']."\"style=\"width:100%;\">
	    </td>	
	</tr>
		
	</table>
 	<input type=\"submit\" value=\"確認\">
 	</form>";
 	
 	if (!empty($_GET['act'])) {
 		$MemberName = $_GET['MemberName'];
 		$EMail = $_GET['EMail'];
 		$PhoneNumber = $_GET['PhoneNumber'];
 		$Address = $_GET['Address'];
 		$FamilyNumber = $_GET['FamilyNumber'];
 		$ShippingWay = $_GET['ShippingWay'];
		$ShippingFee = $_GET['ShippingFee'];
		$Memo = $_GET['Memo'];
		$AgentAccount = $_GET['AgentAccount'];
 		
 		$sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `E-Mail`, `手機號碼`, `郵遞區號＋地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`, `合併寄送人帳號`)
 		VALUES (\"$MemberName\", \"$fbAccount\", \"$EMail\", \"$PhoneNumber\", \"$Address\", \"$FamilyNumber\", \"$ShippingWay\", \"$ShippingFee\", \"$Memo\" , \"$AgentAccount\")
 		ON DUPLICATE KEY UPDATE `姓名`=\"$MemberName\", `E-Mail`=\"$EMail\", `手機號碼`=\"$PhoneNumber\", `郵遞區號＋地址`=\"$Address\",`全家店到店服務代號`=\"$FamilyNumber\", `寄送方式`=\"$ShippingWay\", `運費`=\"$ShippingFee\", `備註`=\"$Memo\", `合併寄送人帳號`=\"$AgentAccount\"";		
 		$result = mysql_query($sql,$con);
 		echo $sql;
 		if (!$result) {
 			echo $sql;
 			echo "<br>";
 			die('Invalid query2: ' . mysql_error());
 		}
 		
 		header("location: http://mommyssecret.tw/MemberInformationCallBack.php");
 	}
 	else
 	{
 	?>
 		<script>
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
 			    
 			    $( "#AgentAccount" ).autocomplete({
 			      source: availableAccount
 			    });
 			  } ); 	
		  </script>	
 		<?php
  		echo $MemberInformation;
 	}
 	?>
 	<?php
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

	