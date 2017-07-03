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
	
	$params = array("sCompany" => "test", "sPassword" =>"test", "dsCusJson" => "[
	{
		\"epino\": \"A00018\",
		\"edelno\": \"1000005090\",
	}
	]"
		);
	
	$result = $client->TransReport_Json($params)->TransReport_JsonResult;
	
	echo $result;
	
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}


// print($client->TransData_Json("test", "test", json_encode($arr)));

?>
</body>
</html>