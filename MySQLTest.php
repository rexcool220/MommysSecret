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
<title>OnSale</title>
</head>
<body>
<?php
try {
	$client = new SoapClient("http://Hctrt.hct.com.tw/EDI_WebService2/Service1.asmx?wsdl");
	
	$arr = array('epino' => 'A00001', 'ercsig' => 'Mary', 'ertel1' => '0911123456', 'eraddr' => "台中市大雅區中清路三段513號", 'eqamt' => 10);
	
	$params = array("company" => "test", "password" =>"test", "json" => "[
	{
		\"epino\": \"A00029\",
		\"ercsig\": \"Mary\",
		\"ertel1\": \"0911123456\",
		\"eraddr\": \"台中市大雅區中清路三段513號\",
		\"ejamt\": \"1\",
		\"eqamt\": \"10\"
	}
	]"
	);
	
	$result = $client->TransData_Json($params)->TransData_JsonResult;
	
// 	$array = json_decode($result, true);
// 	echo strlen($array[0]['image']);
	
	echo $result;
	
	
	// 	$params = array("sCompany" => "04814950011", "sPassword" =>"0922113355", "dsCusJson" => "[
// 	{
// 		\"epino\": \"A00017\",
// 		\"edelno\": \"8222536346\",
// 	}
// 	]"
// 		);
	
// 	$result = $client->TransReport_Json($params)->TransReport_JsonResult;
	
// 	echo $result;
	
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}


// print($client->TransData_Json("test", "test", json_encode($arr)));

?>
</body>
</html>