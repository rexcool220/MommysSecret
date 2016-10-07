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
<link rel="stylesheet" type="text/css" href="MommysSecret.css">
<title>未匯款清單</title>
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
    $fbAccount = $userNode->getName();
    if(($fbAccount == 'Gill Fang')||
        ($fbAccount == 'JoLyn Dai')||
        ($fbAccount == 'Queenie Tsan')||
        ($fbAccount == '熊會買')||
        ($fbAccount == '熊哉')||
        ($fbAccount == '古振平'))
    {
        // 	echo "管理者 : $fbAccount";
    }
    else
    {
        echo "$fbAccount : 你不是管理者";
        exit;
    }

$sql = "SELECT FB帳號, FBID, MAX(匯款日期 ) , SUM(單價*數量 ) 
FROM  `ShippingRecord`
WHERE  `匯款日期` =  '0000-00-00'
AND FBID
IN (

SELECT DISTINCT FBID
FROM  `ShippingRecord` 
WHERE  `匯款日期` =  '0000-00-00'
)
GROUP BY FBID
ORDER BY MAX( 匯款日期 ) ASC, SUM(單價*數量) DESC";

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

$NotRemitList = "<table id=\"NotRemitList\" width=\"60%\">
	<tr>
	<th>FB帳號 </th>
    <th>FBID</th>
	<th>最近匯款日期</th>
	<th>應付款金額</th>
	<th></th>
	</tr>";

$accountList = "";
while($row = mysql_fetch_array($result))
{
    $NotRemitList = $NotRemitList . "<tr>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FB帳號'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FBID'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['MAX(匯款日期 )'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['SUM(單價*數量 )'] . "</td>";
    $NotRemitList = $NotRemitList . "<td><input type=\"checkbox\" name=\"NotRemitList\" value=\"".$row['FB帳號']."\" onclick=\"NotRemitListChecked(this)\" checked style=\"WIDTH: 40px; HEIGHT: 40px\">";
    $NotRemitList = $NotRemitList . "</tr>";
    $accountList = $accountList . "\n@" . $row['FB帳號'];
}
$NotRemitList = $NotRemitList . "</table>";

echo "<h3>共 $NotRemitListCount 人</h3>";

echo "<textarea id=\"TobeInformTextarea\" rows=\"4\" cols=\"50\">".date("Y/m/d")."未匯款點點名，還有".$NotRemitListCount."人未匯款！！哭哭～～先tag一些喔！＃請大家趕快匯款，社團墊款資金有限，空間也有限 ＃不併月寄送收款 ＃有買車用頭枕，醬油，浴巾，巧克力粉等大又重的商品建議使用貨運，免得下期還要補收運費唷～：".$accountList."</textarea><br>";

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
	form.setAttribute("action", "http://mommyssecret.tw/NotRemitListByFBID.php");
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

