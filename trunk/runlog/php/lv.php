<?php


/*
 * Created on Apr 21, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once 'ajax_page_init.php';

try {
	$conn = getConnection();
	echo getLogRecords($conn);
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}

// Fetch the events info from DB as JSON
function getLogRecords($conn) {
	$sql = "SELECT * FROM " . tl_logger;
	$stmt = $conn->query($sql);
	$result = $stmt->fetchAll(PDO :: FETCH_ASSOC);
	return json_encode($result);
}