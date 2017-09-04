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
	<script src="//code.jquery.com/jquery-1.12.3.js"></script>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
	<title>對帳管理</title>
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
        $('#RemitInformation').dataTable({
		"fixedHeader": {
			header: true,
		},               
        dom: 'Bfrtip',
    	buttons: [
	    	{
	    		text: '歷史資料',
	    		action: function ( e, dt, node, config ) {
				window.open("RemitCheckingCallBackAll.php",'_blank');
	    		}
	    	}
    	],           
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "desc" ], [8, "asc"]],
    	select: true
        });
        $('.table-update').click(function () {
	      	var data = $('#RemitInformation').DataTable()
		        .row( $(this).parents('tr') )
		        .data();
			
			$.ajax({
				type: "POST",
				url: "RemitEdit.php",
				data: {data : data}
			}).done(function(output) {
				alert(output);
			});	        
      	});
        
		var table = $('#RemitInformation').DataTable();
        $('#RemitInformation tbody').on( 'focusout', 'td', function () {
        	var cell = table.cell( this );
            cell.data( this.innerHTML );
        } );
        $(".isChecked").on( 'click', function () {
            if($(this).text() == "否")
            {
            	$(this).html('已收');
            }
            else
            {
            	$(this).html('否');
            }
        	var cell = table.cell( this );
            cell.data( this.innerHTML );
        });
        $(".BuyingInf").on( 'click', function () {
            var url = "BuyingInformationByRemitNumber.php?remitNumber=" + $(this).text();
        	var win = window.open(url, '_blank');
        	win.focus();
        });
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
			window.history.replaceState( {} , '對帳管理', 'http://mommyssecret.tw/MS/Employee/RemitChecking.php' );
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
	
	//To get all item id
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT * FROM  `RemitRecord` WHERE  `匯款日期` >= DATE_SUB( NOW( ) , INTERVAL 2 MONTH ) ")
	
	or die(mysql_error());
	
	echo "<table id=\"RemitInformation\">
	<thead><tr>
	<th>匯款編號 </th>
	<th>FB帳號</th>
    <th>FBID</th>
	<th>匯款金額</th>
	<th>應匯款金額</th>
	<th>匯款末五碼</th>
	<th>匯款日期</th>
	<th>Memo</th>
	<th>管理員備註</th>			
	<th>已收款</th>
	<th></th>
	</thead></tr><tbody>";
	
	while($row = mysql_fetch_array($result))
	{
		$isChecked = $row['已收款'] == 0 ? "否" : "已收";
		echo "<tr>";
		echo "<td class=\"BuyingInf\">".$row['匯款編號']."</td>";
		echo "<td>".$row[FB帳號]."</td>";
		echo "<td>".$row[FBID]."</td>";
		echo "<td>".$row[匯款金額]."</td>";
		echo "<td>".$row[應匯款金額]."</td>";
		echo "<td>".$row[匯款末五碼]."</td>";
		echo "<td>".$row[匯款日期]."</td>";
		echo "<td>".$row[Memo]."</td>";
		echo "<td contenteditable=\"true\">".$row[管理員備註]."</td>";
		echo "<td class=\"isChecked\">".$isChecked."</td>";
		echo "<td><span id=\"Icon\" class=\"table-update glyphicon glyphicon-edit\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
</body>