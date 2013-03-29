<?php

/*
 * Created on May 19, 2012
 *
 * Deactivate a shoe
 */

require_once 'ajax_page_init.php';

//
// TODO: make sure the course that we try to update belongs to the user
//

if (!isset ($_GET['course_id'])) {
	die(getErrorStatusWithDummyData("Missing mandatory parameter: course_id"));
}
if (!isset ($_GET['active'])) {
	die(getErrorStatusWithDummyData("Missing mandatory parameter: active"));
}

$courseId = $_GET['course_id'];
$active = $_GET['active'];

try {
	$conn = getConnection();
	$sql = 'UPDATE tl_courses SET active=:active WHERE id=:id';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':active' => $active,
		':id' => $courseId
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to update course activation."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}