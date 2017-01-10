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
	<title>訂單管理</title>
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
        $('#ItemInformation').dataTable({  
		"fixedHeader": {
			header: true,
		},            
        dom: 'Bfrtip',
    	buttons: [
	    	{
	    		text: '新增資料',
	    		action: function ( e, dt, node, config ) {
					alert('施工中');
	    		}
	    	}
    	],           
        "lengthMenu": [[100, 250, 500, -1], [100, 250, 500, "All"]],
        "bLengthChange": false,
    	"order": [[ 0, "asc" ]],
    	select: true
        });
        $('.table-update').click(function () {
	      	var data = $('#ItemInformation').DataTable()
		        .row( $(this).parents('tr') )
		        .data();
			
			$.ajax({
				type: "POST",
				url: "MSEdit.php",
				data: {data : data}
			}).done(function(output) {
				alert(output);
			});	        
      	});
        $('.table-remove').click(function () {
            if(confirm("確定刪除?"))
            {
	        	var data = $('#ItemInformation').DataTable()
			        .row( $(this).parents('tr') )
			        .data();
			
				$.ajax({
					type: "POST",
					url: "MSDelete.php",
					data: {data : data}
				}).done(function(output) {
					alert(output);
				});	  
		      	$('#ItemInformation').DataTable()
		        .row( $(this).parents('tr') )
		        .remove()
		        .draw();
            }
      	});
//         $("#ItemInformation").on('click', function() {
//         	this.invalidate();
//         	this.draw();
//         });
		var table = $('#ItemInformation').DataTable();
        $('#ItemInformation tbody').on( 'focusout', 'td', function () {
        	var cell = table.cell( this );
            cell.data( this.innerHTML );
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
			window.history.replaceState( {} , '訂單管理', 'http://mommyssecret.tw/MSView.php' );
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
	
	$result = mysql_query("SELECT * FROM `ShippingRecord`")
	
	or die(mysql_error());
	
	echo "<table id=\"ItemInformation\">
	<thead><tr>
	<th>FB帳號</th>	    		
	<th>品項</th>
	<th>單價</th>				
	<th>數量</th>
	<th>匯款日期</th>	    		
	<th>出貨日期</th>
	<th>序號</th>
	<th>匯款編號</th>
	<th>確認收款</th>
	<th>FBID</th>
	<th>備註</th>
	<th>月份</th>
	<th>Active</th>
	<th>規格</th>
	<th>ItemID</th>
	<th></th>
	<th></th>			
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td contenteditable=\"true\">".$row[FB帳號]."</td>";
		echo "<td contenteditable=\"true\">".$row[品項]."</td>";
		echo "<td contenteditable=\"true\">".$row[單價]."</td>";
		echo "<td contenteditable=\"true\">".$row[數量]."</td>";
		echo "<td contenteditable=\"true\">".$row[匯款日期]."</td>";
		echo "<td contenteditable=\"true\">".$row[出貨日期]."</td>";
		echo "<td contenteditable=\"true\">".$row[SerialNumber]."</td>";
		echo "<td contenteditable=\"true\">".$row[匯款編號]."</td>";
		echo "<td contenteditable=\"true\">".$row[確認收款]."</td>";
		echo "<td contenteditable=\"true\">".$row[FBID]."</td>";
		echo "<td contenteditable=\"true\">".$row[備註]."</td>";
		echo "<td contenteditable=\"true\">".$row[月份]."</td>";
		echo "<td contenteditable=\"true\">".$row[Active]."</td>";
		echo "<td contenteditable=\"true\">".$row[規格]."</td>";
		echo "<td contenteditable=\"true\">".$row[ItemID]."</td>";
		echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
		echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
</body>
	
