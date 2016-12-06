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
	<script src="../../extensions/Editor/js/dataTables.editor.min.js"></script>
	
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
    $(document).ready(function () {        
        $('#ItemCategorys').dataTable({  
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "asc" ]],
    	select: true
        });
        $('.table-update').click(function () {
        	var arriveDate = $(this).closest("tr").find(".arriveDate");
	      	var data = $('#ItemCategorys').DataTable()
		        .row( $(this).parents('tr') )
		        .data();
			
			$.ajax({
				type: "POST",
				url: "UpdateItemCategory.php",
				data: {data : data}
			}).done(function(output) {
				alert(output);
				arriveDate.html(output);
			});	        
      	});
//         $("#ItemCategorys").on('click', function() {
//         	this.invalidate();
//         	this.draw();
//         });
		var table = $('#ItemCategorys').DataTable();
        $('#ItemCategorys tbody').on( 'focusout', 'td', function () {
        	var cell = table.cell( this );
            cell.data( this.innerHTML ).draw();
        } );
    });
    // Activate an inline edit on click of a table cell  
    
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
	?>
		<script>
			window.history.replaceState( {} , '到貨管理', 'http://mommyssecret.tw/ItemCategoryViewCallBack.php' );
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
			($fbAccount == 'Queenie Tsan')||
			($fbAccount == '熊會買')||
			($fbAccount == '熊哉')||
    		($fbAccount == '古振平')||
            ($fbAccount == 'Keira Lin'))
	{
	// 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}
	
	//To get all item id
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT * FROM `ItemCategory`")
	
	or die(mysql_error());
	
	echo "<table id=\"ItemCategorys\">
	<thead><tr>
	<th>ItemID</th>	    		
	<th>品項</th>
	<th>單價</th>				
	<th>規格</th>
	<th>月份</th>	    		
	<th>需求數量</th>
	<th>到貨數量</th>
	<th>成本</th>
	<th>批發價</th>
	<th>廠商</th>
	<th>到貨日期</th>
	<th></th>
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td contenteditable=\"true\">".$row[ItemID]."</td>";
		echo "<td contenteditable=\"true\">".$row[品項]."</td>";
		echo "<td contenteditable=\"true\">".$row[單價]."</td>";
		echo "<td contenteditable=\"true\">".$row[規格]."</td>";
		echo "<td contenteditable=\"true\">".$row[月份]."</td>";
		echo "<td contenteditable=\"true\">".$row[需求數量]."</td>";
		echo "<td contenteditable=\"true\">".$row[到貨數量]."</td>";
		echo "<td contenteditable=\"true\">".$row[成本]."</td>";
		echo "<td contenteditable=\"true\">".$row[批發價]."</td>";
		echo "<td contenteditable=\"true\">".$row[廠商]."</td>";
		echo "<td class=\"arriveDate\">".$row[到貨日期]."</td>";
		echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
</body>
	
