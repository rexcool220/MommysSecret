<?php

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
	$url = "https://docs.google.com/forms/d/1kCA1gdJDOD0X0hPfHdW4E9z0k7HPuBl0AaimQLnpAnw/viewform?entry.743012400=rexcool";
	$data = ObtainPageSource($url);
	$googleFormPattern = "/(?<=<form action=\")[^\"]*/";
	$formActionUrl = '';

	if (preg_match($googleFormPattern,$data, $matches)) {
		$formActionUrl = $matches[0];
	}
	else {
		echo "Not match";
	}
	$redirectUrl = "http://localhost/MommysSecret/RedirectedPage.php";
	$replacement = "<script type=\"text/javascript\">var submitted=false;</script>
		<iframe name=\"hidden_iframe\" id=\"hidden_iframe\"
		style=\"display:none;\" onload=\"if(submitted)
		{window.location='".$redirectUrl."';}\"></iframe>
		<form action=\"".$formActionUrl
		."\" method=\"post\"
		target=\"hidden_iframe\" onsubmit=\"submitted=true;\">";
	
	echo preg_replace("/(<form[^>]*>)/", $replacement, $data);
	
	//echo htmlspecialchars($replacedString);
?>
	