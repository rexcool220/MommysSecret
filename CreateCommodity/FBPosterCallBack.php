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
<html>
<head>
	<title>開團小幫手</title>
	<meta name="format-detection" content="telephone=no">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.js"></script>
</head>
<body>

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
		window.history.replaceState( {} , 'FBPoseter', 'http://mommyssecret.tw/CreateCommodity/FBPoster.php' );
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
	    	 
//         echo $userNode->getId();	
	}
	else
	{
	    echo "$fbAccount : 你不是管理者";
	    exit;
	}
	//To get all item id
	include('ConnectMySQL.php');
	
	try {
	    $response = $fb->get("/607414496082801");
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
	    echo 'Graph returned an error: ' . $e->getMessage();
	    exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
	    exit;
	}
?>
<div class="form-group">

</div>

<form data-toggle="validator" role="form" name="ajaxform" id="ajaxform" action="ajax-form-submit.php" method="POST">
<div class="row">
<div class="col-lg-8 col-lg-offset-2">
	<div class="form-inline row">
		<div class="form-group has-feedback col-lg-2">
			<label class="btn btn-primary" for="my-file-selector">
			    <input id="my-file-selector" type="file" style="display:none;" name="my-file-selector">
			上傳照片
			</label>
		</div>
		<div class="form-group has-feedback">
			<input id="fileToBeUpload" type="text" class="form-control" name="fileToBeUpload" data-error="請選擇上傳檔案" required>
			<!-- 				<span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
		</div>      
	</div>
	<div class="row">	
		<div class="form-group has-feedback col-lg-6">
		  <label class="control-label" for="monthSelector">月份:</label>
			  <select id="monthSelector" class="form-control" name="month" required>
			  	<option disabled selected value></option>
			    <option>1</option>
			    <option>2</option>
			    <option>3</option>
			    <option>4</option>
			    <option>5</option>
			    <option>6</option>
			    <option>7</option>
			    <option>8</option>
			    <option>9</option>
			    <option>10</option>
			    <option>11</option>
			    <option>12</option>
			  </select>
		</div>
		<div class="form-group has-feedback col-lg-6">
			<label class="control-label" for="datepicker">收單日期:</label>
				<input id="datepicker" type="text" class="form-control" name="closeDate" data-error="請填寫收單日期" required>
				<!-- 				<span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
			<div class="help-block with-errors"></div>
		</div>
	</div>
	<div class="row">	
		<div class="form-group has-feedback col-lg-8">
			<label class="control-label" for="itemName">品項:</label>
				<input id="itemName" type="text" class="form-control" name="itemName" data-error="請填寫品項" required>
				<!-- 				<span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
			<div class="help-block with-errors"></div>
		</div>
	</div>
	<div class="row">
		<div class="form-group has-feedback col-lg-8">
		  <label class="control-label" for="specNumbers">規格數量:</label>
			  <select id="specNumbers" class="form-control" name="itemSpecCounts" onchange="myFunction()" required>
			  	<option disabled selected value></option>
			    <option>1</option>
			    <option>2</option>
			    <option>3</option>
			    <option>4</option>
			    <option>5</option>
			    <option>6</option>
			    <option>7</option>
			    <option>8</option>
			    <option>9</option>
			    <option>10</option>
			    <option>11</option>
			    <option>12</option>
			    <option>13</option>
			    <option>14</option>
			    <option>15</option>
			    <option>16</option>
			    <option>17</option>
			    <option>18</option>
			    <option>19</option>
			    <option>20</option>
			    <option>21</option>
			    <option>22</option>
			    <option>23</option>
			    <option>24</option>
			    <option>25</option>
			    <option>26</option>
			    <option>27</option>
			    <option>28</option>
			    <option>29</option>
			    <option>30</option>
			  </select>
		</div>	
	</div>
	<div class="row">		
		<div id="inputList" class="form-group">
		</div>	
	</div>
	<div class="row">
		<div class="form-group has-feedback col-lg-8">
			<div class="form-group has-feedback">
				<label class="control-label" for="vendor">廠商:</label>
				<input id="vendor" type="text" class="form-control" name="vendor" data-error="請填寫廠商" required>
<!-- 				<span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group has-feedback col-lg-8">					
			<div class="form-group has-feedback">
				<label class="control-label" for="itemComment">商品解說:</label>
				<textarea id="itemComment" type="text" class="form-control" rows="20" name="itemComment" data-error="請填寫商品解說" required></textarea>
				<!-- 				<span class="glyphicon form-control-feedback" aria-hidden="true"></span> -->
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>				
	<div class="form-group">
		<input type="button" class="btn btn-primary" id="simple-post" value="確定開團!!" />
	</div>
</div>
</div>	
</form>

<div id="simple-msg"></div>

<script>
$(document).ready(function()
{
	$("#simple-post").click(function()
	{
		$("#simple-post").prop("disabled",true);
		$('#ajaxform').validator().on('submit', function (e) {
			if (e.isDefaultPrevented()) 
			{
			}
			else
			{
// 				var postData = $(this).serializeArray();
				var form = $('form')[0]; // You need to use standart javascript object here
				var formData = new FormData(form);
				var formURL = $(this).attr("action");
				$.ajax(
				{
					url : formURL,
					type: "POST",
					data : formData,
				    contentType: false,
				    processData: false,
					success:function(data, textStatus, jqXHR) 
					{
						$("#simple-msg").html('<pre><code class="prettyprint">'+data+'</code></pre>');
	
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						$("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
					}
				}).done(function(output) {
					$("#simple-post").prop("disabled",false);
					alert("發佈成功!!!");
				});
			    e.preventDefault();	//STOP default action
			    e.unbind();
			}
		})
		$("#ajaxform").submit(); //SUBMIT FORM
	});

    $("#my-file-selector").on('change', function() {
    	$('#fileToBeUpload').val($('input[type=file]')[0].files[0].name);
	})    
});
$( function() {
	$( "#datepicker" ).datepicker();
} );
function myFunction() {
	var specNumbers = parseInt(document.getElementById("specNumbers").value);
	$('#inputList').empty();
	for (i = 0; i < specNumbers; i++) {
		$('#inputList').append("<div class=\"form-group has-feedback col-lg-3\"><label class=\"control-label\" for=\"itemSpec" + i + "\">規格" + (i + 1).toString() + ":</label><input id=\"itemSpec" + i + "\" type=\"text\" class=\"form-control\" name=\"itemSpec" + i + "\" required></div>" +
							   "<div class=\"form-group has-feedback col-lg-3\"><label class=\"control-label\" for=\"itemPrice" + i + "\">單價" + (i + 1).toString() + ":</label><input id=\"itemPrice" + i + "\" type=\"number\" class=\"form-control\" name=\"itemPrice" + i + "\" required></div>" +
							   "<div class=\"form-group has-feedback col-lg-3\"><label class=\"control-label\" for=\"itemCost" + i + "\">成本" + (i + 1).toString() + ":</label><input id=\"itemCost" + i + "\" type=\"number\" class=\"form-control\" name=\"itemCost" + i + "\" required></div>" +
							   "<div class=\"form-group has-feedback col-lg-3\"><label class=\"control-label\" for=\"itemShopPrice" + i + "\">批發價" + (i + 1).toString() + ":</label><input id=\"itemShopPrice" + i + "\" type=\"number\" class=\"form-control\" name=\"itemShopPrice" + i + "\" required></div>"
		);
	}
	$('#ajaxform').validator('update');
}
</script>