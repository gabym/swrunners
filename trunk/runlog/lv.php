<?php
require_once 'php/html_page_init.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
    /* tables */
    table.tablesorter {
        font-family:arial;
        background-color: #CDCDCD;
        margin:10px 0pt 15px;
        font-size: 8pt;
        width: 100%;
        text-align: left;
        table-layout: fixed;
    }
    table.tablesorter thead tr th, table.tablesorter tfoot tr th {
        background-color: #e6EEEE;
        border: 1px solid #FFF;
        font-size: 8pt;
        padding: 4px;
    }
    table.tablesorter thead tr .header {
        background-image: url(http://tablesorter.com/themes/blue/bg.gif);
        background-repeat: no-repeat;
        background-position: center right;
        cursor: pointer;
    }
    table.tablesorter tbody td {
        color: #3D3D3D;
        padding: 4px;
        background-color: #FFF;
        vertical-align: top;
    }
    table.tablesorter tbody tr.odd td {
        background-color:#F0F0F6;
    }
    table.tablesorter thead tr .headerSortUp {
        background-image: url(http://tablesorter.com/themes/blue/asc.gif);
    }
    table.tablesorter thead tr .headerSortDown {
        background-image: url(http://tablesorter.com/themes/blue/desc.gif);
    }
    table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
    background-color: #8dbdd8;
    }

</style>

<script src="./js/jquery.min.js"></script>
<script src="./js/json2.js" type="text/javascript"></script>
<script src="./js/json_sans_eval.js"></script>
<script src="./js/constants.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
<script src="./js/functions.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
<script src="./js/table_sorter.js" type="text/javascript"></script>

<script type='text/javascript'>

function populateLogRecords() {
    $.ajax({
        url : 'php/lv.php',
        dataType : 'json',
        data : {
            // empty - for now
        },
        success : function(json) {
            var html = "";
            $.each(json, function(index, item) {
                html += "<tr>";
                html += "<td>" + index + "</td>";
                html += "<td>" + item.ts + "</td>";
                html += "<td>" + item.level + "</td>";
                html += "<td>" + item.file + "<br>" + item.qs + "</td>";
                html += "<td>" + item.msg + "</td>";
                html += "<td>" + item.function + "</td>";
                html += "<td>" + (item.line == 0 ? '' : item.line) + "</td>";
                html += "<td>" + item.agent + "</td>";
                html += "<td>" + item.ip + "</td>";
                html += "</tr>";
            });
            var tbody = $('#records');
            tbody.html(html);
            $("#log_records_table").trigger("update");
            $("#log_records_table").trigger("appendCache");
        }
    });
}

function truncateLog()
{
    if (confirm('Are you sure you want do delete the entire log?'))
    {
        $.ajax({
            url : 'php/lv_truncate.php',
            dataType : 'text',
            success : function(txt) {
                var doc = Utils.parseJSON(txt);
                if (doc.status.ecode == STATUS_ERR) {
                    alert("Failed to truncate log - " + doc.status.emessage);
                } else {
                    document.location.reload();
                }
            }
        });
    }
}

$(document).ready(function() {
    $("#log_records_table").tablesorter();
    populateLogRecords();

});
</script>
</head>
<body>
<h2>tlog Log Viewer</h2>
<input type="button" onclick="truncateLog()" value="Purge log">
<div id="data">
<table id="log_records_table" class="tablesorter">

<thead>
<th width="20">#</th><th width="60">Time</th><th width="40">Level</th><th width="30%">File</th><th width="30%">Message</th><th width="10%">Function</th><th width="40">Line</th><th width="10%">Agent</th><th width="70">IP</th>
</thead>

<tbody id="records">
</tbody>

</table>
</div>
</body>
</html>