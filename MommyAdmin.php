<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';

header("Content-Type:text/html; charset=utf-8");

if (!session_id()) {
    session_start();
}
?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="Admin.css?20170110">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
        <title>MommysSecret</title>
        <script>
            $(function () {
                $('#toggle-event').change(function () {
                    if ($(this).prop('checked')) {
                        $.ajax({
                            type: "POST",
                            url: "MommyAdminSetting.php",
                            data: {isOpen: 'true'}
                        }).done(function (output) {
// 				alert(output);
                        });
                    }
                    else {
                        $.ajax({
                            type: "POST",
                            url: "MommyAdminSetting.php",
                            data: {isOpen: 'false'}
                        }).done(function (output) {
// 				alert(output);
                        });
                    }
                })
            })
        </script>
    </head>

<body>

<?php

if (!isset($_GET['code'])) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (!session_id()) {
        session_start();
    }

    $fb = new Facebook\Facebook([
        'app_id' => '198155157308846',
        'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
        'default_graph_version' => 'v2.6',
    ]);
    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email']; // optional
    //$permissions = ['email','publish_actions','user_managed_groups']; // optional

    $loginUrl = $helper->getLoginUrl('http://mommyssecret.tw/MS/MommyAdmin.php', $permissions);

    //echo urldecode($loginUrl);
    header("location: " . $loginUrl);
} else {
    if (!$accessToken) {
        $fb = new Facebook\Facebook([
            'app_id' => '198155157308846',
            'app_secret' => 'd338a067b933196d2be2c4c4c87c1205',
            'default_graph_version' => 'v2.6',
        ]);
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (empty($accessToken) && !empty($_SESSION['accessToken'])) {
            $accessToken = $_SESSION['accessToken'];
        } else if (!empty($accessToken)) {
            $_SESSION['accessToken'] = $accessToken;
        } else if (!empty($accessToken) && !empty($_SESSION['accessToken'])) {
            echo "accessToken error";
            exit;
        }
        $fb->setDefaultAccessToken($accessToken);
    }

    ?>
    <script>
        window.history.replaceState({}, 'MommysAdmin', 'http://mommyssecret.tw/MS/MommyAdmin.php');
    </script>
<?php

try {
    $response = $fb->get('/me');
    $userNode = $response->getGraphUser();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
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

if (($type == "管理員") || ($type == "共用帳號")) {
    echo "<p hidden id=\"accountType\">$type</p>";
    echo "<p hidden id=\"fbAccount\">$fbAccount</p>";
} else {
    echo "$fbAccount : 你沒有權限";
    exit;
}
$sql = "SELECT * FROM `Setting`;";
$result = mysql_query($sql, $con);

if (!$result) {
    die('Invalid query: ' . mysql_error());
}

$row = mysql_fetch_array($result);

$isOpen = $row['isOpen'];

$isOpenCheckBox = "";

if ($isOpen == 1) {
    $isOpenCheckBox = "
			<tr>
				<td>
					<input id=\"toggle-event\" type=\"checkbox\" data-toggle=\"toggle\" data-on=\"開放匯款\" data-off=\"不開放匯款\" checked>
				</td>
			</tr>";
} else {
    $isOpenCheckBox = "
			<tr>
				<td>
					<input id=\"toggle-event\" type=\"checkbox\" data-toggle=\"toggle\" data-on=\"開放匯款\" data-off=\"不開放匯款\">
				</td>
			</tr>";
}

$AdminTable = "
		<table id=\"AdminTable\">
			<tr>
				<td>
			        <a target=\"_blank\" href=\"/MS/Members/MemberView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-user\"></span> 會員資料
			        </a>				
				</td>
				<td>
			        <a target=\"_blank\" href=\"MSView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-shopping-cart\"></span> 訂單管理
			        </a>			
				</td>				
			</tr>
			<tr>	
				<td>
			        <a target=\"_blank\" href=\"BuyingInformationByQuery.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-search\"></span> 會員結帳代查詢
			        </a>				
				</td>					
				<td>
			        <a target=\"_blank\" href=\"/MS/Employee/RemitChecking.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-piggy-bank\"></span> 對帳管理
			        </a>				
				</td>			
			</tr>
			<tr>
				<td>
			        <a target=\"_blank\" href=\"ItemCategoryView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-download-alt\"></span> 到貨管理
			        </a>
				</td>							
				<td>
			        <a target=\"_blank\" href=\"NotRemitList.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-thumbs-down\"></span> 未匯款清單
			        </a>				
				</td>			
			</tr>		
			<tr>
				<td>
			        <a target=\"_blank\" href=\"/MS/Employee/ShippingCheckingIndex.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-plane\"></span> 出貨管理
			        </a>				
				</td>							
				<td>
			        <a target=\"_blank\" href=\"TagByItemID.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-tags\"></span> Tag小工具
			        </a>
				</td>			
			</tr>		
			<tr>
				<td>
			        <a target=\"_blank\" href=\"FBParserTemp.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-check\"></span> 點單系統
			        </a>			
				</td>			
				<td>
			        <a target=\"_blank\" href=\"TagByUnknownMembers.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-question-sign\"></span> 失蹤會員小幫手 
			        </a>			
				</td>			
			</tr>
			<tr>
				<td>
			        <a target=\"_blank\" href=\"/MS/CreateCommodityTemp/FBPoster.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-comment\"></span> 開團小幫手
			        </a>
				</td>
				<td>
			        <a target=\"_blank\" href=\"/MS/Members/RebateView.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-usd\"></span> 回饋金查詢
			        </a>
				</td>	
			</tr>	
			<tr>
				<td>
			        <a target=\"_blank\" href=\"/MS/Employee/TagCustomerForShipping.php\" class=\"btn btn-default btn-lg btn-block\">
			          <span class=\"glyphicon glyphicon-volume-up\"></span> 出貨通知
			        </a>
				</td>
			</tr>
			<tr>" .
    $isOpenCheckBox .
    "</tr>
		</table>";
echo $AdminTable;
?>
    </body>

    </html>
    <?php
}
?>