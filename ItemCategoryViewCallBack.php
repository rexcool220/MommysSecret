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
	<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">	
	<title>到貨管理</title>
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
    	select: true,
		"fixedHeader": {
			header: true,
		}
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
            cell.data( this.innerHTML );
//             var row = $(this).parent().parent().children().index($(this).parent());
//             var col = $(this).parent().children().index($(this));
//             if((col == 6)||(col == 7))
//             {
//             	var activeCell = table.cell(row, 12);
//             	requestAmount = parseInt($(this).parent().children(':nth-child(7)').text());//需求數量
//             	currentAmount = parseInt($(this).parent().children(':nth-child(8)').text());//到貨數量
// 				if(currentAmount >= requestAmount)
// 				{
// 					$(this).parent().children().eq(12).empty().append('1');
// 					activeCell.data('1');
// 				}
// 				else
// 				{
// 					$(this).parent().children().eq(12).empty().append('0');
// 					activeCell.data('0');
// 				}
// 				$(this).parent().draw()
//             }
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
	
	//To get all item id
	include('ConnectMySQL.php');
	
	// get results from database
	
// 	echo "SELECT * FROM `ItemCategory` where 月份 = ".date("Ym",strtotime("+1 month"))." AND 月份 = ".date("Ym",strtotime("+0 month"))." AND 月份 = ".date("Ym",strtotime("-1 month"))." AND 月份 = ".date("Ym",strtotime("-2 month"));
	
	$result = mysql_query("SELECT * FROM `ItemCategory` where 月份 = ".date("Ym",strtotime("+2 month"))." OR 月份 = ".date("Ym",strtotime("+1 month"))." OR 月份 = ".date("Ym",strtotime("+0 month"))." OR 月份 = ".date("Ym",strtotime("-1 month"))." OR 月份 = ".date("Ym",strtotime("-2 month")))
	
	or die(mysql_error());
	
	echo "<table id=\"ItemCategorys\">
	<thead><tr>
	<th>商品圖</th>
	<th>ItemID</th>		
	<th>品項</th>
	<th>價格</th>				
	<th>規格</th>
	<th>月份</th>	    		
	<th>需求數量</th>
	<th>到貨數量</th>
	<th>成本</th>
	<th>批發價</th>
	<th>廠商</th>
	<th>到貨日期</th>
	<th>Active</th>			
	<th>存檔</th>
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td><img src=uploads/".str_replace(' ', '%20',$row[Photo])." style=\"height:100px;width:100px;\" /></td>";
// 		echo "<td><img src=uploads/".$row[Photo]." /></td>";
		echo "<td contenteditable=\"true\">".$row[ItemID]."</td>";
		echo "<td contenteditable=\"true\">".$row[品項]."</td>";
		echo "<td contenteditable=\"true\">".$row[價格]."</td>";
		echo "<td contenteditable=\"true\">".$row[規格]."</td>";
		echo "<td contenteditable=\"true\">".$row[月份]."</td>";
		echo "<td contenteditable=\"true\">".$row[需求數量]."</td>";
		echo "<td contenteditable=\"true\">".$row[到貨數量]."</td>";
		echo "<td contenteditable=\"true\">".$row[成本]."</td>";
		echo "<td contenteditable=\"true\">".$row[批發價]."</td>";
		echo "<td contenteditable=\"true\">".$row[廠商]."</td>";
		echo "<td class=\"arriveDate\">".$row[到貨日期]."</td>";
		echo "<td contenteditable=\"true\">".$row[Active]."</td>";
		echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
</body>
	
