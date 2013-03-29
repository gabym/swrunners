<?php
$sent_cookies = $_COOKIE;
session_start();
header('Content-Type: application/json');

$session_value = isset($_SESSION['value']) ? $_SESSION['value'] : '';
echo json_encode(array(
	'session_value' => $session_value,
	'sent_cookies' => json_encode($sent_cookies),
	));