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
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
</head>

<body>
<script type="text/javascript">
    $(document).ready(function () {
        $('#memberView').dataTable();
    });
</script>
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
	
	$loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MemberView.php', $permissions);
	
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
			window.history.replaceState( {} , 'MemberView', 'http://mommyssecret.tw/MemberView.php' );
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

	// connect to the database
	
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT * FROM Members")
	
	or die(mysql_error());
	
	
	
	// display data in table
	
// 	echo "<p><b>View All</b> | <a href='MemberView-paginated.php?page=1'>View Paginated</a></p>";
	
	
	
	echo "<table id=\"memberView\" class=\"display\">";
	
	echo "<thead><tr>
			<th>姓名</th>
			<th>FB帳號</th>
		    <th>FBID</th>
			<th>手機號碼</th>
			<th>郵遞區號＋地址</th>				
			<th>全家店到店服務代號</th>
			<th>寄送方式</th>
			<th>運費</th>
			<th>備註</th>
			<th>回饋金餘額</th>
			<th>
			</th>
			<th>
			</th>
			</thead></tr><tbody>";
	
	
	
	// loop through results of database query, displaying them in the table
	
	while($row = mysql_fetch_array( $result )) {
	
	
	
	// echo out the contents of each row into a table
	
	echo "<tr>";
	
	echo '<td>' . $row['姓名'] . '</td>';
	
	echo '<td>' . $row['FB帳號'] . '</td>';
	
	echo '<td>' . $row['FBID'] . '</td>';
	
	echo '<td>' . $row['手機號碼'] . '</td>';
	
	echo '<td>' . $row['郵遞區號＋地址'] . '</td>';	
	
	echo '<td>' . $row['全家店到店服務代號'] . '</td>';
	
	echo '<td>' . $row['寄送方式'] . '</td>';
	
	echo '<td>' . $row['運費'] . '</td>';
	
	echo '<td>' . $row['備註'] . '</td>';
	
	echo '<td>' . $row['Rebate'] . '</td>';
	
	echo '<td><a href="MemberEdit.php?FBID=' . $row['FBID'] . '">Edit</a></td>';
	
	echo '<td><a href="MemberDelete.php?FBID=' . $row['FBID'] . '">Delete</a></td>';
	
	echo "</tr>";
	
	}
	
	
	
	// close table>
	
	echo "</tbody></table>";
	
	?>
	
	<p><a href="MemberNew.php">Add a new record</a></p>
	
	
	
	</body>
	
	</html>
<?php
}
?>