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
        dom: 'Bfrtip',
    	buttons: [
	    	{
	    		text: '上傳資料',
				action: function ( e, dt, node, config ) {
					//verify columns
					var FBAccounts = 
		                this
						.columns(0)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data
					var itemNames = 
						this
						.columns(1)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data

					var itemPrices = 
						this
						.columns(2)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data
					var itemCounts = 
		                this
						.columns(3)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data
					var FBIDs = 
						this
						.columns(9)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data

					var months = 
						this
						.columns(11)
						.data()
						.eq( 0 );      // Reduce the 2D array into a 1D array of data		
		            for (var i = 0; i < FBAccounts.length; ++i) {
		            	if(FBAccounts[i] == "")
		            	{
			            	alert("請檢查FB帳號!");
			            	return false;
		            	}
		            }

		            for (var i = 0; i < itemNames.length; ++i) {
		            	if(itemNames[i] == "")
		            	{
			            	alert("請檢查品項!");
			            	return false;
		            	}
		            }

		            for (var i = 0; i < itemPrices.length; ++i) {
		            	if(itemPrices[i] == "")
		            	{
			            	alert("請檢查價格!");
			            	return false;
		            	}
		            }
		            for (var i = 0; i < itemCounts.length; ++i) {
		            	if(itemCounts[i] == "")
		            	{
			            	alert("請檢查數量!");
			            	return false;
		            	}
		            }

		            for (var i = 0; i < FBIDs.length; ++i) {
		            	if(FBIDs[i] == "")
		            	{
			            	alert("請檢查FBID!");
			            	return false;
		            	}
		            }

		            for (var i = 0; i < months.length; ++i) {
		            	if(months[i] == "")
		            	{
			            	alert("請檢查月份!");
			            	return false;
		            	}
		            }		            
					
			        jQuery.fn.pop = [].pop;
			        jQuery.fn.shift = [].shift;
			        var $rows = $('#ItemInformation').find('tr:not(:hidden)');
			        var headers = [];
			        var data = [];
			        $($rows.shift()).find('th:not(:empty)').each(function () {
			            headers.push($(this).text());
					});
			          
					//Turn all existing rows into a loopable array
					$rows.each(function () {
						var $td = $(this).find('td');
						var h = {};
				            
						//Use the headers from earlier to name our hash keys
						headers.forEach(function (header, i) {
							h[header] = $td.eq(i).text();   
						});
						data.push(h);
					});
					$.ajax({
						type: "POST",
						url: "MSBatchAdd.php",
						data: {data : data}
					}).done(function(output) {
						alert(output);
					});
				}
	    	}
    	],           
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 15, "asc" ]],
    	select: true
        });
        $('.table-remove').click(function () {
            if(confirm("確定刪除?"))
            {
// 	        	var data = $('#ItemInformation').DataTable()
// 			        .row( $(this).parents('tr') )
// 			        .data();
			
// 				$.ajax({
// 					type: "POST",
// 					url: "MSDelete.php",
// 					data: {data : data}
// 				}).done(function(output) {
// 					alert(output);
// 				});	  
		      	$('#ItemInformation').DataTable()
		        .row( $(this).parents('tr') )
		        .remove()
		        .draw();
            }
      	});
        $('.table-duplicate').click(function () {
        	var $clone = $(this).closest('tr').clone(true);
        	$('#ItemInformation').DataTable()
        	.row
        	.add($clone)
        	.draw();
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
			window.history.replaceState( {} , '新增訂單', 'http://mommyssecret.tw/MS/MSNew.php' );
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
	<th>規格</th>
	<th>ItemID</th>
	<th></th>
	<th></th>
	</thead></tr><tbody>";
	

	echo "<tr>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td bgcolor=\"#DCDCDC\"></td>";
	echo "<td bgcolor=\"#DCDCDC\"></td>";
	echo "<td bgcolor=\"#DCDCDC\"></td>";
	echo "<td bgcolor=\"#DCDCDC\"></td>";
	echo "<td bgcolor=\"#DCDCDC\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span></td>";
	echo "<td><span class=\"table-duplicate glyphicon glyphicon-duplicate\"></span></td>";
	echo "</tr>";

	
	echo "</tbody></table>";
	?>
</body>
	
