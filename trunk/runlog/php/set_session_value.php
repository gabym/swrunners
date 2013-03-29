<?php
$sent_cookies = $_COOKIE;
session_start();
$_SESSION['value'] = $_GET['value'];
?>
<html>
<body>
	<div>Session value is set to: <?php echo $_SESSION['value']; ?></div>
	<br>
	<div>Cookies sent to server:</div>
	<pre><?php echo json_encode($sent_cookies);?></pre>
</body>
</html>