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

$comment = "Arica Lo
Arica Lo +2併Gill Fang
管理
讚顯示更多心情 · 回覆 · 11小時
林小妤
林小妤 1
管理
讚顯示更多心情 · 回覆 · 11小時
Shu-yi Tsai
Shu-yi Tsai 2
管理
讚顯示更多心情 · 回覆 · 11小時
Shelly W Chen
Shelly W Chen 1
管理
讚顯示更多心情 · 回覆 · 11小時
Yi Fang Lee
Yi Fang Lee +1
管理
讚顯示更多心情 · 回覆 · 11小時
Shelley  Tang
Shelley Tang 2
管理
讚顯示更多心情 · 回覆 · 11小時
Yi-Chen Hsieh
Yi-Chen Hsieh +2併Gill Fang
管理
讚顯示更多心情 · 回覆 · 11小時
孫翬婷
孫翬婷 +2
管理
讚顯示更多心情 · 回覆 · 11小時
Ya-Ting Chang
Ya-Ting Chang +2
管理
讚顯示更多心情 · 回覆 · 11小時
王心怡
王心怡 +2
管理
讚顯示更多心情 · 回覆 · 11小時
Fairyoo Chen
Fairyoo Chen 2
管理
讚顯示更多心情 · 回覆 · 10小時
Fairyoo Chen
Fairyoo Chen 再+2，共4盒，謝謝🙏🏻
管理
讚顯示更多心情 · 回覆 · 10小時
Cherrie Wang
Cherrie Wang +1
管理
讚顯示更多心情 · 回覆 · 10小時
Emily Tseng
Emily Tseng +2
管理
讚顯示更多心情 · 回覆 · 10小時
湯玲
湯玲 1
管理
讚顯示更多心情 · 回覆 · 10小時
Wang Tzu Chia
Wang Tzu Chia +1併Ling Hui Wei
管理
讚顯示更多心情 · 回覆 · 10小時
Vica Chen
Vica Chen +3
管理
讚顯示更多心情 · 回覆 · 10小時
陳怡佳
陳怡佳 2
管理
讚顯示更多心情 · 回覆 · 10小時
Boow Kao
Boow Kao +1（併 Yun Chu Chen)
管理
讚顯示更多心情 · 回覆 · 10小時
Ling Hui Wei
Ling Hui Wei +1
管理
讚顯示更多心情 · 回覆 · 10小時
Jamie Chiu
Jamie Chiu +2
管理
讚顯示更多心情 · 回覆 · 10小時
陳淑鈴
陳淑鈴 2
管理
讚顯示更多心情 · 回覆 · 10小時
凱倫陳
凱倫陳 1
管理
讚顯示更多心情 · 回覆 · 9小時
廖婉妤
廖婉妤 2
管理
讚顯示更多心情 · 回覆 · 9小時
吳小金
吳小金 +1
管理
讚顯示更多心情 · 回覆 · 9小時
熊會買
熊會買你和其他 1 人都負責管理 Mommy's Secret+ (下次收款4/12) 的成員、版主、設定及貼文。 小計41，送單
管理
讚顯示更多心情 · 回覆 · 7小時
Yoshi Chi
Yoshi Chi +2
管理
讚顯示更多心情 · 回覆 · 6小時
Wing Wu
Wing Wu +2 併 Yoshi Chi
管理
讚顯示更多心情 · 回覆 · 6小時
趙靜萍
趙靜萍 1
管理
讚顯示更多心情 · 回覆 · 6小時
Serene Huang
Serene Huang +1
管理
讚顯示更多心情 · 回覆 · 5小時
Angel Hsieh
Angel Hsieh +2
管理
讚顯示更多心情 · 回覆 · 5小時
Sheena Hsieh
Sheena Hsieh +1
管理
讚顯示更多心情 · 回覆 · 3小時
Li-roung Chu
Li-roung Chu +1併Apple Chu
管理
讚顯示更多心情 · 回覆 · 3小時
Zhi Zhi Chen
Zhi Zhi Chen 1
管理
讚顯示更多心情 · 回覆 · 3小時
李婉綺
李婉綺 +2
管理
讚顯示更多心情 · 回覆 · 2小時 · 已編輯
熊會買
熊會買你和其他 1 人都負責管理 Mommy's Secret+ (下次收款4/12) 的成員、版主、設定及貼文。 小計54，送單卡位喲
管理
讚顯示更多心情 · 回覆 · 2小時
Bella Chiou
Bella Chiou +1
管理
讚顯示更多心情 · 回覆 · 2小時
倪永慧
倪永慧 +1
管理
讚顯示更多心情 · 回覆 · 1小時
Faye Cheng
Faye Cheng +1
管理
讚顯示更多心情 · 回覆 · 1小時
Chi-Chen Huang
Chi-Chen Huang +2
管理
讚顯示更多心情 · 回覆 · 47分鐘
Vivian Li
Vivian Li +4
管理
讚顯示更多心情 · 回覆 · 42分鐘
熊會買
熊會買你和其他 1 人都負責管理 Mommy's Secret+ (下次收款4/12) 的成員、版主、設定及貼文。 小計63，送單卡位喲
管理";

$commodity = "[201805]
[04/14/2018收單]
[德國Visiomax 拋棄式拭鏡螢幕消毒濕巾]
[52入盒裝：120元]

➡️紙盒包裝，空運有時會有壓到
☑️這一盒內有52入（每入都有包裝）外出好方便
☑️可以消毒手機螢幕，相機鏡頭，眼鏡，電腦螢幕，擦完亮晶晶";

$itemID = "123412341234";
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
$pieces = explode("\n", $comment);

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

for ($i = 0; $i < count($pieces); $i = $i + 4) {
    $account = $pieces[$i];
    $message = $pieces[$i + 1];
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
    echo "<td contenteditable=\"true\">" . $result["comments"]["data"][$i]["message"] . "</td>";
    echo "<td><span class=\"table-remove glyphicon glyphicon-remove\"></span>
	    		<span class=\"table-duplicate glyphicon glyphicon-duplicate\"></span></td>";
    echo "</tr>";

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