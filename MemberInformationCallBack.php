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
	<title>個人資料</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
	function validateForm() {
	    var MemberName = document.forms["MemberInformationForm"]["MemberName"].value;
	    var fbAccount = document.forms["MemberInformationForm"]["fbAccount"].value;
	    var PhoneNumber = document.forms["MemberInformationForm"]["PhoneNumber"].value;
	    var Address = document.forms["MemberInformationForm"]["Address"].value;
	    var FamilyNumber = document.forms["MemberInformationForm"]["FamilyNumber"].value;
	    var ShippingWay = document.forms["MemberInformationForm"]["ShippingWay"].value;
	    var ShippingFee = document.forms["MemberInformationForm"]["ShippingFee"].value;
	    
	    if (MemberName == null || MemberName == "" || 
	    		fbAccount == null || fbAccount == "" || 
	    	    PhoneNumber == null || PhoneNumber == "" ||
	    	    Address == null || Address == "" ||
	    	    FamilyNumber == null || FamilyNumber == "" ||
	    	    ShippingWay == null || ShippingWay == "" ||
	    	    ShippingFee == null || ShippingFee == "")
	    {
	        alert("請檢查欄位");
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
			shippingFee = 85;
		}
		else if(document.getElementById("ShippingWayId").value == "ZOo")
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
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
	    'app_id' => '198155157308846',
		'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
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
 			window.history.replaceState( {} , '個人資料', 'http://mommyssecret.tw/MS/MemberInformationCallBack.php' );
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
 	$FBID = $userNode->getId();
 	
 	if(isset($_SESSION['Completed']))
 	{
 		echo '<script language="javascript">';
		echo 'alert("已填寫成功")';
		echo '</script>';
 		unset($_SESSION['Completed']);
 		//header("location: https://www.facebook.com/MommySecret.Plan/?fref=ts");
 	}
 	
	$sql = "SELECT * FROM `Members` WHERE FBID  = '$FBID';";
 	$result = mysql_query($sql,$con);
 	
 	if (!$result) {
 		die('Invalid query: ' . mysql_error());
 	}
 	
 	$row = mysql_fetch_array($result);

 	$MemberInformation = "
	<form name=\"MemberInformationForm\" action=\"MemberInformationCallBack.php\" onsubmit=\"return validateForm()\" method=\"POST\">
   	<input type=\"hidden\" name=\"act\" value=\"run\">
	<table id=\"Member\">
	<tr>
		<th>FB帳號</th> 			
	    <td>
				<input type=\"text\" name=\"fbAccount\" readonly=\"readonly\" value=\"".$fbAccount."\"style=\"width:300px;\">
	    </td>	    				
	</tr>
    <tr>
		<th>FBID</th> 			
	    <td>
				<input type=\"text\" name=\"FBID\" readonly=\"readonly\" value=\"".$FBID."\"style=\"width:300px;\">
	    </td>	    				
	</tr>		    		
	<tr>
		<th>真實姓名<font color=\"red\">*</font></th>
	    <td>
			<input type=\"text\" name=\"MemberName\" value=\"".$row['姓名']."\"style=\"width:300px;\">	    	    		
	    </td>			
	</tr>
    <tr>
		<th>手機號碼<font color=\"red\">*</font></th>       		
	    <td>
			<input type=\"text\" name=\"PhoneNumber\" value=\"".$row['手機號碼']."\"style=\"width:300px;\">
	    </td>	
	</tr>
	<tr>		
		<th>郵遞區號地址<font color=\"red\">*</font></th>         		
	    <td>
			<input type=\"text\" name=\"Address\" title=\"請務必寫郵遞區號，如104Ｘ縣市Ｘ區ＸＸ路Ｘ段Ｘ號Ｘ樓\" value=\"".$row['郵遞區號地址']."\"style=\"width:300px;\">
	    </td>	
	</tr>    		
	<tr>			
		<th>全家店到店 店名地址<font color=\"red\">*</font><br><a target=\"_blank\" href=\"http://www.famiport.com.tw/shop.asp\">http://www.famiport.com.tw/shop.asp</a></th>         		
				
	    <td>
			<input type=\"text\" name=\"FamilyNumber\" title=\"ex:全家板橋松柏店 新北市板橋區松柏街13號\" value=\"".$row['全家店到店服務代號']."\"style=\"width:300px;\">
	    </td>	
	</tr>
	<tr>			
		<th>指定的寄送方式<font color=\"red\">*</font></th>         		
	    <td>
			<select id=\"ShippingWayId\" onchange=\"myFunction()\" name=\"ShippingWay\" style=\"width:300px;\">
	    		<option selected>".$row['寄送方式']."</option>
				<option value=\"店到店\">店到店</option>
				<option value=\"貨運\">貨運</option>
				<option value=\"ZOo\">ZOo</option>
				<option value=\"印不停\">印不停</option>
			</select>
	    </td>	        		
	</tr>
		<tr>			
		<th>運費</th>         		
	    <td>
			<input type=\"text\" id=\"ShippingFeeId\" name=\"ShippingFee\" readonly=\"readonly\" value=\"".$row['運費']."\"style=\"width:300px;\">
	    </td>	
	</tr>	 			
	<tr>			
		<th>備註</th>         		
	    <td>
			<input type=\"text\" name=\"Memo\" value=\"".$row['備註']."\"style=\"width:300px;\">
	    </td>	
	</tr>
	</table>			
 	<input type=\"submit\" value=\"確認\">
 	</form>";
 	
 	if (!empty($_POST['act'])) {
 		$MemberName = $_POST['MemberName'];
 		$PhoneNumber = $_POST['PhoneNumber'];
 		$Address = $_POST['Address'];
 		$FamilyNumber = $_POST['FamilyNumber'];
 		$ShippingWay = $_POST['ShippingWay'];
		$ShippingFee = $_POST['ShippingFee'];
		$Memo = $_POST['Memo'];
 		
 		$sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `FBID`, `手機號碼`, `郵遞區號地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`)
 		VALUES (\"$MemberName\", \"$fbAccount\", \"$FBID\", \"$PhoneNumber\", \"$Address\", \"$FamilyNumber\", \"$ShippingWay\", \"$ShippingFee\", \"$Memo\")
 		ON DUPLICATE KEY UPDATE `姓名`=\"$MemberName\", `FB帳號`=\"$fbAccount\", `手機號碼`=\"$PhoneNumber\", `郵遞區號地址`=\"$Address\",`全家店到店服務代號`=\"$FamilyNumber\", `寄送方式`=\"$ShippingWay\", `運費`=\"$ShippingFee\", `備註`=\"$Memo\"";
 		$result = mysql_query($sql,$con);
//  		echo $sql;
 		if (!$result) {
 			echo $sql;
 			echo "<br>";
 			die('Invalid query2: ' . mysql_error());
 		}
 		
 		$_SESSION['Completed'] = true;
 		
 		header("location: http://mommyssecret.tw/MS/MemberInformationCallBack.php");
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
 ?>

	