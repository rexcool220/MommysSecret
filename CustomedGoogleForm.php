<?php
	$customedGoogleForm = $_GET['CustomedGoogleForm'];
	
	$fbAccount = $_GET['FbAccount'];
	
	$data = ObtainPageSource($customedGoogleForm.urlencode($fbAccount));
	
	$redirectUrl = "http://localhost/MommysSecret/Client.php?FbAccount=".urlencode($fbAccount);
	
	if(preg_match("/(?<=<form action=\")[^\"]*/", $data, $matches)) {
		$googleSpreadsheetUrl = $matches[0];
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
	
	echo preg_replace("/(<form[^>]*>)/", $replacement, $data);
	
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
	