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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>  
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
	<title>點單確認表</title>
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
    // Activate an inline edit on click of a table cell       
    $(document).ready(function () {
        $('#Comments').dataTable({
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "desc" ]],
        "select": {
	            style:    'os',
	            selector: 'td:first-child'
        	}
        });
        $('.table-remove').click(function () {
	      	$('#Comments').DataTable()
	        .row( $(this).parents('tr') )
	        .remove()
	        .draw();
      	});
        $('.table-duplicate').click(function () {
        	var $clone = $(this).closest('tr').clone(true);
        	$('#Comments').DataTable()
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
			window.history.replaceState( {} , '點單確認表', 'http://mommyssecret.tw/FBParserComment.php' );
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
	if(($fbAccount == '熊會買')||
		($fbAccount == '熊哉'))
	{
	// 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}
	
	if(isset($_POST['ID'])) {
		$ID = $_POST['ID'];	
	}
	else 
	{
		echo "ID is empty";
		exit;
	}
	
	try {
		$response = $fb->get("/607414496082801_".$ID."?fields=comments.limit(999)");
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

	echo "<table id=\"Comments\">
	<thead><tr>
	<th>時間</th>	    		
	<th>FB帳號</th>
	<th>FBID</th>
	<th>Message</th>
	<th></th>
	</thead></tr><tbody>";
	
	for($i = 0; $i < count($result["comments"]["data"]); $i++)
	{
		echo "<tr>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["created_time"]."</td>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["from"]["name"]."</td>";
		echo "<td contenteditable=\"false\">".$result["comments"]["data"][$i]["from"]["id"]."</td>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["message"]."</td>";
		echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span>
	    		<span class=\"table-duplicate glyphicon glyphicon-duplicate\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	
