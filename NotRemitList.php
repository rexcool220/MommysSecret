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
<title>未匯款清單</title>
</head>
<body>
<script type="text/javascript">
    $(document).ready(function () {
        $('#NotRemitList').dataTable({
    		"fixedHeader": {
    			header: true,
    		},               
        	"lengthMenu": [[50,100,150,-1], [50, 100, 150, "All"]],
        	"order": [[ 3, "desc" ]]
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
    
        if(empty($accessToken)&&!empty($_SESSION['accessToken']))
        {
            $accessToken = $_SESSION['accessToken'];
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
		echo "$fbAccount : 你不是管理者";
		exit;
	}

$sql = "SELECT Members.FB帳號, ShippingRecord.FBID, MAX(ShippingRecord.匯款日期 ) , SUM(ShippingRecord.單價*ShippingRecord.數量 ), Members.寄送方式 FROM `ShippingRecord`,`Members` WHERE ShippingRecord.匯款日期 = '0000-00-00' AND (ShippingRecord.ItemID, ShippingRecord.規格) IN (SELECT DISTINCT ItemID, 規格 FROM `ItemCategory` WHERE Active = true) AND ShippingRecord.FBID IN ( SELECT DISTINCT FBID FROM `ShippingRecord` WHERE `匯款日期` = '0000-00-00' ) AND ShippingRecord.FBID = Members.FBID GROUP BY ShippingRecord.FBID ORDER BY MAX( ShippingRecord.匯款日期 ) ASC, SUM(ShippingRecord.單價*ShippingRecord.數量) DESC
";

$result = mysql_query($sql,$con);
if (!$result) {
    die('Invalid query: ' . mysql_error());
    exit();
}

$NotRemitListCount = mysql_num_rows($result);

if(mysql_num_rows($result) == 0)
{
    echo "$fbAccount,$FBID<br>";
}

$NotRemitList = "<table id=\"NotRemitList\">
	<thead><tr>
	<th>FB帳號 </th>
    <th>FBID</th>
	<th>最近匯款日期</th>
	<th>應付款金額</th>
    <th>出貨方式</th>
	<th></th>
	</thead></tr><tbody>";

$accountList = "";
while($row = mysql_fetch_array($result))
{
    $NotRemitList = $NotRemitList . "<tr>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FB帳號'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FBID'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['MAX(ShippingRecord.匯款日期 )'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['SUM(ShippingRecord.單價*ShippingRecord.數量 )'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['寄送方式'] . "</td>";
    $NotRemitList = $NotRemitList . "<td><input type=\"checkbox\" name=\"NotRemitList\" value=\"".$row['FB帳號']."\" onclick=\"NotRemitListChecked(this)\" checked style=\"WIDTH: 40px; HEIGHT: 40px\">";
    $NotRemitList = $NotRemitList . "</tr>";
    $accountList = $accountList . "\n@" . $row['FB帳號'];
}
$NotRemitList = $NotRemitList . "</tbody></table>";

echo "<h3>共 $NotRemitListCount 人</h3>";

echo "<textarea id=\"TobeInformTextarea\" rows=\"4\" cols=\"50\">".date("Y/m/d")."未匯款點點名，還有".$NotRemitListCount."人未匯款！！#匯款須知：空間有限不併月出貨，有近3000樣大大小小商品，保管分類就很複雜，請大家一定一定當月把商品付款領走不能寄放喔!社團墊款訂貨基於互信請大家按時匯款 ❤".$accountList."</textarea><br>";

echo $NotRemitList;

mysql_close($con);
?>
<script>
var table = document.getElementById("NotRemitList");

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
	form.setAttribute("action", "http://mommyssecret.tw/MS/NotRemitListByFBID.php");
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
function NotRemitListChecked(checkbox) {
    if (checkbox.checked)
    {
    	document.getElementById('TobeInformTextarea').value = 
        	document.getElementById('TobeInformTextarea').value + 
			"\n@" + checkbox.value;
    }
    else
    {
        stringTobeFind = "\n@" + checkbox.value;
    	document.getElementById('TobeInformTextarea').value = (document.getElementById('TobeInformTextarea').value).replace(stringTobeFind, "");
    }
}

</script>

