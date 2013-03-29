<?php

require_once 'ajax_page_init.php';
require_once 'course_common.php';

//
// TODO: make sure the course that we try to create belongs to the user
//

$runner_id = $memberAuthentication->getMemberId();
$course = getCourse();

try {
	$conn = getConnection();
	$sql = 'INSERT INTO tl_courses (course_name,length,description,active,runner_id) VALUES (:course_name,:length,:description,:active,:runner_id)';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':course_name' => $course-> {"course_name" },
		':length' => $course-> {"length" },
		':description' => $course-> {"description" },
		':active' => 1,
		':runner_id' => $runner_id
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to Create a course."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}


