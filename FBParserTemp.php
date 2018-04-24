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
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
    <title>點單Temp</title>
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
<form action="http://mommyssecret.tw/MS/FBParserTempProcess.php" id="commentForm" method="post">
    itemID: <input type="text" name="itemID">
    <input type="submit">
</form>
<br>
商品欄位<br>
<textarea rows="4" cols="50" name="commodity" form="commentForm"></textarea><br>
留言欄位<br>
<textarea rows="4" cols="50" name="comment" form="commentForm"></textarea>
</body>

