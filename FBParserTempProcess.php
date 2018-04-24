<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once "./vendor/google/apiclient/examples/templates/base.php";
require_once 'ConnectMySQL.php';
header("Content-Type:text/html; charset=utf-8");
if (!session_id()) {
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
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
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

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

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
<?php
include('ConnectMySQL.php');

$comment = $_POST['comment'];

$commodity = $_POST['commodity'];

$itemID = $_POST['itemID'];
//To get all item id

$pieces = explode("\n", $commodity);

$itemMonthCategory = $pieces[0];
if (preg_match("/^\[([^\]]+)\]/", $itemMonthCategory, $matches)) {
    $itemMonthCategory = $matches[1];
}
$dueDate = $pieces[1];
if (preg_match("/^\[([^\]]+)\]/", $dueDate, $matches)) {
    $dueDate = $matches[1];
}
$itemName = $pieces[2];
if (preg_match("/^\[([^\]]+)\]/", $itemName, $matches)) {
    $itemName = $matches[1];
}
$itemPrice = $pieces[3];
if (preg_match("/：([0-9]+)/", $itemPrice, $matches)) {
    $itemPrice = $matches[1];
}

$FBID = "";

//$comment = $_POST['comment'];
$pieces = explode("管理", $comment);

$sql = "SELECT 規格 FROM  `ItemCategory` WHERE ItemID = $itemID";
$specResult = mysql_query($sql,$con);
if (!$specResult) {
    die('Invalid query: ' . mysql_error());
}
echo "<p id=\"specList\">規格:";
while($specRow = mysql_fetch_array($specResult)) {
    echo $specRow['規格'].'/';
}
echo "</p>";

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
	<th>備註</th>	    		
	<th>數量</th>
	<th></th>
	</thead></tr><tbody>";

for ($i = 0; $i < count($pieces); $i = $i + 1) {
    $subPieces = explode("\n", $pieces[$i]);

    if(count($subPieces) > 4){
        $account = trim($subPieces[count($subPieces) - 3]);
        $message = $subPieces[count($subPieces) - 2];

        $result = mysql_query("SELECT FBID FROM `Members` WHERE FB帳號 = \"$account\"")
        or die(mysql_error());

        $row = mysql_fetch_array($result);
        $FBID = $row['FBID'];

        echo "<tr>";
        echo "<td></td>";
        echo "<td contenteditable=\"true\">" . $account . "</td>";
        echo "<td contenteditable=\"true\">" . $FBID . "</td>";
        echo "<td>" . $itemMonthCategory . "</td>";
        echo "<td>" . $itemID . "</td>";
        echo "<td>" . $itemName . "</td>";
        echo "<td contenteditable=\"true\"></td>";
        echo "<td contenteditable=\"true\">" . $itemPrice . "</td>";
        echo "<td contenteditable=\"true\"></td>";
        echo "<td contenteditable=\"true\">" . $message . "</td>";
        echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span>
                    <span class=\"table-duplicate glyphicon glyphicon-duplicate\"></span></td>";
        echo "</tr>";
    }
    else{
        continue;
    }


}

echo "</tbody></table>";
?>
</body>

<script type="text/javascript">
    // Activate an inline edit on click of a table cell
    $(document).ready(function () {
        $('#Comments').dataTable({
            "fixedHeader": {
                header: true
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: '上傳資料',
                    action: function (e, dt, node, config) {
                        //verify columns
                        var itemNames =
                            this
                                .columns(5)
                                .data()
                                .eq(0);      // Reduce the 2D array into a 1D array of data
                        var itemSpecs =
                            this
                                .columns(6)
                                .data()
                                .eq(0);      // Reduce the 2D array into a 1D array of data
                        var itemPrices =
                            this
                                .columns(7)
                                .data()
                                .eq(0);      // Reduce the 2D array into a 1D array of data

                        var itemCounts =
                            this
                                .columns(9)
                                .data()
                                .eq(0);      // Reduce the 2D array into a 1D array of data

                        for (var i = 0; i < itemNames.length; ++i) {
                            if (itemNames[i] == "") {
                                alert("請檢查品項!");
                                return false;
                            }
                        }
                        for (var i = 0; i < itemSpecs.length; ++i) {
                            if ((itemSpecs[i] == "") || ($("#specList").text().includes(itemSpecs[i]) == false)) {
                                alert(itemSpecs[i]);
                                alert("請檢查規格!");
                                return false;
                            }
                        }

                        for (var i = 0; i < itemPrices.length; ++i) {
                            if (itemPrices[i] == "") {
                                alert("請檢查價格!");
                                return false;
                            }
                        }

                        for (var i = 0; i < itemCounts.length; ++i) {
                            if (itemCounts[i] == "") {
                                alert("請檢查數量!");
                                return false;
                            }
                        }


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
                            data: {data: data}
                        }).done(function (output) {
                            alert(output);
                        });
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
                        var PriceColumns =
                            this
                                .columns(7)
                                .data()
                                .eq( 0 );      // Reduce the 2D array into a 1D array of data
                        var combinArray = [];

                        for (var i = 0; i < SpecColumns.length; ++i) {
                            combinArray.push([SpecColumns[i], itemCount[i], PriceColumns[i]]);
                        }

                        combinArray.sort(function sortFunction(a, b) {
                            if (a[0] === b[0]) {
                                return 0;
                            }
                            else {
                                return (a[0] < b[0]) ? -1 : 1;
                            }
                        });

                        var SpecArray = [], CountArray = [], PriceArray = [],prev;

                        for ( var i = 0; i < combinArray.length; i++ ) {
                            if ( combinArray[i][0] !== prev ) {
                                SpecArray.push(combinArray[i][0]);
                                CountArray.push(Number(combinArray[i][1]));
                                PriceArray.push(Number(combinArray[i][2]));
                            } else {
                                CountArray[CountArray.length-1] = Number(CountArray[CountArray.length-1]) + Number(combinArray[i][1]);
                            }
                            prev = combinArray[i][0];
                        }

                        var specCount = "";

                        for (var i = 0; i < SpecArray.length; ++i) {
                            specCount = specCount + SpecArray[i] + " + " + CountArray[i] + "  $" + PriceArray[i] + "<br>";
                        }

                        $( "#dialogText" ).html(specCount);

                        $( "#dialog" ).dialog();
                    }
                },
            ],
            "lengthMenu": [[-1], ["All"]],
            "bLengthChange": false,
            "order": [[0, "asc"]],
            "select": {
                style: 'os',
                selector: 'td:first-child'
            }
        });
        $('.table-remove').click(function () {
            $('#Comments').DataTable()
                .row($(this).parents('tr'))
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
        var table = $('#Comments').DataTable();
        $('#Comments tbody').on('focusout', 'td', function () {
            var cell = table.cell(this);
            cell.data(this.innerHTML);
        });
    });

</script>