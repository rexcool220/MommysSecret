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
	<title>點單</title>
	<meta name="format-detection" content="telephone=no">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>  
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
</head>
<body>

<script type="text/javascript">
    $(document).ready(function () {
        $('#ItemsList').dataTable({
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
			'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
			'default_graph_version' => 'v2.8',
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
		window.history.replaceState( {} , 'PaymentProcess', 'http://mommyssecret.tw/FBParserCallBack.php' );
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

	$fbID = $userNode->getId();
	
	$fbAccount = $userNode->getName();
	
	$result = mysql_query("SELECT TYPE FROM `Members` WHERE FBID = $fbID")
	
	or die(mysql_error());
	
	$row = mysql_fetch_array($result);
	
	$type = $row['TYPE'];
	
	if($type == "共用帳號")
	{
		echo "<p hidden id=\"accountType\">$type</p>";
		echo "<p hidden id=\"fbAccount\">$fbAccount</p>";
	}
	else
	{
		echo "$fbAccount : 你沒有權限";
		exit;
	}
	//To get all item id
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT distinct ItemID FROM `ShippingRecord`")
	
	or die(mysql_error());
	
	$ItemIDs = array();
	
	while($row = mysql_fetch_array($result)){
	    $ItemIDs[] = $row["ItemID"];
	}
	
	try {
	    $response = $fb->get("/607414496082801/feed?fields=id,created_time,message&since=". date("Y-m-d", strtotime("-3 months")). "&limit=500");
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
	    echo 'Graph returned an error: ' . $e->getMessage();
	    exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
	    exit;
	}
	$result = $response->getDecodedBody();
 
	$pagesEdge = $response->getGraphEdge();
	
	echo "<table id=\"ItemsList\">
	<thead><tr>
	<th>ID</th>
	<th>月份</th>			
	<th>開團日期</th>
	<th>收單日期</th>
	<th>品項</th>
	<th>規格</th>
	<th>單價</th>
	</thead></tr><tbody>";
	
	
	do {
	    foreach ($pagesEdge as $page) {
	    	
	    	preg_match("/(\d+)_(\d+)/", $page['id'], $matches);
	    	$id = $matches[2];
// 	    	echo $page['message']."<br>";
	    	preg_match("/^\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]*/", $page['message'], $matches);
	    	$itemMonthCategory = $matches[1];
	    	$dueDate = $matches[2];
	    	$itemName = $matches[3];
	    	$itemPrice = $matches[4];
	    	

	    	
	   		if(($itemName == "")||($itemName == NULL))
	   		{
	   			$itemName = "<font color=\"red\">" . substr($page['message'], 0 , 60) . "</font>"; 
	   		}
	    	
	    	
	    	echo "<tr>";
	    	if (in_array($id, $ItemIDs)) {
	    		echo "<td><font color=\"red\">".$id."</font></td>";
	    	}
	    	else 
	    	{
	    		echo "<td>".$id."</td>";
	    	}
	    	echo "<td>".$itemMonthCategory."</td>";
			echo "<td>".$page['created_time']->format('Y-m-d')."</td>";
			echo "<td>".$dueDate."</td>";
	        echo "<td>".$itemName."</td>";
	        echo "<td>".$itemSpec."</td>";
	        echo "<td>".$itemPrice."</td>";
			echo "</tr>";

	    }
	} while ($pagesEdge = $fb->next($pagesEdge));	
	echo "</tbody></table>";

?>
<script>
var table = document.getElementById("ItemsList");

if (table != null) {
    for (var i = 0; i < table.rows.length; i++)
	{
    	
		for (var j = 0; j < table.rows[i].cells.length; j++)
		{    
			if(j == 0)//FBID
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
	form.setAttribute("action", "http://mommyssecret.tw/FBParserComment.php");
	form.setAttribute("target", "view");

	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "ID");
	hiddenField.setAttribute("value", tableCell.innerText);
	form.appendChild(hiddenField);
	document.body.appendChild(form);

	window.open('', 'view');

	form.submit();
}
</script>