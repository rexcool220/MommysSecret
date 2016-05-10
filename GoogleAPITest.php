<?php

require_once realpath(dirname(__FILE__) . '/vendor/autoload.php');

include_once "./vendor/google/apiclient/examples/templates/base.php";

$client = new Google_Client();

putenv("GOOGLE_APPLICATION_CREDENTIALS=Mommyssecret-86f67288f5ad.json");

if ($credentials_file = checkServiceAccountCredentialsFile()) {
  // set the location manually
  $client->setAuthConfig($credentials_file);
} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
  // use the application default credentials
  $client->useApplicationDefaultCredentials();
} else {
  echo missingServiceAccountDetailsWarning();
  return;
}

$client->setApplicationName("Sheets API Testing");
$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);


$fileId = "1tLQ4_ZfSPI3fGn6PDI1fIhYcMOIcTDxa_h7sY1Ghcw0";

$tokenArray = $client->fetchAccessTokenWithAssertion();
$accessToken = $tokenArray["access_token"];

$service = new Google_Service_Drive($client);
$results = $service->files->get($fileId);




