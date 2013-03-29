<?php
header('Content-Type: application/json');

error_reporting(E_ALL ^ E_NOTICE);
session_start();

require_once 'constants.php';
require_once 'utils.php';
require_once 'DBLogger.php';
require_once 'member_authentication.php';

$memberAuthentication = new memberAuthentication();

if (!$memberAuthentication->isMemberAuthenticated())
{
    die(getErrorStatusWithDummyData('access denied'));
}