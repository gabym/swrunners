<?php
/*
 * Created on May 5, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once 'ajax_page_init.php';
require_once 'event_common.php';

if(!isset( $_GET['member_id'])) {
	die(getErrorStatusWithDummyData("member id is not set"));
	
}
 
$member_id = $_GET['member_id'];
$validationResult = validatePositiveInt($member_id);
if (!$validationResult->isValid()) {
	die(getErrorStatusWithDummyData("Invalid member id: " . $validationResult->getMessage()));
}
 
$conn = getConnection();
try {
	$shoes =  getUserShoes($conn,$member_id);
	$courses = getUserCourses($conn,$member_id);	
	echo returnEventDataJSONsuccess($shoes,$courses);	
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}

function returnEventDataJSONsuccess($shoes,$courses) {
    $result = array (
			"shoes" => $shoes,
			"courses" => $courses
	    );

    return returnJSONsuccess($result);
}