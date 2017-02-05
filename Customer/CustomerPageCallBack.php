<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
include_once "../vendor/google/apiclient/examples/templates/base.php";
require_once '../ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
    session_start();
}
?>
<!DOCTYPE html>
<head>
	<title>點單</title>
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
	
	<style>
		.btn-circle {
		  width: 30px;
		  height: 30px;
		  text-align: center;
		  padding: 6px 0;
		  font-size: 12px;
		  line-height: 1.428571429;
		  border-radius: 15px;
		}
		.btn-circle.btn-lg {
		  width: 50px;
		  height: 50px;
		  padding: 10px 16px;
		  font-size: 18px;
		  line-height: 1.33;
		  border-radius: 25px;
		}
		.btn-circle.btn-xl {
		  width: 70px;
		  height: 70px;
		  padding: 10px 16px;
		  font-size: 24px;
		  line-height: 1.33;
		  border-radius: 35px;
		}
	</style>
</head>
<body>
<script type="text/javascript">
    $(document).ready(function () {                
        $('#ItemInformation').dataTable({                       
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "asc" ]],
    	select: true
        });
    });
</script>


<?php 
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
			'app_id' => '198155157308846',
			'app_secret' => '3f31e64dbccb7ccc03c35398d5dc0652',
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
		window.history.replaceState( {} , 'CustomerPage', 'http://mommyssecret.tw/Customer/CustomerPageCallback.php' );
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
	$FBID = $userNode->getId();
	
	$sql = "SELECT * FROM `ShippingRecord` where (匯款日期 = \"0000-00-00\" OR 出貨日期 = \"0000-00-00\") AND FBID = ".$FBID;
	
	$result = mysql_query($sql)
	
	or die(mysql_error());
	
	$ItemInformation = "<table id=\"ItemInformation\">
	<thead><tr>
	<th>FB帳號</th>
	<th>品項</th>
	<th>單價</th>
	<th>數量</th>
	<th>匯款日期</th>
	<th>出貨日期</th>
	<th>月份</th>
	<th>規格</th>
	<th>FB網址</th>
	</thead></tr><tbody>";

	while($row = mysql_fetch_array($result))
	{
		$ItemInformation = $ItemInformation."<tr>";
		$ItemInformation = $ItemInformation. "<td>".$row[FB帳號]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[品項]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[單價]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[數量]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[匯款日期]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[出貨日期]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[月份]."</td>";
		$ItemInformation = $ItemInformation. "<td>".$row[規格]."</td>";
		$ItemInformation = $ItemInformation. "<td><button type=\"button\" class=\"btn btn-success btn-circle\" onclick=\"window.open('https://www.facebook.com/groups/607414496082801/permalink/".$row[ItemID]."', '_blank')\"><i class=\"glyphicon glyphicon-link\"></i></button></td>";
		$ItemInformation = $ItemInformation. "</tr>";
	}
	$ItemInformation = $ItemInformation. "</tbody></table>";
	?>
<div class="row">
	<div class="form-group has-feedback col-lg-2">
		<button type="button" class="btn btn-default btn-circle btn-xl"><i class="glyphicon glyphicon-ok"></i></button>
		<button type="button" class="btn btn-primary btn-circle btn-xl"><i class="glyphicon glyphicon-list"></i></button>
		<button type="button" class="btn btn-success btn-circle btn-xl"><i class="glyphicon glyphicon-link"></i></button>
		<button type="button" class="btn btn-info btn-circle btn-xl"><i class="glyphicon glyphicon-ok"></i></button>
		<button type="button" class="btn btn-warning btn-circle btn-xl"><i class="glyphicon glyphicon-remove"></i></button>
		<button type="button" class="btn btn-danger btn-circle btn-xl"><i class="glyphicon glyphicon-heart"></i></button>
	</div>
	<div class="form-group has-feedback col-lg-10">
		<?php 
			echo $ItemInformation;
// 			echo $sql;
		?>
	</div>
</div>	