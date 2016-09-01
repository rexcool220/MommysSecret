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
        ($fbAccount == '王雅琦')||
        ($fbAccount == 'Queenie Tsan')||
        ($fbAccount == '熊會買')||
        ($fbAccount == '熊哉')||
        ($fbAccount == '熊會算')||
        ($fbAccount == '古振平'))
    {
        // 	echo "管理者 : $fbAccount";
    }
    else
    {
        echo "$fbAccount : 你不是管理者";
        exit;
    }

$sql = "SELECT FB帳號, FBID, MAX(匯款日期 ) 
FROM  `ShippingRecord` 
WHERE FBID
IN (

SELECT DISTINCT FBID
FROM  `ShippingRecord` 
WHERE  `匯款日期` =  '0000-00-00'
)
GROUP BY FBID ORDER BY MAX(匯款日期 ) ASC";

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
	</tr>";

while($row = mysql_fetch_array($result))
{
    $NotRemitList = $NotRemitList . "<tr>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FB帳號'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['FBID'] . "</td>";
    $NotRemitList = $NotRemitList . "<td>" . $row['MAX(匯款日期 )'] . "</td>";
    $NotRemitList = $NotRemitList . "</tr>";
}
$NotRemitList = $NotRemitList . "</table>";

echo "<h3>共 $NotRemitListCount 人</h3>";

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
</script>

