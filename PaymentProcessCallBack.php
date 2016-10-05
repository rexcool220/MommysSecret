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
	<link rel="stylesheet" type="text/css" href="MommysSecret.css?20160905">
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
	    var PhoneNumber = document.forms["MemberInformationForm"]["PhoneNumber"].value;
	    
	    if (MemberName == null || MemberName == "" || 
	    		fbAccount == null || fbAccount == "" || 
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
	}

	
	function validateRemitForm(form) {
	    form.RemitButton.disabled = true;
	    form.RemitButton.value = "處理中...";
	    var remitLastFiveDigit = document.forms["RemitForm"]["remitLastFiveDigit"].value;
	    var remitAmont = document.forms["RemitForm"]["remitAmont"].value;
	    
	    if (remitLastFiveDigit == null || remitLastFiveDigit == "" ||
	    		remitAmont == null || remitAmont == "")
	    {
	        alert("請檢查欄位");
	        form.RemitButton.disabled = false;
	        form.RemitButton.value = "回報匯款";
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
				window.history.replaceState( {} , 'PaymentProcess', 'http://mommyssecret.tw/PaymentProcessCallBack.php' );
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
	if(isset($_SESSION["completed"]))
	{
	    echo "<script type='text/javascript'>alert('已收到您匯款資料，待對帳')</script>";
	    unset($_SESSION["completed"]);
	}
	
    if (isset($_POST['CheckOut']) && empty($_SESSION["completed"])) {
        $remitLastFiveDigit = $_POST['remitLastFiveDigit'];
        $remitAmont = $_POST['remitAmont'];
        $memo = $_POST['memo'];
        $moneyToBePaid = $_POST['moneyToBePaid'];
        $rebateWillBeUpdate = $_POST['rebateWillBeUpdate'];
        $rebateTobeDeduct = $_POST['rebateTobeDeduct'];
        	
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
    	    $sql = "INSERT INTO  `RemitRecord` (`匯款編號` ,`匯款末五碼` ,`匯款日期` ,`Memo` ,`已收款` ,`匯款金額` ,`FB帳號` ,`FBID` ,`應匯款金額`,`PaidRebate`)
    	    VALUES (NULL ,  '$remitLastFiveDigit',  CURDATE(),  '$memo',  '0',  '$remitAmont',  '$fbAccount', '$FBID' ,'$moneyToBePaid', $rebateTobeDeduct);";
    	    $result = mysql_query($sql,$con);
    	    if (!$result) {
    	        die('Invalid query: ' . mysql_error());
    	    }
    	    	
    	    $sql = "UPDATE `ShippingRecord` SET `匯款日期` = CURDATE(), `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord)  WHERE FBID = '$FBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL)";
    	    $result = mysql_query($sql,$con);
    	    if (!$result) {
    	        die('Invalid query: ' . mysql_error());
    	    }
    	    
    	    $sql = "UPDATE `Members` SET `Rebate` = '$rebateWillBeUpdate' WHERE FBID = '$FBID'";
    	    $result = mysql_query($sql,$con);
    	    if (!$result) {
    	    	die('Invalid query: ' . mysql_error());
    	    }
    	    
    	    
            $_SESSION["completed"] = true;   
     		header("location: http://mommyssecret.tw/PaymentProcessCallBack.php");
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
    		    $PhoneNumber = $_POST['PhoneNumber'];
    		    
    		    if(($MemberName == "")||($PhoneNumber == ""))
    		    {
    		        echo "<script type='text/javascript'>alert('請檢查欄位')</script>";
    		    }
    		    else
    		    {
        		    $sql = "INSERT INTO `Members` (`姓名`, `FB帳號`, `手機號碼`, `FBID`)
        		    VALUES (\"$MemberName\", \"$fbAccount\", \"$PhoneNumber\", \"$FBID\")
        		    ON DUPLICATE KEY UPDATE `姓名`=\"$MemberName\", `FB帳號`=\"$fbAccount\", `手機號碼`=\"$PhoneNumber\"";
        		
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
			$sql = "SELECT * FROM `Members` WHERE FBID  = '$FBID';";
		 	$result = mysql_query($sql,$con);
		 	
		 	if (!$result) {
		 		die('Invalid query: ' . mysql_error());
		 	}
		 	
		 	$row = mysql_fetch_array($result);

		 	$MemberInformation = "
			<form name=\"MemberInformationForm\" action=\"PaymentProcessCallBack.php\" onsubmit=\"return validateMemberForm()\" method=\"POST\">
		   	<input type=\"hidden\" name=\"ModifyMember\" value=\"run\">
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
				<th>真實中文姓名<font color=\"red\">*</font></th>
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
			</table>			
		 	<input type=\"submit\" value=\"修改個人資料\">
		 	</form>";
		 	
            echo $MemberInformation;
            ?>
    </div>
    <div id="menu1" class="tab-pane fade"><br>
		<?php 
			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$FBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL) order by FB帳號;";
			
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
			
			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$FBID' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$FBID') AND 出貨日期 = '0000-00-00' order by FB帳號;";
			
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
			
			
			
			
			$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$FBID' AND `匯款編號` <> (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$FBID') AND 出貨日期 = '0000-00-00' AND 匯款日期 <> '0000-00-00' order by FB帳號;";
				
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
		        else 
		        {
        		    $sql = "INSERT INTO `Members` (`FB帳號`, `FBID`, `郵遞區號＋地址`, `全家店到店服務代號`, `寄送方式`, `運費`, `備註`)
        		    VALUES (\"$fbAccount\", \"$FBID\", \"$Address\", \"$FamilyNumber\", \"$ShippingWay\", \"$ShippingFee\", \"$Memo\")
        		    ON DUPLICATE KEY UPDATE `FB帳號`=\"$fbAccount\",`郵遞區號＋地址`=\"$Address\",`全家店到店服務代號`=\"$FamilyNumber\", `寄送方式`=\"$ShippingWay\", `運費`=\"$ShippingFee\", `備註`=\"$Memo\"";
        		
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
			$sql = "SELECT * FROM `Members` WHERE FBID  = '$FBID';";
		 	$result = mysql_query($sql,$con);
		 	
		 	if (!$result) {
		 		die('Invalid query: ' . mysql_error());
		 	}
		 	
		 	$row = mysql_fetch_array($result);

		 	$ShippingInformation = "
			<form name=\"AddressInformationForm\" action=\"PaymentProcessCallBack.php\" onsubmit=\"return validateShippingForm()\" method=\"POST\">
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
    				<option value=\"ZOo\">ZOo</option>
    				<option value=\"Bon Vivant\">Bon Vivant</option>
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
    	 	<input type=\"submit\" value=\"修改寄送資訊\">
    	 	</form>";
		 	
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
		 	
            echo $ShippingInformation;
            ?>
    </div>
    <div id="menu3" class="tab-pane fade"><br>
		<?php
		
		$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$FBID' AND (匯款日期 = '0000-00-00' || 匯款日期 is NULL) order by FB帳號;";
			
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
			
		$sql = "SELECT * FROM `ShippingRecord` WHERE FBID = '$FBID' AND `匯款編號` = (SELECT MAX( 匯款編號 ) FROM RemitRecord where FBID = '$FBID') order by FB帳號;";
			
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
			
		$sql = "SELECT * FROM `Members` WHERE FBID  = '$FBID';";
		$result = mysql_query($sql,$con);
		
		if (!$result) {
		    die('Invalid query: ' . mysql_error());
		}
		
		$row = mysql_fetch_array($result);
		$name = $row['姓名'];
		$phoneNumber = $row['手機號碼'];
		$address = $row['郵遞區號＋地址'];
		$familyNumber = $row['全家店到店服務代號'];
		$shippingWay = $row['寄送方式'];
		$shippingFee = $row['運費'];
		$rebate = $row['Rebate'];
		
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
		    if($totalPrice > 3000)
		    {
		    	$rebateToBeIncrease = $totalPrice * 2 / 100;
		    }
		    else
		    {
		    	$rebateToBeIncrease = 0;
		    }
		    if($moneyToBePaid >= $rebate)
		    {
		    	$rebateTobeDeduct = $rebate;
		    	$rebateWillBeUpdate = $rebateToBeIncrease;
		    	$moneyToBePaid = $moneyToBePaid - $rebate;
		    }
		    else 
		    {
		    	
		    	$rebateTobeDeduct = $moneyToBePaid;
		    	$rebateWillBeUpdate = $rebate - $rebateTobeDeduct + $rebateToBeIncrease;
		    	$moneyToBePaid = 0;
		    }
		}
		
	    if($toRemitTableCount > 0)
	    {
	    	echo "<table style=\"font-size:24px;display: inline-block;\">
	    	<tr>
	    	<th>購買金額</th>
	    	<td>$totalPrice</td>
	    	</tr>
	    	</table>";	
	    		    		    	
	        echo '<b><font size="32"> + </font></b>';
	    	
	    	echo "<table style=\"font-size:24px;display: inline-block;\">
	    	<tr>
	    	<th>運費</th>
	    	<td>$actualShippingFee</td>
	    	</tr>
	    	</table>";
	    	
	    	echo '<b><font size="32"> - </font></b>';
	    	
	    	echo "<table style=\"font-size:24px;display: inline-block;\">
	    	<tr>
	    	<th>回饋金</th>
	    	<td>$rebateTobeDeduct</td>
	    	</tr>
	    	</table>";
	    	
	    	echo '<b><font size="32"> = </font></b>';
	    	
	    	echo "<table style=\"font-size:24px;display: inline-block;\">
	    	<tr>
	    	<th>合計匯款金額</th>
	    	<td>$moneyToBePaid</td>
	    	</tr>
	    	</table><br>";
	    	
	    	
// 	        echo '<b><font size="6">';
// 	        echo "購買金額 : $totalPrice + 運費 : $actualShippingFee - 回饋金 : $rebateTobeDeduct = 合計匯款金額 : $moneyToBePaid<br>";
// 	        echo '</font></b>';
	        
	    	echo "<table style=\"font-size:22px;\">
	    	<tr>
	    	<th style=\"background-color: #ccff66; color: #804000;\">回饋金餘額</th>
	    	<td title=\"回饋金餘額=溢付款/商品退款+前期購買金買3000元2%回饋-本期使用金額（未使用完畢遞延下期繼續折抵）\">$rebate</td>
	    	</tr>
	    	</table>";
	    	
	    	echo '<hr align="left" width="1200px" color="#000000" size="4" />';
	    	
	        echo '<b><font size="4">';
	        echo "匯款帳號:郵局戶名:謝昀臻<br>
			                    帳號:0002015-0385639";
	        echo '</font></b>';
	        
	        echo '<hr align="left" width="1200px" color="#000000" size="4" />';
	        
	        echo $toRemitTable;

	        
	        if(($address != "") && ($familyNumber != "") && ($shippingWay != "") && ($name != ""))
	        {
		 		echo "<form name=\"RemitForm\" action=\"PaymentProcessCallBack.php\" onsubmit=\"return validateRemitForm(this)\" method=\"POST\">
		 			<input type=\"hidden\" name=\"CheckOut\" value=\"run\">
		        	<input type=\"hidden\" value=\"$FBID\" name=\"FBID\">
		        	<input type=\"hidden\" value=\"$moneyToBePaid\" name=\"moneyToBePaid\">
	    	    	<input type=\"hidden\" value=\"$rebateWillBeUpdate\" name=\"rebateWillBeUpdate\">
	    	    	<input type=\"hidden\" value=\"$rebateTobeDeduct\" name=\"rebateTobeDeduct\">
				  	<p><input type=\"text\" name=\"remitLastFiveDigit\" placeholder=\"匯款末五碼\"></p>
				  	<p><input type=\"text\" name=\"remitAmont\" placeholder=\"匯款金額\"></p>
				  	<p><input type=\"text\" name=\"memo\" placeholder=\"Memo\"></p>
				  	<input type=\"submit\" name=\"RemitButton\" value=\"回報匯款\">
		 		</form>";
	        }
	        else 
	        {
	        	echo '<b><font size="6">';
	        	echo "請檢查會員資料和寄送地址，有欄位沒有填寫喔";
	        	echo '</font></b>';
	        	echo "<form name=\"RemitForm\" action=\"PaymentProcessCallBack.php\" onsubmit=\"return validateRemitForm(this)\" method=\"POST\">
	        	<fieldset disabled>
	        	<input type=\"hidden\" name=\"CheckOut\" value=\"run\">
	        	<input type=\"hidden\" value=\"$FBID\" name=\"FBID\">
	        	<input type=\"hidden\" value=\"$moneyToBePaid\" name=\"moneyToBePaid\">
	        	<input type=\"hidden\" value=\"$rebateWillBeUpdate\" name=\"rebateWillBeUpdate\">
	        	<p><input type=\"text\" name=\"remitLastFiveDigit\" placeholder=\"匯款末五碼\"></p>
	        	<p><input type=\"text\" name=\"remitAmont\" placeholder=\"匯款金額\"></p>
	        	<p><input type=\"text\" name=\"memo\" placeholder=\"Memo\"></p>
	        	<input type=\"submit\" name=\"RemitButton\" value=\"回報匯款\" style=\"background-color: #d6d6c2;\">
	        	</fieldset>
	        	</form>";	        	
	        }
 		}
 		else 
 		{
     		echo "<h3>已收到您的資料待對帳</h3>";
     		
     		echo "<table style=\"font-size:22px;\">
     		<tr>
     		<th style=\"background-color: #ccff66; color: #804000;\">回饋金餘額</th>
     		<td title=\"回饋金餘額=溢付款/商品退款+前期購買金買3000元2%回饋-本期使用金額（未使用完畢遞延下期繼續折抵）\">$rebate</td>
     		</tr>
     		</table><br>";
     		
    	 	echo $remitedTable;
    	 	
 		}
		?>
    </div>    
  </div>
</div>

</body>
</html>
