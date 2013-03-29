<?php
require_once 'php/bc_html_page_init.php';
?>
<!DOCTYPE HTML>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<html dir="RTL">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>הרוח השניה - הנעליים שלי</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.1/build/cssreset/cssreset-min.css">
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href='./css/runlog.css' rel='stylesheet' type='text/css'/>

<script src="./js/jquery.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./js/jquery.maskedinput.js" type="text/javascript"></script>
<script src="./js/json2.js" type="text/javascript"></script>
<script src="./js/constants.js" type="text/javascript"></script>
<script src="./js/json_sans_eval.js"></script>
<script src="./js/functions.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
<script src="./js/jquery.ui.datepicker-he.js" type="text/javascript"></script>

<script type='text/javascript'>

var shoes = new Array();
function populateUserShoesRecords() {
    $.ajax({
        url : 'php/user_shoes.php',
        dataType : 'text',
        success : function(txt) {
            var doc = Utils.parseJSON(txt);
        	if (doc.status.ecode == BC_ERR) {
				alert("Failed to get shoes data - " + doc.status.emessage);
        	} else {
	            var html = "";
                shoes = new Array();
	            $.each(doc.data, function(index, item) {
                    item.start_using_date = Time.jsDateToHebDate(Time.sqlDateToJsDate(item.start_using_date));
                    shoes[item.id] = item;

	                html += '<tr id="'+item.id+'" class="'+(item.active == 1 ? 'active' : 'inactive')+'">';
                    html += '<td class="active_col"><input type="checkbox" ' + (item.active == 1 ? 'checked' : '') + '></td>';
	                html += '<td>' + item.name + '</td>';
                    html += '<td>' + item.type_name + '</td>';
	                html += '<td>' + (item.start_using_date) + '</td>';
	                html += '<td>' + item.distance + '</td>';
	                html += '</tr>';
	            });
	            $('#records').html(html);
                $('#user_shoes_table tr.active').each(function(){
                    $(this).mouseenter(function(){$(this).addClass('hover');});
                    $(this).mouseleave(function(){$(this).removeClass('hover');});
                    $(this).click(function(){openUpdateShoeDialog($(this).attr('id'))});
                });
                $('#user_shoes_table td.active_col input[type="checkbox"]').each(function(){
                    $(this).click(function(event){
                        var itemId = $(this).closest('tr').attr('id');
                        var isChecked = $(this).attr('checked') == 'checked';
                        updateShoeIsActive(itemId, isChecked);
                        event.stopPropagation();
                    });
                });
        	}
         }
    });
}

function openCreateShoeDialog() {
    openShoeDialog('', '', '1', '');
}

function openUpdateShoeDialog(shoeId)
{
    var shoe = shoes[shoeId];
    openShoeDialog(shoe.name, shoe.start_using_date, shoe.type_id, shoe.id);
}

function openShoeDialog(shoeName, shoeStartUsingDate, shoeType, shoeId)
{
    if (isNaN(parseInt(shoeId)))
    {
        var dialogTitle = 'הוסף נעל';
    }
    else
    {
        var dialogTitle = 'ערוך פרטי נעל';
    }
    $('#shoe_name').val(shoeName);
    $('#datepicker').val(shoeStartUsingDate);
    $('#type').val(shoeType);
    $('#shoe_id').val(shoeId);
    $('#create_shoe_dialog').css('background-color', '#feffe5');
    $("#create_shoe_dialog").dialog({ title: dialogTitle });
    $("#create_shoe_dialog").dialog("open");
}

function updateShoeIsActive(shoeId, isActive)
{
    $.ajax({
        url : 'php/update_shoe_activation.php',
        dataType : 'text',
        data : {
            shoe_id : shoeId,
            active : isActive ? 1 : 0
        },
        success : function(txt) {
            var doc = Utils.parseJSON(txt);
            if (doc.status.ecode == BC_ERR) {
                alert(doc.status.emessage);
            } else {
                populateUserShoesRecords();
            }
        }
    });
}

function getShoe() {
	var shoe = {
	    shoe_name : $('#shoe_name').val(),
        start_using_date : Time.hebDateToSqlDate($('#datepicker').val()),
        type : $('#type').val(),
        shoe_id : $('#shoe_id').val()
    };

	return shoe;
}

$(document).ready(function() {

    $('#create_shoe_dialog').dialog(
       {
        height : 300,
        width : 380,
        show : {
            effect : 'drop',
            direction : 'rtl'
        },
        autoOpen: false,
        buttons : {
            "אשר" : function() {
                var shoe = getShoe();
                if (shoe != null)
                {
                    var shoe_str = JSON.stringify(shoe);
                    if (shoe.shoe_id == '')
                    {
                        var url = 'php/create_shoe.php';
                    }
                    else
                    {
                        var url = 'php/update_shoe.php';
                    }
                    $.ajax({
                        url : url,
                        dataType : 'text',
                        data : {
                            shoeStr : shoe_str
                        },
                        success : function(txt) {
                            var doc = Utils.parseJSON(txt);
                            if (doc.status.ecode == BC_ERR) {
                                alert(doc.status.emessage);
                            }
                        }
                    });
                    $(this).dialog("close");
                    populateUserShoesRecords();
                }
            },
            "סגור" : function() {
                $(this).dialog("close");
            }
        }
       });

    $(function() {
        $("#datepicker").datepicker({ changeYear: true, changeMonth: true });
        $.datepicker.regional['he'];
    });

    populateUserShoesRecords();
});

</script>

<style>
#create_shoe_dialog {
    padding-left: 25px;
    padding-right: 25px;
}
#create_shoe_dialog .label {
    width: 85px;
    font-size: 11px;
    font-weight: bold;
    display: inline-block;
}
#create_shoe_dialog input {
    width: 150px;
}
#create_shoe_dialog select {
    width: 158px;
}
</style>
</head>

<body>
    <?php require 'widgets/bc_header.php'; ?>

    <div class="RunLog" class="ui-widget" style="width:920px; margin-left:auto; margin-right:auto;">
        <div id="create_shoe_dialog">
            <form>
                <div style="margin-top:15px;">
                    <span class="label">שם הנעל:</span>
                    <input id="shoe_name">
                </div>

                <div style="margin-top:5px;">
                    <span class="label">תחילת שימוש:</span>
                    <input type="text" name="start_using_date" id="datepicker">
                </div>

                <div style="margin-top:5px;">
                    <label class="label">סוג נעל:</label>
                    <select id="type">
                      <option value="1">כביש</option>
                      <option value="2">שטח</option>
                      <option value="3">אימון קלה</option>
                      <option value="4">תחרות</option>
                    </select>
                </div>

                <input type="hidden" id="shoe_id" name="shoe_id">
            </form>
        </div>

        <h2 class="page_header">הנעליים שלי</h2>
        <div id="data" style="margin-top:20px;">
            <table id="user_shoes_table" class="tablesorter">
                <thead>
                    <th>בשימוש</th>
                    <th>שם</th>
                    <th>סוג</th>
                    <th>תחילת שימוש</th>
                    <th>סך קילומטרים</th>
                </thead>
                <tbody id="records"></tbody>
            </table>
        </div>

        <input type="button" onclick="openCreateShoeDialog()" value="הוסף נעל" style="margin-top:20px;">
    </div>
</body>
</html>