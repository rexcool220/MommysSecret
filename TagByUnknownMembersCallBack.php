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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.12.3.js"></script>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">	
	<title>失蹤會員小幫手</title>
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
	<div id="dialog" title="小幫手說">
		<p id="dialogText"></p>
	</div>
<script type="text/javascript">
    // Activate an inline edit on click of a table cell  
    $(document).ready(function () {
        $('#UnknownMembers').dataTable({  
		"fixedHeader": {
			header: true,
		},              
		dom: 'Bfrtip',
		buttons: [
        {
            text: '產生Tag文字',
            action: function ( e, dt, node, config ) {
				this.rows().every( function () {
				var d = this.data();
	            	 
				d.counter++; // update data source for the row
	            	 
				this.invalidate(); // invalidate the data DataTables has cached for this row
				} );
	                
	            var FBAccounts = 
	                this
					.columns(0)
					.data()
					.eq( 0 )
					.sort()
					.unique();      // Reduce the 2D array into a 1D array of data

				var FBAccountString = "";
	            
	            for (var i = 0; i < FBAccounts.length; ++i) {
	            	FBAccountString = FBAccountString + "@" + FBAccounts[i] + "<br>"; 
	            }
	            	            
	            $( "#dialogText" ).html(FBAccountString);

	            $( "#dialog" ).dialog();
            }
        }
		],  
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "asc" ]],
    	"aaSorting": [],
        "select": {
	            style:    'os',
	            selector: 'td:first-child'
        	}
        });
        $('.table-remove').click(function () {
	      	$('#UnknownMembers').DataTable()
	        .row( $(this).parents('tr') )
	        .remove()
	        .draw();
      	});
        $('.table-duplicate').click(function () {
        	var $clone = $(this).closest('tr').clone(true);
        	$('#UnknownMembers').DataTable()
        	.row
        	.add($clone)
        	.draw();
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
			window.history.replaceState( {} , '失蹤會員小幫手', 'http://mommyssecret.tw/MS/TagByUnknownMembersCallBack.php' );
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
	
	$sql = "SELECT `FBID` FROM `Members`;";
	$result = mysql_query($sql,$con);
	
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	$row = mysql_fetch_array($result);
	
	$exsistMembers = array();
	
	while($row = mysql_fetch_array($result))
	{
		array_push($exsistMembers, $row[FBID]);
	}
// 	print_r($exsistMembers);
		
// 	try {
// 		$response = $fb->get("607414496082801/members?limit=999");
// 	} catch(Facebook\Exceptions\FacebookResponseException $e) {
// 		// When Graph returns an error
// 		echo 'Graph returned an error: ' . $e->getMessage();
// 		exit;
// 	} catch(Facebook\Exceptions\FacebookSDKException $e) {
// 		// When validation fails or other local issues
// 		echo 'Facebook SDK returned an error: ' . $e->getMessage();
// 		exit;
// 	}
// 	$result = $response->getDecodedBody();
	
// 	$pagesEdge = $response->getGraphEdge();
		
// 	echo "<table id=\"UnknownMembers\">
// 	<thead><tr>	    		
// 	<th>FB帳號</th>
// 	<th>FBID</th>
// 	<th></th>
// 	</thead></tr><tbody>";
	
// 	do {
// 		foreach ($pagesEdge as $page) {
// 			if(in_array($page['id'], $exsistMembers) == false)
// 			{
// 				echo "<tr>";
// 				echo "<td>".$page["name"]."</td>";
// 				echo "<td>".$page['id']."</td>";
// 				echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span></td>";
// 				echo "</tr>";
// 			}
// 		}
// 	} while ($pagesEdge = $fb->next($pagesEdge));
	
// 	echo "</tbody></table>";

$sql = "SELECT `FBID`, `FB帳號` FROM  `ShippingRecord` WHERE `匯款日期` = '0000-00-00' GROUP BY (`FBID`)";
$result = mysql_query($sql,$con);

if (!$result) {
	die('Invalid query: ' . mysql_error());
}

$row = mysql_fetch_array($result);
	
	echo "<table id=\"UnknownMembers\">
	<thead><tr>
	<th>FB帳號</th>
	<th>FBID</th>
	<th></th>
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		if(in_array($row['FBID'], $exsistMembers) == false)
		{
			echo "<tr>";
			echo "<td>".$row['FB帳號']."</td>";
			echo "<td>".$row['FBID']."</td>";
			echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span></td>";
			echo "</tr>";
		}
	}

	echo "</tbody></table>";
	?>
</body>
	
