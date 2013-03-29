<?php
/*
 * Created on May 24, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require_once 'ajax_page_init.php';

$runner_id = $memberAuthentication->getMemberId();
$validationResult = validatePositiveInt($runner_id);
if (!$validationResult->isValid()) {
	die(getErrorStatusWithDummyData("Invalid runner id: " . $validationResult->getMessage()));
}

try {
	$conn = getConnection();
	echo getUserCoursesRecords($conn,$runner_id);
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}

// Fetch the events info from DB as JSON
function getUserCoursesRecords($conn,$runner_id) {
	$sql = "SELECT id, course_name, length, IFNULL(description, '') as description, active FROM  tl_courses WHERE runner_id = '" . $runner_id . "' order by active DESC, length DESC";
	$stmt = $conn->query($sql);
	$result = $stmt->fetchAll(PDO :: FETCH_ASSOC);
	return returnJSONsuccess($result);
}