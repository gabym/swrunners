<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

require_once 'constants.php';
require_once 'utils.php';
require_once 'DBLogger.php';
require_once 'member_authentication.php';

if (isset($_GET['debug']))
{
    DBLogger::setDebugEnabled($_GET['debug'] == 'true');
}

$no_auto_request_logging = array(
    'lv.php'
);

if (!in_array(basename($_SERVER['PHP_SELF']), $no_auto_request_logging))
{
    debugLogRequest(debug_backtrace());
}

$no_member_authentication = array(
    'lv.php',
    'login.php',
    'logout.php'
);

$memberAuthentication = new memberAuthentication();

if (!in_array(basename($_SERVER['PHP_SELF']), $no_member_authentication))
{
    if (!$memberAuthentication->isMemberAuthenticated())
    {
        $memberAuthentication->redirectToLoginPage();
    }
}

$memberId = $memberAuthentication->getMemberId();