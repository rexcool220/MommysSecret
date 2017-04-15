<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
require_once '../ConnectMySQL.php';
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
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">	
	<title>會員管理</title>
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
        $('#MemberInformation').dataTable({
		"fixedHeader": {
			header: true,
		},               
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
        "order": [[ 11, "asc" ]],
    	select: true
        });
        $('.table-update').click(function () {
	      	var data = $('#MemberInformation').DataTable()
		        .row( $(this).parents('tr') )
		        .data();
			
			$.ajax({
				type: "POST",
				url: "/Members/MemberEdit.php",
				data: {data : data}
			}).done(function(output) {
				alert(output);
			});	        
      	});
//         $("#MemberInformation").on('click', function() {
//         	this.invalidate();
//         	this.draw();
//         });
		var table = $('#MemberInformation').DataTable();
        $('#MemberInformation tbody').on( 'focusout', 'td', function () {
        	var cell = table.cell( this );
            cell.data( this.innerHTML );
        } );
    	$("#submit").click(function(event){
			var amount = $('#amount');
			var comment = $('#comment');
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
					url: "/Members/RebateEdit.php",
					data: $(rebateform).serialize(),
				}).done(function(output) {
					alert(output);
				});
	    		$('#myModal').modal('hide');
			}
    	});
    });
	function editRebate(tableCell) {
		if($('#accountType').text() == "共用帳號")
		{
			alert("修改回饋金，請不要使用共用帳號");
		}
		else
		{
	// 		alert(tableCell.parentNode.childNodes[0].innerHTML);
			$('#loginFBAccount').val($('#fbAccount').text());
			$('#customberFBAccount').val(tableCell.parentNode.childNodes[0].innerHTML);
			$('#customerFBID').val(tableCell.parentNode.childNodes[8].innerHTML);
			$('#logInAccount').val();
			$('#myModal').modal('show');
		}
	}
    
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
			window.history.replaceState( {} , '會員管理', 'http://mommyssecret.tw/Members/MemberView.php' );
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
		echo "$fbAccount : 你不是管理者";
		exit;
	}
	
	$result = mysql_query("SELECT * FROM `Members`")
	
	or die(mysql_error());
	
	echo "<table id=\"MemberInformation\">
	<thead><tr>
	<th>姓名</th>	    		
	<th>FB帳號</th>
	<th>手機號碼</th>				
	<th>郵遞區號＋地址</th>
	<th>全家店到店服務代號</th>	    		
	<th>寄送方式</th>
	<th>運費</th>
	<th>備註</th>
	<th>FBID</th>
	<th>Rebate</th>
	<th>Type</th>
	<th></th>
	</thead></tr><tbody>";
	
	echo "<td contenteditable=\"true\">新增會員資料</td>";
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
	echo "</tr>";
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td contenteditable=\"true\">".$row[姓名]."</td>";
		echo "<td contenteditable=\"true\">".$row[FB帳號]."</td>";
		echo "<td contenteditable=\"true\">".$row[手機號碼]."</td>";
		echo "<td contenteditable=\"true\">".$row[郵遞區號＋地址]."</td>";
		echo "<td contenteditable=\"true\">".$row[全家店到店服務代號]."</td>";
		echo "<td contenteditable=\"true\">".$row[寄送方式]."</td>";
		echo "<td contenteditable=\"true\">".$row[運費]."</td>";
		echo "<td contenteditable=\"true\">".$row[備註]."</td>";
		echo "<td contenteditable=\"true\">".$row[FBID]."</td>";
		echo "<td onclick=\"editRebate(this)\">".$row[Rebate]."</td>";
		echo "<td contenteditable=\"true\">".$row[Type]."</td>";
		echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
  <!-- Modal -->
<!-- Modal -->
<!-- modal contact form -->
<div id="myModal" class="modal fade" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">回饋金表單</h4>
            </div>
            <div class="modal-body" id="myModalBody">
                <form id="rebateform" role="form" >
                	<div class="form-group">
                        <label for="loginFBAccount">登入帳號</label>
                        <input type="text" name="loginFBAccount" id="loginFBAccount" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="customberFBAccount">客戶帳號</label>
                        <input type="text" name="customberFBAccount" id="customberFBAccount" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="customerFBID">客戶FBID</label>
                        <input type="text" name="customerFBID" id="customerFBID" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="amount">金額</label>
                        <input type="text" name="amount" id="amount" placeholder="輸入欲增加/減少的金額" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="comment">備註</label>
                        <textarea name="comment" id="comment" rows="4" placeholder="請註明修改原因" class="form-control"></textarea>
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