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


if(!$accessToken)
{
	$fb = new Facebook\Facebook([
			'app_id' => '1540605312908660',
			'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
			'default_graph_version' => 'v2.6',
	]);

	if(empty($accessToken)&&!empty($_SESSION['accessToken']))
	{
		$accessToken = $_SESSION['accessToken'];
	}
	else if(!empty($accessToken)&&!empty($_SESSION['accessToken']))
	{
		echo "accessToken error";
		exit;
	}
	$fb->setDefaultAccessToken($accessToken);
}

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



// number of results to show per page

$per_page = 30;



// figure out the total pages in the database

$result = mysql_query("SELECT * FROM Members");

$total_results = mysql_num_rows($result);

$total_pages = ceil($total_results / $per_page);



// check if the 'page' variable is set in the URL (ex: view-paginated.php?page=1)

if (isset($_GET['page']) && is_numeric($_GET['page']))

{

$show_page = $_GET['page'];



// make sure the $show_page value is valid

if ($show_page > 0 && $show_page <= $total_pages)

{

$start = ($show_page -1) * $per_page;

$end = $start + $per_page;

}

else

{

// error - show first set of results

$start = 0;

$end = $per_page;

}

}

else

{

// if page isn't set, show first set of results

$start = 0;

$end = $per_page;

}



// display pagination



echo "<p><a href='MemberView.php'>View All</a> | <b>View Page:</b> ";

for ($i = 1; $i <= $total_pages; $i++)

{

echo "<a href='MemberView-paginated.php?page=$i'>$i</a> ";

}

echo "</p>";



// display data in table

echo "<table border='1' cellpadding='10'>";

echo "<tr>
		<th>姓名</th>
		<th>FB帳號</th>
        <th>FBID</th>
		<th>手機號碼</th>
		<th>郵遞區號地址</th>
		<th>全家店到店服務代號</th>
		<th>寄送方式</th>
		<th>運費</th>
		<th>備註</th>
		<th>回饋金餘額</th>
		<th>
		</th>
		<th>
		</th>
	</tr>";



// loop through results of database query, displaying them in the table

for ($i = $start; $i < $end; $i++)

{

// make sure that PHP doesn't try to show results that don't exist

if ($i == $total_results) { break; }



// echo out the contents of each row into a table

echo "<tr>";

echo '<td>' . mysql_result($result, $i, '姓名') . '</td>';

echo '<td>' . mysql_result($result, $i, 'FB帳號') . '</td>';

echo '<td>' . mysql_result($result, $i, 'FBID') . '</td>';

echo '<td>' . mysql_result($result, $i, '手機號碼') . '</td>';

echo '<td>' . mysql_result($result, $i, '郵遞區號地址') . '</td>';

echo '<td>' . mysql_result($result, $i, '全家店到店服務代號') . '</td>';

echo '<td>' . mysql_result($result, $i, '寄送方式') . '</td>';

echo '<td>' . mysql_result($result, $i, '運費') . '</td>';

echo '<td>' . mysql_result($result, $i, '備註') . '</td>';

echo '<td>' . mysql_result($result, $i, 'Rebate') . '</td>';

echo '<td><a href="MemberEdit.php?FBID=' . mysql_result($result, $i, 'FBID') . '">Edit</a></td>';

echo '<td><a href="MemberDelete.php?FBID=' . mysql_result($result, $i, 'FBID') . '">Delete</a></td>';

echo "</tr>";

}

// close table>

echo "</table>";



// pagination



?>

<p><a href="MemberNew.php">Add a new record</a></p>



</body>

</html>