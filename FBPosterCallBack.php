<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if(!session_id()) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>FBParser</title>
	<meta name="format-detection" content="telephone=no">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> 
	
	<script src="~/Content/bootstrapValidator/js/bootstrapValidator.min.js"></script>
    <link href="~/Content/bootstrapValidator/css/bootstrapValidator.min.css" rel="stylesheet" />
	
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
		window.history.replaceState( {} , 'FBPoseter', 'http://mommyssecret.tw/FBPosterCallBack.php' );
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
<form name="ajaxform" id="ajaxform" action="ajax-form-submit.php" method="POST">
	<div class="input-group">
	  <label>月份</label>
	  <select id="selector" class="form-control" name="month">
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
	<div class="form-group">
		<label>收單日期</label>
		<input id="datepicker" type="text" class="form-control" name="closeDate">
	</div>
	<div class="form-group">
		<label>品項</label>
		<input id="input1" type="text" class="form-control" name="itemName" placeholder="品項" required>
	</div>
	<div class="form-group">
	  <label>規格數量</label>
	  <select id="specNumbers" class="form-control" onchange="myFunction()">
	  	<option>0</option>
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
	  </select>
	</div>	
	<div id="inputList" class="form-group">
	</div>	
	

	<div class="form-group">
		<label>單價</label>
		<input id="input3" type="text" class="form-control" name="itemPrice">
	</div>
</form>
<input type="button"  id="simple-post" value="Run Code" />
<div id="simple-msg"></div>

<script>
$(document).ready(function()
{
$("#simple-post").click(function()
{
	$("#ajaxform").submit(function(e)
	{
		$("#simple-msg").html("<img src='loading.gif'/>");
		var postData = $(this).serializeArray();
		var formURL = $(this).attr("action");
		$.ajax(
		{
			url : formURL,
			type: "POST",
			data : postData,
			success:function(data, textStatus, jqXHR) 
			{
				$("#simple-msg").html('<pre><code class="prettyprint">'+data+'</code></pre>');

			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				$("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
			}
		});
	    e.preventDefault();	//STOP default action
	    e.unbind();
	});
		
	$("#ajaxform").submit(); //SUBMIT FORM
});

});
$( function() {
	$( "#datepicker" ).datepicker();
} );
function myFunction() {
	var specNumbers = parseInt(document.getElementById("specNumbers").value);
	$('#inputList').empty();
	for (i = 0; i < specNumbers; i++) {
		$('#inputList').append("<div class=\"input-group\"><label>規格" + i + "</label>");
		$('#inputList').append("<input id=\"input" + i + "\"type=\"text\" class=\"form-control\" name=\"itemSpec" + i + "\"></div>");
	}
}
</script>