<?php

/*
 * Created on May 19, 2012
 *
 * To change the template for this generated file go to
 */

require_once 'constants.php';
require_once 'utils.php';
require_once 'DBLogger.php';

function getCourse() {
	if (!isset ($_GET['courseStr'])) {
		die(getErrorStatusWithDummyData("Mandatory input - courseStr was not found."));
	}
	$courseStr = $_GET['courseStr'];
	$course = json_decode($courseStr);
	$json_decode_error = json_last_error();
	if ($json_decode_error != JSON_ERROR_NONE) {
		die(getErrorStatusWithDummyData("Failed to decode JSON - " . getJSONDEcodeErrDesc($json_decode_error)));
	}
	return $course;

}

