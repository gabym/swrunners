<?php
require_once 'php/html_page_init.php';
?>
<!DOCTYPE HTML>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<html dir="RTL">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>הרוח השניה - יומן ריצה</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.1/build/cssreset/cssreset-min.css">
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet"
          type="text/css"/>
    <link href='./css/fullcalendar.css?v=<?php echo CSS_VERSION;?>' rel='stylesheet' type='text/css'/>
    <link href='./css/jquery.qtip.min.css' rel='stylesheet' type='text/css'/>
    <link href='./css/runlog.css' rel='stylesheet' type='text/css'/>

    <script src="./js/jquery.min.js" type="text/javascript"></script>
    <script src="./js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="./js/jquery.qtip.js" type="text/javascript"></script>
    <script src="./js/jquery.elastic.js" type="text/javascript"></script>
    <script src="./js/jquery.maskedinput.js" type="text/javascript"></script>
    <script src="./js/json2.js" type="text/javascript"></script>
    <script src="./js/fullcalendar.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
    <script src="./js/constants.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
    <script src="./js/json_sans_eval.js"></script>
    <script src="./js/jquery.ui.datepicker-he.js" type="text/javascript"></script>
    <script src="./js/functions.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>

    <script type="text/javascript">
        var memberId = <?php echo $memberId; ?>;
        if (jQuery.browser.msie && jQuery.browser.version.indexOf("8.") == 0) {
            if (typeof JSON !== 'undefined') {
                JSON.parse = null;
            }
        }

        $(document).ready(function () {
            Functions.initUsersAutoComplete();
            Functions.populateUsersSelect(Calendar.init);
        });
    </script>
    <style>
        #users {
            position: relative;
            top: 24px;
            display: none;
        }

        #calendar {
            width: 920px;
            margin: 0 auto;
        }

        #calendar .fc-header-title h2 {
            margin-top: 4px;
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php require 'widgets/bc_header.php'; ?>
<div style="display:none;">
    <label for="users1">Users: </label>
    <input id="users1"/>
</div>

<div style="text-align:center;">
    <select id="users" style="" onchange="Functions.userChanged();"></select>
</div>

<div id='calendar'></div>
<div id="delete_dialog" title="מחיקת אימון">האם למחוק את האימון ?</div>

<!-- ~~~~~~~~~~~~~~~~~~~ -->
<!-- Create/Update Event -->
<!-- ~~~~~~~~~~~~~~~~~~~ -->

<div id="create_update_event_dialog" title="הוספת/עדכון אימון">
    <form>
        <!-- Event types -->
        <div style="margin-top:15px;">
            <label for="run_types" class="label">סוג אימון:</label>
            <select id="run_types"></select>
        </div>

        <!-- Shoes -->
        <div id="user_shoes" style="margin-top:5px;">
            <label id="shoesSelectLabel" class="label" for="shoesSelect">נעל:</label>
            <span id='inactive_shoe'></span>
            <select id="shoesSelect"></select>
        </div>

        <!-- Courses -->
        <div id="user_courses" style="margin-top:5px;">
            <label id="courseSelectLabel" class="label" for="courseSelect">מסלול:</label>
            <span id='inactive_course'></span>
            <select id="courseSelect"></select>
        </div>

        <!-- Duration, Distance, Pace -->
        <div id="duration_distance_pace">
            <table cellspacing="0" cellpadding="0" border="0" id="duration_distance_pace_table">
                <tr>
                    <th class="first"></th>
                    <th>מרחק</th>
                    <th>זמן</th>
                    <th>קצב</th>
                </tr>
                <tr>
                    <td class="first">חימום:</td>
                    <td><input type="text" id="warmup_distance"></td>
                    <td><input type="text" id="warmup_time"></td>
                    <td>
                        <div id="warmup_pace" class="pace"></div>
                    </td>
                </tr>
                <tr>
                    <td class="first">תרגיל:</td>
                    <td><input type="text" id="run_distance"></td>
                    <td><input type="text" id="run_time"></td>
                    <td>
                        <div id="run_pace" class="pace"></div>
                    </td>
                </tr>
                <tr>
                    <td class="first">שחרור:</td>
                    <td><input type="text" id="cooldown_distance"></td>
                    <td><input type="text" id="cooldown_time"></td>
                    <td>
                        <div id="cooldown_pace" class="pace"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        <div style="margin-top:20px;">
            <div class="label" style="float:right;">פרטים:</div>
            <div id="notesContainer" style="float:right;"></div>
        </div>
    </form>
</div>
</body>
</html>