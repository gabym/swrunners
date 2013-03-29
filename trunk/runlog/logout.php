<?php

require_once 'php/html_page_init.php';

$memberAuthentication = new memberAuthentication();
$memberAuthentication->logout();

header("Location: login.php");
exit();