<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
require_once '../ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
	session_start();
}
?>
<html>
<head>
<meta name="format-detection" content="telephone=no">
<!-- <link rel="stylesheet" type="text/css" href="MommysSecret.css"> -->
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
<title>未出貨確認總表</title>
</head>
<body>
<script type="text/javascript">
    $(document).ready(function () {
        $('#shippingCheckingIndex').dataTable({
    		"fixedHeader": {
    			header: true,
    		},               
            "lengthMenu": [[50,100,150,-1], [50, 100, 150, "All"]],
        	"order": [[ 2, "desc" ]]
        });
    });
</script>
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
			window.history.replaceState( {} , '未出貨確認總表', 'http://mommyssecret.tw/Employee/ShippingCheckingIndex.php' );
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
		echo "$fbAccount : 你不是管理者";
		exit;
	}
	
// 	$sql = "SELECT FB帳號, FBID, 出貨日期, 匯款編號, 寄送方式, 應匯款金額, Memo, 管理員備註
// FROM (
// SELECT ShippingRecord.FB帳號, ShippingRecord.FBID, ShippingRecord.出貨日期, RemitRecord.匯款編號, Members.寄送方式, RemitRecord.應匯款金額, RemitRecord.Memo, RemitRecord.管理員備註
// FROM  `ShippingRecord` ,  `ItemCategory` ,  `RemitRecord` ,  `Members`
// WHERE (
// ShippingRecord.ItemID, ShippingRecord.規格
// ) = ( ItemCategory.ItemID, ItemCategory.規格 )
// AND ShippingRecord.匯款編號 = RemitRecord.匯款編號
// AND ShippingRecord.FBID = Members.FBID
// AND ShippingRecord.確認收款 =1
// AND ItemCategory.Active =
// TRUE
// ORDER BY ShippingRecord.出貨日期 DESC
// ) AS sub
// GROUP BY 出貨日期";
	$sql = 	"SELECT Members.FB帳號, ShippingRecord.FBID, ShippingRecord.出貨日期, RemitRecord.匯款編號, Members.寄送方式, RemitRecord.應匯款金額, RemitRecord.Memo, RemitRecord.管理員備註, RemitRecord.匯款日期
		FROM  `ShippingRecord` ,  `ItemCategory` ,  `RemitRecord` ,  `Members` 
		WHERE (
		ShippingRecord.ItemID, ShippingRecord.規格
		) = ( ItemCategory.ItemID, ItemCategory.規格 ) 
		AND ShippingRecord.匯款編號 = RemitRecord.匯款編號
		AND ShippingRecord.FBID = Members.FBID
		AND ShippingRecord.確認收款 =1
		AND (
		ShippingRecord.出貨日期 =  '0000-00-00'
		)
		AND ItemCategory.Active = TRUE
		GROUP BY RemitRecord.匯款編號
		ORDER BY RemitRecord.匯款編號 ASC "; 

	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$row = mysql_fetch_array($result);
	
	$ShippingCheckingIndex = "<table id=\"shippingCheckingIndex\">
	<thead><tr>
	<th>FB帳號 </th>
    <th>FBID</th>
	<th>匯款日期</th>
	<th>最新匯款編號</th>
	<th>應匯款金額</th>
	<th>出貨方式</th>
	<th>最新客戶備註</th>
	<th>最新管理員備註</th>
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<tr>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['FB帳號'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['FBID'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['匯款日期'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['匯款編號'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['應匯款金額'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['寄送方式'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['Memo'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "<td>" . $row['管理員備註'] . "</td>";
		$ShippingCheckingIndex = $ShippingCheckingIndex . "</tr>";
	}
	$ShippingCheckingIndex = $ShippingCheckingIndex . "</tbody></table>";
	
	echo $ShippingCheckingIndex;
	
	mysql_close($con);
?>
<script>
var table = document.getElementById("shippingCheckingIndex");

if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
    	
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 1)//FBID
			{
		        table.rows[i].cells[j].onclick = function ()
		        {
		            tableText(this);
		        };
			}
		}
    }
}

function tableText(tableCell) {

	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", "http://mommyssecret.tw/Employee/ShippingCheckingCallBack.php");
	form.setAttribute("target", "view");

	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "CustomerFBID");
	hiddenField.setAttribute("value", tableCell.innerHTML);
	form.appendChild(hiddenField);
	document.body.appendChild(form);

	window.open('', 'view');

	form.submit();
}
</script>
</body>
</html>