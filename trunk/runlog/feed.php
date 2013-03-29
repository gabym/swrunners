<?php
require_once 'php/html_page_init.php';
?>
<!DOCTYPE HTML>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<html dir="RTL">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>הרוח השנייה - מה עשו החבר'ה</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.1/build/cssreset/cssreset-min.css">
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href='./css/runlog.css?v=<?php echo CSS_VERSION;?>' rel='stylesheet' type='text/css'/>

<script src="./js/jquery.min.js" type="text/javascript"></script>
<script src="./js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./js/jquery.elastic.js" type="text/javascript"></script>
<script src="./js/jquery.maskedinput.js" type="text/javascript"></script>
<script src="./js/json2.js" type="text/javascript"></script>
<script src="./js/constants.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>
<script src="./js/json_sans_eval.js"></script>
<script src="./js/functions.js?v=<?php echo JS_VERSION;?>" type="text/javascript"></script>

<script type="text/javascript">

    var gMemberName = "<?php echo $memberAuthentication->getMemberName(); ?>";

    if (jQuery.browser.msie && jQuery.browser.version.indexOf("8.") == 0) {
        if (typeof JSON !== 'undefined') {
            JSON.parse = null;
        }
    }

    function getDayEvents()
    {
        if (feedQueueLock) {
            return;
        }
        feedQueueLock = true;

        var day = feedQueue.shift();
        $.ajax({
            url: 'php/get_daily_feed.php',
            dataType: 'text',
            data: {
                date: Time.hebDateToSqlDate(Time.jsDateToHebDate(day))
            },
            success: function (txt) {
                var doc = Utils.parseJSON(txt);
                if (doc.status.ecode == STATUS_ERR) {
                    alert("Fetch events failed - " + doc.status.emessage);
                }
                else {
                    feed.append('<div class="day_header">'+Time.jsDateToHebString(day)+'</div>');
                    if (doc.data.length == 0) {
                        feed.append('<div class="event">אין נתונים ליום זה</div>');
                    }
                    else {
                        for (var i in doc.data) {
                            var event = doc.data[i];
                            Comments.appendEvent(feed, event);
                        }

                        $.ajax({
                            url: 'php/get_daily_feed_comments.php',
                            dataType: 'text',
                            data: {
                                date: Time.hebDateToSqlDate(Time.jsDateToHebDate(day))
                            },
                            success: function(txt){
                                var doc = Utils.parseJSON(txt);
                                if (doc.status.ecode == STATUS_ERR) {
                                    alert("Fetch events failed - " + doc.status.emessage);
                                }
                                else {
                                    for (var i in doc.data) {
                                        var comment = doc.data[i];
                                        Comments.appendComment(comment);
                                    }
                                }
                            }
                        });
                    }
                }

                feedQueueLock = false;
                if (feedQueue.length > 0) {
                    getDayEvents();
                }
                else {
                    onFeedScroll();
                }
            }
        });
    }

    function getNextDayEvents()
    {
        date.setDate(date.getDate() -1);
        feedQueue.push(new Date(date));
        getDayEvents();
    }

    function onFeedScroll()
    {
        if (feedQueueLock) {
            return;
        }

        if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            getNextDayEvents();
        }
    }

    function init()
    {
        $(window).scroll(onFeedScroll);
        feedQueue = [];
        feedQueueLock = false;
        feed = $('#feed');
        date = new Date();
        feedQueue.push(new Date(date));
        getDayEvents();
    }

    $(document).ready(function () {
        init();
    });
</script>

<style>
    .content {
        width: 920px;
        margin: 0 auto;
    }

    .page_header {
        font-size: 18px;
        margin-top: 20px;
    }

    #feed {
        margin: 20px 0;
    }

    #feed .day_header {
        margin: 25px 0;
        padding: 4px 5px;
        font-size: 14px;
        background-color: #deecf5;
        border-radius: 2px;
    }
</style>
</head>
<body>
<?php require 'widgets/header.php'; ?>
<div class="content">
    <h2 class="page_header">מה עשו החבר'ה</h2>
    <div id='feed'></div>
</div>
</body>
</html>