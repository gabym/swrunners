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
    <link href='./css/runlog.css' rel='stylesheet' type='text/css'/>

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
                    if (doc.status.ecode == BC_ERR) {
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
                                var eventHtml =
                                    '<div class="event">' +
                                    '   <div>' +
                                    '       <div class="event_type" style="background-color:'+Calendar.getEventColor(event.run_type_id)+'; border:solid 1px '+Calendar.getEventBorderColor(event.run_type_id)+';"></div>' +
                                    '       <div class="event_title">'+Calendar.getFeedEventHtml(event)+'</div>' +
                                    '   </div>' +
                                    '   <div class="comments">' +
                                    '       <div id="event_comments_'+event.id+'"></div>' +
                                    '       <div><textarea id="event_new_comment_'+event.id+'" class="event_new_comment" placeholder="הוספת פרגון..."></textarea></div>' +
                                    '   </div>' +
                                    '</div>';
                                feed.append(eventHtml);
                                $('#event_new_comment_'+event.id).elastic().keypress(onCommentKeyPress);
                            }

                            $.ajax({
                                url: 'php/get_daily_feed_comments.php',
                                dataType: 'text',
                                data: {
                                    date: Time.hebDateToSqlDate(Time.jsDateToHebDate(day))
                                },
                                success: function(txt){
                                    var doc = Utils.parseJSON(txt);
                                    if (doc.status.ecode == BC_ERR) {
                                        alert("Fetch events failed - " + doc.status.emessage);
                                    }
                                    else {
                                        for (var i in doc.data) {
                                            var comment = doc.data[i];
                                            appendComment(comment);
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

        function appendComment(comment, animate) {
            var eventComments = $('#event_comments_'+comment.event_id);
            if (!eventComments.is(':empty')){
                eventComments.append('<div class="separator"></div>');
            }
            eventComments.parent().addClass('comments_on');
            var commentHtml =
                '<div class="comment" id="event_comment_'+comment.comment_id+'">' +
                '   <b>'+comment.commenter_name+'</b>: '+comment.comment +
                    (comment.commenter_name == gMemberName ? '<a class="remove_btn" href="#" onclick="removeComment(\''+comment.event_id+'\', \''+comment.comment_id+'\'); return false;">x</a>' : '') +
                '</div>';

            eventComments.append(commentHtml);
        }

        function onCommentKeyPress(e) {
            if (e.keyCode == 13 && !e.shiftKey){
                createComment($(e.target));
                return false;
            }
        }

        function createComment($commentTextarea) {
            var eventId = $commentTextarea.attr('id').replace('event_new_comment_', '');
            var comment = $commentTextarea.val();

            if (comment) {
                var eventComment = {
                    event_id: eventId,
                    comment: comment
                };
                $.ajax({
                    url: 'php/create_event_comment.php',
                    dataType:'text',
                    data:{
                        event_comment: JSON.stringify(eventComment)
                    },
                    success: function(txt) {
                        var doc = Utils.parseJSON(txt);
                        if (doc.status.ecode == BC_ERR) {
                            alert("Create failed: " + doc.status.emessage);
                        }
                        else {
                            appendComment({
                                event_id: eventId,
                                comment_id: doc.data.comment_id,
                                commenter_name: gMemberName,
                                comment: comment
                            },
                            true);
                            $commentTextarea.val('');
                        }
                    }
                });
            }
        }

        function removeComment(eventId, commentId){
            $.ajax({
                url: 'php/delete_event_comment.php',
                dataType:'text',
                data:{
                    event_comment_id: commentId
                },
                success: function(txt) {
                    var doc = Utils.parseJSON(txt);
                    if (doc.status.ecode == BC_ERR) {
                        alert("Delete failed: " + doc.status.emessage);
                    }
                    else {
                        var eventComments = $('#event_comments_'+eventId);
                        var comment = eventComments.find('#event_comment_'+commentId);
                        var separator = comment.prev('.separator');
                        if (separator.length == 0) {
                            var separator = comment.next('.separator');
                        }
                        comment.remove();
                        separator.remove();
                        if (eventComments.is(':empty')) {
                            eventComments.parent().removeClass('comments_on');
                        }
                    }
                }
            });
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

        #feed .event {
            margin: 25px 0;
            display: table;
        }

        #feed .event .event_type {
            width: 10px;
            display: table-cell;
        }

        #feed .event .event_title {
            padding: 0 5px;
            display: table-cell;
        }

        #feed .event .comments {
            margin-top: 4px;
            padding-top: 5px;
            padding-bottom: 5px;
            width: 353px;
            font-size: 11px;
        }

        #feed .event .comments_on {
            padding: 5px;
            background-color: #f0f0f0;
        }

        #feed .event .comments .separator {
            margin: 0 2px;
            height: 1px;
            overflow: hidden;
            background-color: #cccccc;
        }

        #feed .event .comments .comment {
            position: relative;
            padding: 5px;
            width: 315px;
        }

        #feed .event .comments .comment .remove_btn {
            position: absolute;
            top: 7px;
            right: 337px;
            height: 10px;
            padding: 2px 3px;
            line-height: 6px;
            color: #a0a0a0;
            text-decoration: none;
            display: block;
        }

        #feed .event .comments .comment .remove_btn:hover {
            background-color: #ccdae3;
            color: #45423c;
            border-radius: 3px;
        }

        #feed .event .comments .event_new_comment {
            width: 345px;
            height: 16px;
            padding: 0 3px;
            font-size: 11px;
            overflow: hidden;
            resize: none;
        }

    </style>
</head>
<body>
<?php require 'widgets/bc_header.php'; ?>
<div class="content">
    <h2 class="page_header">מה עשו החבר'ה</h2>
    <div id='feed'></div>
</div>
</body>
</html>