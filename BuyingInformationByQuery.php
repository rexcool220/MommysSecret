<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>PaymentProcess</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="MommysSecret.css?20160825">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>  
	<script>
	$( function() {
		$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });    	
	} );
	function validateMemberForm() {
	    var MemberName = document.forms["MemberInformationForm"]["MemberName"].value;
	    var fbAccount = document.forms["MemberInformationForm"]["fbAccount"].value;
	    var EMail = document.forms["MemberInformationForm"]["EMail"].value;
	    var PhoneNumber = document.forms["MemberInformationForm"]["PhoneNumber"].value;
	    
	    if (MemberName == null || MemberName == "" || 
	    		fbAccount == null || fbAccount == "" || 
	    	    EMail == null || EMail == "" ||
	    	    PhoneNumber == null || PhoneNumber == "")
	    {
	        alert("請檢查欄位");
	        return false;
	    }
	}
	
	function validateShippingForm() {
	    var Address = document.forms["AddressInformationForm"]["Address"].value;
	    var FamilyNumber = document.forms["AddressInformationForm"]["FamilyNumber"].value;
	    var ShippingWay = document.forms["AddressInformationForm"]["ShippingWay"].value;
	    var ShippingFee = document.forms["AddressInformationForm"]["ShippingFee"].value;
	    var AgentAccount = document.forms["AddressInformationForm"]["AgentAccount"].value;
	    
	    if (Address == null || Address == "" ||
	    	    FamilyNumber == null || FamilyNumber == "" ||
	    	    ShippingWay == null || ShippingWay == "" ||
	    	    ShippingFee == null || ShippingFee == "" ||
	    	    Address == null || Address == "")
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

	
	function validateRemitForm() {
	    var remitLastFiveDigit = document.forms["RemitForm"]["remitLastFiveDigit"].value;
	    var remitAmont = document.forms["RemitForm"]["remitAmont"].value;
	    
	    if (remitLastFiveDigit == null || remitLastFiveDigit == "" ||
	    		remitAmont == null || remitAmont == "")
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
	</script>
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
				window.history.replaceState( {} , 'PaymentProcess', 'http://mommyssecret.tw/BuyingInformationByQuery.php' );
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
	    	 
//         echo $userNode->getId();	
	}
	else
	{
	    echo "$fbAccount : 你不是管理者";
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
	echo "<br><div class=\"container\"><form method=\"post\" action=\"\">
			<input id=\"AvailibleAccount\" type=\"text\" value=\"\" name=\"memberFBAccountID\" class=\"FBSearch\" placeholder=\"FB帳號\"><p>
			<input type=\"submit\" value=\"查詢\"><p>
			</form></div>";
	
	
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
	    $memberFBID = $memberFBAccountIDArray[1];
	    
	    $sql = "Select * from Members where FBID = '$memberFBID'";
	    $result = mysql_query($sql,$con);
	    
	    if (!$result) {
	        die('Invalid query: ' . mysql_error());
	    }
	    
	    $row = mysql_fetch_array($result);
	    
	    $memberFBID = $row['FBID'];
	    
    	
        if (isset($_POST['CheckOut'])) {
            $remitLastFiveDigit = $_POST['remitLastFiveDigit'];
            $remitAmont = $_POST['remitAmont'];
            $memo = $_POST['memo'];
            $moneyToBePaid = $_POST['moneyToBePaid'];
            	
            if ($remitLastFiveDigit == "")
            {
                echo "<script type='text/javascript'>alert('remitLastFiveDigit')</script>";
            }
            elseif ($remitAmont == "")
            {
                echo "<script type='text/javascript'>alert('remitAmont')</script>";
            }
            else
            {		    
        	    $sql = "INSERT INTO  `RemitRecord` (`匯款編號` ,`匯款末五碼` ,`匯款日期` ,`Memo` ,`已收款` ,`匯款金額` ,`FB帳號` ,`FBID` ,`應匯款金額`)
        	    VALUES (NULL , '$remitLastFiveDigit', CURDATE(), '$memo', '0', '$remitAmont', '$memberFBAccount', '$memberFBID' ,'$moneyToBePaid');";
        	    $result = mysql_query($sql,$con);
        	    	
        	    if (!$result) {
        	        die('Invalid query: ' . mysql_error());
        	    }
        	    	
        	    $sql = "UPDATE `ShippingRecord` SET `匯款日期` = CURDATE(), `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord)  WHERE FBID = '$memberFBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL)";
        	    $result = mysql_query($sql,$con);
        	    if (!$result) {
        	        die('Invalid query: ' . mysql_error());
        	    }
         		header("location: http://mommyssecret.tw/BuyingInformationByQuery.php");
            }		
        }
        ?>
        <div class="container">
          <h2>訂單列表</h2>
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><span style="font-size: 200%">會員資料</span></a></li>
            <li><a data-toggle="tab" href="#menu1" class="inactiveLink"><span style="font-size: 200%">購買清單</span></a></li>
            <li><a data-toggle="tab" href="#menu2" class="inactiveLink"><span style="font-size: 200%">寄送地址</span></a></li>
            <li><a data-toggle="tab" href="#menu3"class="inactiveLink"><span style="font-size: 200%">結帳</span></a></li>
          </ul>
        
          <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br>
        		<?php 
            		if (isset($_POST['ModifyMember'])) {
            		    $MemberName = $_POST['MemberName'];
            		    $EMail = $_POST['EMail'];
            		    $PhoneNumber = $_POST['PhoneNumber'];
            		    
            		    if(($MemberName == "")||($EMail == "")||($PhoneNumber == ""))
            		    {
            		        echo "<script type='text/javascript'>alert('請檢查欄位')</script>";
            		    }
            		    else
            		    {
                		    $sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `E-Mail`, `手機號碼`, `FBID`)
                		    VALUES (\"$MemberName\", \"$memberFBAccount\", \"$EMail\", \"$PhoneNumber\", \"$memberFBID\")
                		    ON DUPLICATE KEY UPDATE `姓名`=\"$MemberName\", `FB帳號`=\"$memberFBAccount, `E-Mail`=\"$EMail\", `手機號碼`=\"$PhoneNumber\"";
                		
                		    $result = mysql_query($sql,$con);
                		    if (!$result) {
                		        echo $sql;
                		        echo "<br>";
                		        die('Invalid query2: ' . mysql_error());
                		    }
                		    	
                		    //header("location: http://mommyssecret.tw/MemberInformationCallBack.php");
                		    ?>
             			 		<script>
             			 			alert("更改成功");
             			 		</script>
            		 		<?php
            		    }
        		 	}			
        			$sql = "SELECT * FROM `Members` WHERE FBID  = '$memberFBID';";
        		 	$result = mysql_query($sql,$con);
        		 	
        		 	if (!$result) {
        		 		die('Invalid query: ' . mysql_error());
        		 	}
        		 	
        		 	$row = mysql_fetch_array($result);
        
        		 	$MemberInformation = "
        			<form name=\"MemberInformationForm\" action=\"BuyingInformationByQuery.php\" onsubmit=\"return validateMemberForm()\" method=\"POST\">
        		   	<input type=\"hidden\" name=\"ModifyMember\" value=\"run\">
        			<table id=\"Member\">
        		    <tr>
        				<th>FB帳號</th> 			
        			    <td>
        						<input type=\"text\" name=\"fbAccount\" readonly=\"readonly\" value=\"".$memberFBAccount."\"style=\"width:300px;\">
        			    </td>	    				
        			</tr>	    
        		    <tr>
        				<th>FBID</th> 			
        			    <td>
        						<input type=\"text\" name=\"FBID\" readonly=\"readonly\" value=\"".$memberFBID."\"style=\"width:300px;\">
        			    </td>	    				
        			</tr>		    
        			<tr>
        				<th>真實姓名<font color=\"red\">*</font></th>
        			    <td>
        					<input type=\"text\" name=\"MemberName\" value=\"".$row['姓名']."\"style=\"width:300px;\">	    	    		
        			    </td>			
        			</tr>
        		    <tr>
        				<th>E-Mail<font color=\"red\">*</font></th> 
        			    <td>
        					<input type=\"text\" name=\"EMail\" title=\"請填寫登錄FB的Mail，因為本名或是FB帳號有可能重複，請留Mail方便我們再次確認喔!\" value=\"".$row['E-Mail']."\"style=\"width:300px;\">
        			    </td>	
        			</tr>
        		    <tr>
        				<th>手機號碼<font color=\"red\">*</font></th>       		
        			    <td>
        					<input type=\"text\" name=\"PhoneNumber\" value=\"".$row['手機號碼']."\"style=\"width:300px;\">
        			    </td>	
        			</tr>
        			</table>			
        		 	<input type=\"submit\" value=\"修改個人資料\">
        		 	</form>";
        		 	
                    echo $MemberInformation;
                    ?>
            </div>
            <div id="menu1" class="tab-pane fade"><br>
        		<?php 
        			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL);";
        			
        			$result = mysql_query($sql,$con);
        			
        			if (!$result) {
        				die('Invalid query: ' . mysql_error());
        			}
        			$toRemitTableCount = mysql_num_rows($result);
        			
        			$toRemitTable = "<table>
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
        				$toRemitTable = $toRemitTable . "<td>" . $row['FBID'] . "</td>";
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
        			
        			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$memberFBID') AND 出貨日期 = '0000-00-00';";
        			//$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND 出貨日期 = '0000-00-00';";
        			
        			$result = mysql_query($sql,$con);
        			
        			if (!$result) {
        			    die('Invalid query: ' . mysql_error());
        			}
        			
        			$remitedTableCount = mysql_num_rows($result);
        			
        			$remitedTable = "<table>
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
        			    $remitedTable = $remitedTable . "<td>" . $row['FBID'] . "</td>";
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
        			
        			
        			
        			
        			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND `匯款編號` <> (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$memberFBID') AND 出貨日期 = '0000-00-00' AND 匯款日期 <> '0000-00-00';";
        			//$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND 出貨日期 = '0000-00-00';";
        				
        			$result = mysql_query($sql,$con);
        				
        			if (!$result) {
        			    die('Invalid query: ' . mysql_error());
        			}
        				
        			$waitShippingCount = mysql_num_rows($result);
        				
        			$waitShipping = "<table>
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
        			    $waitShipping = $waitShipping . "<tr>";
        			    $waitShipping = $waitShipping . "<td>" . $row['SerialNumber'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['FB帳號'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['FBID'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['品項'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['單價'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['數量'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $subTotal . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['匯款日期'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $isReceivedPayment . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['出貨日期'] . "</td>";
        			    $waitShipping = $waitShipping . "<td>" . $row['匯款編號'] . "</td>";
        			    $waitShipping = $waitShipping . "</tr>";
        			}
        			$waitShipping = $waitShipping . "</table>";
        			
        			
        			
        			
        			if($toRemitTableCount > 0)
        			{
            			echo "<h3>待匯款清單</h3>";
                        echo $toRemitTable;
        			}
        			if($remitedTableCount > 0)
        			{
                        echo "<h3>待出貨清單</h3>";
                        echo $remitedTable;
        			}
        			if($waitShippingCount > 0)
        			{
                        echo "<h3>上期未出貨清單</h3>";
                        echo $waitShipping;
        			}
                    
        		?>
            </div>
            <div id="menu2" class="tab-pane fade"><br>
        		<?php 
            		if (isset($_POST['ModifyAddress'])) {
            		    $Address = $_POST['Address'];
            		    $FamilyNumber = $_POST['FamilyNumber'];
            		    $ShippingWay = $_POST['ShippingWay'];
            		    $ShippingFee = $_POST['ShippingFee'];
            		    $Memo = $_POST['Memo'];
            		    $AgentAccount = $_POST['AgentAccount'];    		    
            		    
        		        if ($Address == "")
        		        {
        		            echo "<script type='text/javascript'>alert('Address')</script>";
        		        }
        		        elseif ($FamilyNumber == "")
        		        {
        		            echo "<script type='text/javascript'>alert('FamilyNumber')</script>";
        		        }
        		        elseif ($ShippingWay == "")
        	            {
        	                echo "<script type='text/javascript'>alert('ShippingWay')</script>";
        		        }
        		        elseif ($ShippingFee == "")
        		        {
        		            echo "<script type='text/javascript'>alert('ShippingFee')</script>";
        		        }
        		        elseif (($ShippingWay == "合併寄貨") && ($AgentAccount == ""))
        		        {
        		            echo "<script type='text/javascript'>alert('AgentAccount')</script>";
        		        }
        		        else 
        		        {
                		    $sql = "INSERT INTO `Members` (`FB帳號`, `FBID`, `郵遞區號＋地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`, `合併寄送人帳號`)
                		    VALUES (\"$memberFBAccount\", \"$memberFBID\", \"$Address\", \"$FamilyNumber\", \"$ShippingWay\", \"$ShippingFee\", \"$Memo\" , \"$AgentAccount\")
                		    ON DUPLICATE KEY UPDATE `FB帳號`=\"$memberFBAccount\",`郵遞區號＋地址`=\"$Address\",`全家店到店服務代號`=\"$FamilyNumber\", `寄送方式`=\"$ShippingWay\", `運費`=\"$ShippingFee\", `備註`=\"$Memo\", `合併寄送人帳號`=\"$AgentAccount\"";
                		
                		    $result = mysql_query($sql,$con);
                		    if (!$result) {
                		        echo $sql;
                		        echo "<br>";
                		        die('Invalid query2: ' . mysql_error());
                		    }
                		    	
                		    //header("location: http://mommyssecret.tw/MemberInformationCallBack.php");
                		    ?>
             			 		<script>
             			 			alert("更改成功");
             			 			$('.nav-tabs a[href="#menu2"]').tab('show')
             			 		</script>
            		 		<?php
        		        }
        		 	}
        	 	?>
        		 	<?php
        			$sql = "SELECT * FROM `Members` WHERE FBID  = '$memberFBID';";
        		 	$result = mysql_query($sql,$con);
        		 	
        		 	if (!$result) {
        		 		die('Invalid query: ' . mysql_error());
        		 	}
        		 	
        		 	$row = mysql_fetch_array($result);
        
        		 	$ShippingInformation = "
        			<form name=\"AddressInformationForm\" action=\"BuyingInformationByQuery.php\" onsubmit=\"return validateShippingForm()\" method=\"POST\">
        		   	<input type=\"hidden\" name=\"ModifyAddress\" value=\"run\">
        			<table id=\"Address\">
        		    <tr>		
            		<th>郵遞區號＋地址<font color=\"red\">*</font></th>         		
            	    <td>
            			<input type=\"text\" name=\"Address\" title=\"請務必寫郵遞區號，如104Ｘ縣市Ｘ區ＸＸ路Ｘ段Ｘ號Ｘ樓\" value=\"".$row['郵遞區號＋地址']."\"style=\"width:300px;\">
            	    </td>	
                	</tr>    		
                	<tr>			
                		<th>全家店到店 店名+地址<font color=\"red\">*</font><br><a target=\"_blank\" href=\"http://www.famiport.com.tw/shop.asp\">http://www.famiport.com.tw/shop.asp</a></th>         		
                				
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
                			<input type=\"text\" id=\"ShippingFeeId\" name=\"ShippingFee\" readonly=\"readonly\" value=\"".$row['運費']."\"style=\"width:300px;\">
                	    </td>	
                	</tr>
                	<tr>			
                		<th>合併寄送帳號(請先徵求合併出貨姊妹的同意喔!)</th>         		
                	    <td>
                  			<input id=\"AgentAccount\" type=\"text\" name=\"AgentAccount\" title=\"合併寄送指由<<同一個人匯款收貨>>，XXX會幫我匯款收貨(請留XXX的FB帳號)謝謝喔!\" value=\"".$row['合併寄送人帳號']."\"style=\"width:300px;\">
                	    </td>	
                	</tr> 	 			
                	<tr>			
                		<th>備註</th>         		
                	    <td>
                			<input type=\"text\" name=\"Memo\" value=\"".$row['備註']."\"style=\"width:300px;\">
                	    </td>	
                	</tr>
            		</table>			
            	 	<input type=\"submit\" value=\"修改寄送資訊\">
            	 	</form>";
        		 	
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
        		 	
                    echo $ShippingInformation;
                    ?>
            </div>
            <div id="menu3" class="tab-pane fade"><br>
        		<?php
        		
        		$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL);";
        			
        		$result = mysql_query($sql,$con);
        			
        		if (!$result) {
        		    die('Invalid query: ' . mysql_error());
        		}
        		$toRemitTableCount = mysql_num_rows($result);
        			
        		$toRemitTable = "<table>
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
        		    $toRemitTable = $toRemitTable . "<td>" . $row['FBID'] . "</td>";
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
        			
        		$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$memberFBID' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$memberFBID');";
        			
        		$result = mysql_query($sql,$con);
        			
        		if (!$result) {
        		    die('Invalid query: ' . mysql_error());
        		}
        			
        		$remitedTable = "<table>
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
        		    $remitedTable = $remitedTable . "<tr>";
        		    $remitedTable = $remitedTable . "<td>" . $row['SerialNumber'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['FB帳號'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['FBID'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['品項'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['單價'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['數量'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $subTotal . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['匯款日期'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $isReceivedPayment . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['出貨日期'] . "</td>";
        		    $remitedTable = $remitedTable . "<td>" . $row['匯款編號'] . "</td>";
        		    $remitedTable = $remitedTable . "</tr>";
        		    $totalPrice = $totalPrice + $subTotal;
        		}
        		$remitedTable = $remitedTable . "</table>";
        			
        		$sql = "SELECT * FROM `Members` WHERE FBID  = '$memberFBID';";
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
        		
        
        	    if($toRemitTableCount > 0)
        	    {
        	        echo '<b><font size="6">';
        	        echo "購買金額 : $totalPrice + 運費 : $actualShippingFee = 合計匯款金額 : $moneyToBePaid<br>";
        	        echo '</font></b>';
        	        
        	        echo '<b><font size="4">';
        	        echo "匯款帳號:郵局戶名:謝昀臻<br>
        			                    帳號:0002015-0385639";
        	        echo '</font></b>';
        	        
        	        echo '<hr align="left" width="1200px" color="#000000" size="4" />';
        	        echo $toRemitTable;
        	        ?>
        		 		<form name="RemitForm" action="BuyingInformationByQuery.php" onsubmit="return validateRemitForm()" method="POST">
        		 			<input type="hidden" name="CheckOut" value="run">
        		 			<input type="hidden" value="<?php echo $memberFBID;?>" name="fbAccount">
        		 			<input type="hidden" value="<?php echo $moneyToBePaid;?>" name="moneyToBePaid">
        				  	<p><input type="text" name="remitLastFiveDigit" placeholder="匯款末五碼"></p>
        				  	<p><input type="text" name="remitAmont" placeholder="匯款金額"></p>
        				  	<p><input type="text" name="memo" placeholder="Memo"></p>
        				  	<input type="submit" value="回報匯款">
        		 		</form>
         			<?php
         			
         		}
         		else 
         		{
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
         		    echo '<b><font size="6">';
         		    echo "已收到您的資料待對帳<br>";
         		    echo "購買金額 : $totalPrice + 運費 : $actualShippingFee = 合計匯款金額 : $moneyToBePaid<br>";
         		    echo '</font></b>';         		    
             		
            	 	echo $remitedTable;
         		}
        		?>
            </div>    
          </div>
        </div>
    <?php
	} 
    ?>
</body>
</html>
