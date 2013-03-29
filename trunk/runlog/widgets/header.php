<!--[if IE]>
<style>
.tabrow li {
    margin: 0 -1px;
}
.tabrow li:before,
.tabrow li:after {
    border: none;
}
.tabrow:before {
    border-bottom: inherit;
}
</style>
<![endif]-->
<div id="header">
    <div id="header_graphics">
        <div id="title">מעקב אימונים</div>
        <div id='content' style="display:none;">
            <a id="logout" href="logout.php">התנתק</a>
            <div id="tabs">
                <ul class="tabrow">
                    <li><a id="runlog" href="runlog.php">יומן ריצה</a></li>
                    <li><a id="fedd" href="feed.php">מה עשו החבר'ה</a></li>
                    <li><a id="shoes" href="shoes.php">הנעליים שלי</a></li>
                    <li><a id="courses" href="courses.php">המסלולים שלי</a></li>
					<li><a id="statistics" href="statistics.php">סטטיסטיקה</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    var href = document.location.href;
    var page = href.substr(href.lastIndexOf('/') +1);

    $('.tabrow li').each(function()
    {
        if ($(this).children('a').attr('href') == page)
        {
            $(this).addClass('selected');
            $('#content').show();
        }
    })
});
</script>