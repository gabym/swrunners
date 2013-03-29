<?php
/*
 * Created on May 19, 2012
 *
 * Update existing user course - fields that can be changed: name,length,description
 */
require_once 'ajax_page_init.php';
require_once 'course_common.php';

//
// TODO: make sure the course that we try to update belongs to the user
//

$runner_id = $memberAuthentication->getMemberId();
$course = getCourse();

try {
	$conn = getConnection();
	$sql = 'UPDATE tl_courses SET course_name=:course_name,description=:description,length=:length WHERE id=:id';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':course_name' => $course->course_name,
		':description' => $course->description,
		':length' => $course->length,
		':id' => $course->course_id
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to update course."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}
