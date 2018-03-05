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
		},
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
        $('.table-remove').click(function () {
            if(confirm("確定刪除?"))
            {
	        	var data = $('#ItemCategorys').DataTable()
			        .row( $(this).parents('tr') )
			        .data();

				$.ajax({
					type: "POST",
					url: "DeleteItemCategory.php",
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
		var table = $('#ItemCategorys').DataTable();
        $('#ItemCategorys tbody').on( 'focusout', 'td', function () {
        	var cell = table.cell( this );
            cell.data( this.innerHTML );
        } );
        $("#submit").click(function(event){
    		var amount = $('#itemID');
    		var comment = $('#itemSpec');
    		amount.closest('.form-group').removeClass('has-error').addClass('has-success');
    		comment.closest('.form-group').removeClass('has-error').addClass('has-success');
    		if(!amount.val() || !comment.val()) {
    			if(!amount.val())
    			{
    				amount.closest('.form-group').removeClass('has-success').addClass('has-error');
    			}
    			if(!comment.val())
    			{
    				comment.closest('.form-group').removeClass('has-success').addClass('has-error');
    			}
    			event.preventDefault();
    		}
    		else
    		{
        		event.preventDefault();
        		$.ajax({
    				type: "POST",
    				url: "AddItemCategory.php",
    				data: $(specAddForm).serialize(),
    			}).done(function(output) {
    				alert(output);
    			});
        		$('#myModal').modal('hide');
    		}
    	});
    });

	function addSpec(tableCell) {

			var res = tableCell.parentNode.childNodes[0].innerHTML.match(/uploads\/([^\"]+)\"/);
            $('#photo').val(res[1]);
			$('#itemID').val(tableCell.parentNode.childNodes[1].innerHTML);
			$('#itemName').val(tableCell.parentNode.childNodes[2].innerHTML);
			$('#itemPrice').val(tableCell.parentNode.childNodes[3].innerHTML);
			$('#itemSpec').val(tableCell.parentNode.childNodes[4].innerHTML);
			$('#month').val(tableCell.parentNode.childNodes[5].innerHTML);
			$('#itemCost').val(tableCell.parentNode.childNodes[8].innerHTML);
			$('#itemWholeSalePrice').val(tableCell.parentNode.childNodes[9].innerHTML);
			$('#vendor').val(tableCell.parentNode.childNodes[10].innerHTML);
			$('#arriveDate').val(tableCell.parentNode.childNodes[11].innerHTML);
			jQuery.noConflict();
			$('#myModal').modal('show');
	}
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
			window.history.replaceState( {} , '到貨管理', 'http://mommyssecret.tw/MS/ItemCategoryViewCallBack.php' );
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

	$result = mysql_query("SELECT * FROM `ItemCategory` where 月份 = ".date("Ym",strtotime("+2 month"))." OR 月份 = ".date("Ym",strtotime("+1 month"))." OR 月份 = ".date("Ym",strtotime("+15 day"))." OR 月份 = ".date("Ym",strtotime("+0 month"))." OR 月份 = ".date("Ym",strtotime("-1 month"))." OR 月份 = ".date("Ym",strtotime("-2 month"))." OR 月份 = ".date("Ym",strtotime("-3 month"))." OR 月份 = ".date("Ym",strtotime("-4 month"))." OR 月份 = ".date("Ym",strtotime("-5 month"))  )

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
	<th>刪除</th>
	</thead></tr><tbody>";
	echo "<tr>";
	echo "<td><img src=../uploads/NotAvailable.png style=\"height:100px;width:100px;\" /></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\">新增品項</td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td contenteditable=\"true\"></td>";
	echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
	echo "<td></td>";
	echo "</tr>";

	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td><img src=../uploads/".str_replace(' ', '%20',$row[Photo])." style=\"height:100px;width:100px;\" /></td>";
// 		echo "<td><img src=uploads/".$row[Photo]." /></td>";
		echo "<td onclick=\"addSpec(this)\">".$row[ItemID]."</td>";
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
		echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span></td>";
		echo "</tr>";
	}

	echo "</tbody></table>";
	?>

<div id="myModal" class="modal fade" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">新增規格</h4>
            </div>
            <div class="modal-body" id="myModalBody">
                <form id="specAddForm" role="form" >
                	<div class="form-group">
                        <label for="itemID">photo</label>
                        <input type="text" name="photo" id="photo" class="form-control"/>
                    </div>
                	<div class="form-group">
                        <label for="itemID">ItemID</label>
                        <input type="text" name="itemID" id="itemID" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="itemName">品項</label>
                        <input type="text" name="itemName" id="itemName" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="itemPrice">價格</label>
                        <input type="text" name="itemPrice" id="itemPrice" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="itemSpec">規格</label>
                        <input type="text" name="itemSpec" id="itemSpec" placeholder="輸入規格" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="month">月份</label>
                        <input type="text" name="month" id="month" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="itemCost">成本</label>
                        <input type="text" name="itemCost" id="itemCost" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="itemWholeSalePrice">批發價</label>
                        <input type="text" name="itemWholeSalePrice" id="itemWholeSalePrice" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="vendor">廠商</label>
                        <input type="text" name="vendor" id="vendor" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="arriveDate">到貨日期</label>
                        <input type="text" name="arriveDate" id="arriveDate" class="form-control"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submit" class="btn btn-success">確定</button>
            </div>
        </div>
    </div>
</div>
</body>
	
