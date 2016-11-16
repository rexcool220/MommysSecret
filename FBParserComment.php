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
	
	<title>點單確認表</title>
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
	<div id="dialog" title="統計結果">
		<p id="dialogText"></p>
	</div>
<script type="text/javascript">
    // Activate an inline edit on click of a table cell  
    $(document).ready(function () {
        $('#Comments').dataTable({  
		dom: 'Bfrtip',
		buttons: [
		{
			text: '上傳資料',
			action: function ( e, dt, node, config ) {
		        jQuery.fn.pop = [].pop;
		        jQuery.fn.shift = [].shift;
		        var $rows = $('#Comments').find('tr:not(:hidden)');
		        var headers = [];
		        var data = [];
		        $($rows.shift()).find('th:not(:empty)').each(function () {
		            headers.push($(this).text().toLowerCase());
				});
		          
				//Turn all existing rows into a loopable array
				$rows.each(function () {
					var $td = $(this).find('td');
					var h = {};
			            
					//Use the headers from earlier to name our hash keys
					headers.forEach(function (header, i) {
						h[header] = $td.eq(i).text();   
					});
					data.push(h);
				});
				$.ajax({
					type: "POST",
					url: "ProcessComments.php",
					data: {data : data}
				}).done(function(output) {
					alert(output);
				});
			}
		},
        {
            text: '前往原始網址',
            action: function ( e, dt, node, config ) {
            	window.open("https://www.facebook.com/groups/607414496082801/permalink/" + <?php echo $_POST['ID'];?>,'_blank');
            }
        },
        {
            text: '統計數量',
            action: function ( e, dt, node, config ) {
				this.rows().every( function () {
				var d = this.data();
	            	 
				d.counter++; // update data source for the row
	            	 
				this.invalidate(); // invalidate the data DataTables has cached for this row
				} );
	                
	            var SpecColumns = 
	                this
					.columns(6)
					.data()
					.eq( 0 );      // Reduce the 2D array into a 1D array of data
				var itemCount = 
					this
					.columns(9)
					.data()
					.eq( 0 );      // Reduce the 2D array into a 1D array of data

				var combinArray = [];
												
	            for (var i = 0; i < SpecColumns.length; ++i) {
	            	combinArray.push([SpecColumns[i], itemCount[i]]);
	            }

	            combinArray.sort(function sortFunction(a, b) {
			        if (a[0] === b[0]) {
			            return 0;
			        }
			        else {
			            return (a[0] < b[0]) ? -1 : 1;
			        }
			    });

	            var SpecArray = [], CountArray = [], prev;
	            
	            for ( var i = 0; i < combinArray.length; i++ ) {
	                if ( combinArray[i][0] !== prev ) {
	                	SpecArray.push(combinArray[i][0]);
	                	CountArray.push(Number(combinArray[i][1]));
	                } else {
	                	CountArray[CountArray.length-1] = Number(CountArray[CountArray.length-1]) + Number(combinArray[i][1]);
	                }
	                prev = combinArray[i][0];
	            }

				var specCount = "";
	            
	            for (var i = 0; i < SpecArray.length; ++i) {
	            	specCount = specCount + SpecArray[i] + " + " + CountArray[i] + "<br>"; 
	            }
	            	            
	            $( "#dialogText" ).html(specCount);

	            $( "#dialog" ).dialog();
            }
        },
        {
            text: '刪除此項資料',
            action: function ( e, dt, node, config ) {
		        var itemID = <?php echo $_POST['ID']?>;
				$.ajax({
					type: "POST",
					url: "DeleteWholeItemID.php",
					data: {itemID : itemID}
				}).done(function(output) {
					alert(output);
				});
			}
        }
		],  
        "lengthMenu": [[-1], ["All"]],
        "bLengthChange": false,
    	"order": [[ 0, "asc" ]],
        "select": {
	            style:    'os',
	            selector: 'td:first-child'
        	}
        });
        $('.table-remove').click(function () {
	      	$('#Comments').DataTable()
	        .row( $(this).parents('tr') )
	        .remove()
	        .draw();
      	});
        $('.table-duplicate').click(function () {
        	var $clone = $(this).closest('tr').clone(true);
        	$('#Comments').DataTable()
        	.row
        	.add($clone)
        	.draw();
      	});
    });
  
</script>


<?php
if(!$accessToken)
{
	$fb = new Facebook\Facebook([
		'app_id' => '1540605312908660',
		'app_secret' => '9a3a69dcdc8a10b04da656e719552a69',
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
			window.history.replaceState( {} , '點單確認表', 'http://mommyssecret.tw/FBParserComment.php' );
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
	// 	echo "管理者 : $fbAccount";
	}
	else
	{
		echo "$fbAccount : 你不是管理者";
		exit;
	}
	
	//To get all item id
	include('ConnectMySQL.php');
	
	// get results from database
	
	$result = mysql_query("SELECT distinct ItemID FROM `ShippingRecord`")
	
	or die(mysql_error());
	
	$ItemIDs = array();
	
	while($row = mysql_fetch_array($result)){
		$ItemIDs[] = $row["ItemID"];
	}
	
	if(isset($_POST['ID'])) {
		$ID = $_POST['ID'];	
	}
	else 
	{
		echo "ID is empty";
		echo "<form method=\"POST\" action=\"FBParserComment.php\">
	 	<input type=\"text\" name=\"ID\" value=\"\"><p>
		<input type=\"submit\" value=\"輸入ID\"><p>
</form>";
		exit;
	}
	
	if (in_array($ID, $ItemIDs)) {
		echo "<font color=\"red\"><h1>這筆點過了喔，請再確認一次</h1></font>";
	}
	
	try {
		$response = $fb->get("/607414496082801_".$ID."?fields=message,comments.limit(999)");
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$result = $response->getDecodedBody();
	
	preg_match("/^\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]+\[([^\]]+)\][^\[]+/", $result["message"], $matches);
	$itemMonthCategory = $matches[1];
	$dueDate = $matches[2];
	$itemName = $matches[3];
	preg_match("/(^[0-9]+)/", $matches[4], $priceMatches);

	$itemPrice = $priceMatches[1];
	
	echo "<table id=\"Comments\">
	<thead><tr>
	<th>時間</th>	    		
	<th>FB帳號</th>
	<th>FBID</th>
	<th>月份</th>	    		
	<th>ItemID</th>	    		
	<th>品項</th>
	<th>規格</th>
	<th>單價</th>
	<th>折扣</th>	    		
	<th>數量</th>
	<th></th>
	</thead></tr><tbody>";
	
	for($i = 0; $i < count($result["comments"]["data"]); $i++)
	{
		echo "<tr>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["created_time"]."</td>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["from"]["name"]."</td>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["from"]["id"]."</td>";
		echo "<td contenteditable=\"true\">".$itemMonthCategory."</td>";
		echo "<td contenteditable=\"true\">".$ID."</td>";
		echo "<td contenteditable=\"true\">".$itemName."</td>";
		echo "<td contenteditable=\"true\"></td>";
		echo "<td contenteditable=\"true\">".$itemPrice."</td>";
		echo "<td contenteditable=\"true\"></td>";
		echo "<td contenteditable=\"true\">".$result["comments"]["data"][$i]["message"]."</td>";
		echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span>
	    		<span class=\"table-duplicate glyphicon glyphicon-duplicate\"></span></td>";
		echo "</tr>";
	}
	
	echo "</tbody></table>";
	?>
</body>
	
