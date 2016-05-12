<?php

require_once realpath(dirname(__FILE__) . '/vendor/autoload.php');

include_once "./vendor/google/apiclient/examples/templates/base.php";

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$client = new Google_Client();

putenv("GOOGLE_APPLICATION_CREDENTIALS=Mommyssecret-e24d4b121c15.json");

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

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance() or die('asdf');

// $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
// $spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();

// $spreadsheet = $spreadsheetFeed->getByTitle('MySpreadsheet');
// $worksheetFeed = $spreadsheet->getWorksheetFeed();
// $worksheet = $worksheetFeed->getByTitle('Sheet1');
// $cellFeed = $worksheet->getCellFeed();
// $cell = $cellFeed->getCell(1, 2);
// echo $cell->getCellIdString();

// $service = new Google_Service_Drive($client);
// $results = $service->files->get($fileId);

// $url = "https://spreadsheets.google.com/feeds/list/$fileId/default/private/full";
// $method = 'GET';
// $headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
// $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// $resp = $httpClient->request($method, $url);
// $body = $resp->getBody()->getContents();
// $code = $resp->getStatusCode();
// $reason = $resp->getReasonPhrase();
// echo "$code : $reason\n\n";
// echo "$body\n";



