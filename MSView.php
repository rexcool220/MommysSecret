<?php 
require_once __DIR__ . '/vendor/autoload.php';

require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if(!session_id()) {
	session_start();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>View Records</title>

</head>

<body>

<?php

if(!isset($_GET['code']))
{
	require_once __DIR__ . '/vendor/autoload.php';
	if(!session_id()) {
		session_start();
	}
	
	$fb = new Facebook\Facebook([
	  'app_id' => '1540605312908660',
	  'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
	  'default_graph_version' => 'v2.6',
	]);
	$helper = $fb->getRedirectLoginHelper();
	
	$permissions = ['email']; // optional
	//$permissions = ['email','publish_actions','user_managed_groups']; // optional
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MSView.php', $permissions);
	
	//echo urldecode($loginUrl);
	header("location: ".$loginUrl);
}
else 
{
	if(!$accessToken)
	{
		$fb = new Facebook\Facebook([
				'app_id' => '1540605312908660',
				'app_secret' => '066f0c1bd42b77412f8d36776ee7b788',
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
			window.history.replaceState( {} , 'MSView', 'http://mommyssecret.tw/MSView.php' );
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

	// connect to the database
	
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT * FROM ShippingRecord order by SerialNumber")
	
	or die(mysql_error());
	
	
	
	// display data in table
	
	echo "<p><b>View All</b> | <a href='MSView-paginated.php?page=1'>View Paginated</a></p>";
	
	
	
	echo "<table border='1' cellpadding='10'>";
	
	echo "<tr>
			<th>FB帳號</th>
			<th>FBID</th>
			<th>品項</th>
			<th>單價</th>
			<th>數量</th>
			<th>匯款日期</th>
			<th>出貨日期</th>
			<th>SerialNumber</th>
			<th>匯款編號</th>
			<th>確認收款</th>
			<th>
			</th>
			<th>
			</th>
		</tr>";
	
	
	
	// loop through results of database query, displaying them in the table
	
	while($row = mysql_fetch_array( $result )) {
	
	
	
	// echo out the contents of each row into a table
	
	echo "<tr>";
	
	echo '<td>' . $row['FB帳號'] . '</td>';
	
	echo '<td>' . $row['FBID'] . '</td>';
	
	echo '<td>' . $row['品項'] . '</td>';
	
	echo '<td>' . $row['單價'] . '</td>';
	
	echo '<td>' . $row['數量'] . '</td>';
	
	echo '<td>' . $row['匯款日期'] . '</td>';
	
	echo '<td>' . $row['出貨日期'] . '</td>';
	
	echo '<td>' . $row['SerialNumber'] . '</td>';
	
	echo '<td>' . $row['匯款編號'] . '</td>';
	
	echo '<td>' . $row['確認收款'] . '</td>';
	
	echo '<td><a href="MSEdit.php?SerialNumber=' . $row['SerialNumber'] . '">Edit</a></td>';
	
	echo '<td><a href="MSDelete.php?SerialNumber=' . $row['SerialNumber'] . '">Delete</a></td>';
	
	echo "</tr>";
	
	}
	
	
	
	// close table>
	
	echo "</table>";
	
	?>
	
	<p><a href="MSNew.php">Add a new record</a></p>
	
	
	
	</body>
	
	</html>
<?php
}
?>