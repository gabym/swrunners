<?php
/*
 * Created on May 7, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 * Truncates the tl_logger table
 *
 */
require_once 'ajax_page_init.php';

try {
	$conn = getConnection();
	$sql = "TRUNCATE tl_logger";
	$stmt = $conn->query($sql);

    $response = array (
   		"status" => (array (
   			"ecode" => STATUS_OK,
   			"emessage" => "Operation successful."
   		))
   	);
   	echo json_encode($response);

	$conn = null;
}
catch(PDOException $e) {
	die(getErrorStatus($e->getMessage()));
}