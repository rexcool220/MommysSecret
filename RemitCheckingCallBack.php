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
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
<style>
#Default {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#Member {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 60%;
}

td, th {
    border: 1px solid #ddd;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #ffe6e6;
    color: #ea9399;
}
body {
    background-image: url("MommysSecretBackGround.png");
    background-repeat: no-repeat;
    background-position: right top;
    background-size: 25%;
    background-attachment: fixed;
}
</style>
<title>匯款出貨確認表</title>
</head>
<body>
<script type="text/javascript">
    $(document).ready(function () {
        $('#remitCheckingTable').dataTable({
		"fixedHeader": {
			header: true,
		},                
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "desc" ]]
        });
    });
</script>
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

$sql = "SELECT * FROM  `RemitRecord` where 匯款編號 > 1300 ORDER BY 已收款 ASC,匯款編號  DESC ;";

$result = mysql_query($sql,$con);

if (!$result) {
	die('Invalid query: ' . mysql_error());
}
$remitCheckingTableCount = mysql_num_rows($result);

$remitCheckingTable = "<table id=\"remitCheckingTable\">
	<thead><tr>
	<th>匯款編號 </th>
	<th>FB帳號</th>
    <th>FBID</th>
	<th>匯款金額</th>
	<th>應匯款金額</th>
	<th>匯款末五碼</th>
	<th>匯款日期</th>
	<th>Memo</th>
	<th>已收款</th>
	<th>管理員備註</th>				
	<th></th>
	</thead></tr><tbody>";
while($row = mysql_fetch_array($result))
{
	$isRemited = $row['已收款'] == 0 ? "否" : "已收";
	$isShipped = $row['已出貨'] == 0 ? "否" : "已出";
	$remitCheckingTable = $remitCheckingTable . "<tr>";
	$remitCheckingTable = $remitCheckingTable . "<td><a href=\"BuyingInformationByRemitNumber.php?remitNumber=".$row['匯款編號']."\" target=\"_blank\">".$row['匯款編號']."</a></td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['FB帳號'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['FBID'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款金額'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['應匯款金額'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款末五碼'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['匯款日期'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['Memo'] . "</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>$isRemited</td>";
	$remitCheckingTable = $remitCheckingTable . "<td>" . $row['管理員備註'] . "</td>";
	if($row['已收款'] == false)
	{
    	$remitCheckingTable = $remitCheckingTable . "<td>    
        	<form action=\"RemitCheckingCallBack.php\" method=\"get\">
         		<input type=\"hidden\" name=\"RemitChecked\" value=\"run\">
         		<input type=\"hidden\" value=\"".$row['匯款編號']."\" name=\"remitNumber\">
         		<input type=\"submit\" value=\"確認已匯款\">
         	</form>
    	</td>";
	}
	else 
	{
	    $remitCheckingTable = $remitCheckingTable . "<td>
        	<form action=\"RemitCheckingCallBack.php\" method=\"get\">
            	<input type=\"hidden\" name=\"RemitUnchecked\" value=\"run\">
            	<input type=\"hidden\" value=\"".$row['匯款編號']."\" name=\"remitNumber\">
            	<input type=\"submit\" value=\"取消匯款\">
     		</form>
    	</td>";	
	}
	
	$remitCheckingTable = $remitCheckingTable . "</tr>";
}
$remitCheckingTable = $remitCheckingTable . "</tbody></table>";

if (!empty($_GET['RemitChecked'])) {
	$remitNumber = $_GET['remitNumber'];
	
	$sql = "UPDATE `RemitRecord` SET `已收款` = '1'  WHERE 匯款編號 = $remitNumber";
	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$sql = "UPDATE `ShippingRecord` SET `確認收款` = '1'  WHERE 匯款編號 = $remitNumber AND (ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true)";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	header("location: http://mommyssecret.tw/RemitCheckingCallBack.php");
}

if (!empty($_GET['RemitUnchecked'])) {
    $remitNumber = $_GET['remitNumber'];

    $sql = "UPDATE `RemitRecord` SET `已收款` = '0'  WHERE 匯款編號 = $remitNumber";
    $result = mysql_query($sql,$con);

    if (!$result) {
        die('Invalid query: ' . mysql_error());
    }

    $sql = "UPDATE `ShippingRecord` SET `確認收款` = '0'  WHERE 匯款編號 = $remitNumber AND (ItemID, 規格) IN (SELECT DISTINCT ItemID, 規格 FROM  `ItemCategory` WHERE Active = true)";
    $result = mysql_query($sql,$con);

    if (!$result) {
        die('Invalid query: ' . mysql_error());
    }

    header("location: http://mommyssecret.tw/RemitCheckingCallBack.php");
}

if (!empty($_GET['remitNumberLink'])) {
	$remitNumberLink = $_GET['remitNumberLink'];
	$managerMemo = $_GET['managerMemo'];
	if(preg_match("/(?<=remitNumber=)[0-9]+/", $remitNumberLink, $matches)) {
		$remitNumber = $matches[0];
	}
	else {
		echo 'Some thing error<p>';
		exit;
	}
	$sql = "UPDATE `RemitRecord` SET `管理員備註` = '$managerMemo'  WHERE 匯款編號 = $remitNumber";
	
	$result = mysql_query($sql,$con);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	header("location: http://mommyssecret.tw/RemitCheckingCallBack.php");
}

echo $remitCheckingTable;

mysql_close($con);
?>
<script>
var table = document.getElementById("remitCheckingTable");
if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 9)//管理員備註
			{
		        table.rows[i].cells[j].onclick = function ()
		        {
		            tableText(this);
		        };
			}
// 			if(j == 9)
// 			{
// 		        table.rows[i].cells[j].onclick = function ()
// 		        {
// 		            confirmRemited(this);
// 		        };
// 			}
		}
    }
}

function tableText(tableCell) {
    var memo = prompt("輸入管理員備註");
	var remitNumberLink = table.rows[tableCell.parentNode.rowIndex].cells[0].innerHTML;
	window.location.replace("http://mommyssecret.tw/RemitCheckingCallBack.php?managerMemo=" + memo + "&remitNumberLink=" + remitNumberLink);
}
function confirmRemited(tableCell) {
    //alert("confirm");
}
</script>
</body>
</html>