<?php
header("Content-Type:text/html; charset=utf-8");
	if(!session_id()) {
		session_start();
	}
	$customedGoogleForm = $_SESSION['googleFormUrl'];
	
	$fbAccount = $_SESSION['fbAccount'];
	
	//$customedGoogleForm = 'https://docs.google.com/forms/d/1kCA1gdJDOD0X0hPfHdW4E9z0k7HPuBl0AaimQLnpAnw/viewform?entry.743012400=';
	
	$data = ObtainPageSource($customedGoogleForm.$fbAccount);
	
	$redirectUrl = "http://localhost/MommysSecret/Client.php";
	
	if(preg_match("/(?<=<form action=\")[^\"]*/", $data, $matches)) {
		$googleSpreadsheetUrl = $matches[0];
	}
	else {
		echo 'not matched';
	}
	
	if(preg_match("/<form[\s\S]*form>/", $data, $matches)) {
		$stringBetweenTagForm = $matches[0];
		echo htmlspecialchars($temp);
	}
	else {
		echo 'not matched';
	}
	
	$replacement = "<script type=\"text/javascript\">var submitted=false;</script>
		<iframe name=\"hidden_iframe\" id=\"hidden_iframe\"
		style=\"display:none;\" onload=\"if(submitted)
		{window.location='".$redirectUrl."';}\"></iframe>
		<form action=\"".$googleSpreadsheetUrl
		."\" method=\"post\"
		target=\"hidden_iframe\" onsubmit=\"submitted=true;\">";
	
	echo preg_replace("/(<form[^>]*>)/", $replacement, $stringBetweenTagForm);
	
	function ObtainPageSource($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		return $data;
	}
?>
	