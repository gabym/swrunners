<?php

/*
 * Created on May 18, 2012
 *
 * Create a new user course
 * 
 * http://www.bc-running.com/runlog/php/create_course.php?shoeStr={"name" : "north mountain","description" : "very nice..", "runner_id" : 12345, "length" : 12.4 }
 *  
 * 
 */
 
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


