<?php


/*
 * Get a data as input, find the date six days before and fetch the totals: distance
 * Created on Apr 8, 2012
 *
 */
require_once 'ajax_page_init.php';

$requestStr = $_GET['request'];
if (!isset ($requestStr)) {
	die(getErrorStatusWithDummyData("FATAL: Expecting mandatory parama : request"));
}

$request = json_decode($requestStr);

$validationResult = validatePositiveInt($request-> {
	'member_id' });
// we have invalid member_id - lets quit
if (!$validationResult->isValid()) {
	die(getErrorStatusWithDummyData("Invalid member id: " . $validationResult->getMessage()));
}

$weekends = $request-> {
	'weekends' };
foreach ($weekends as $weekend) {
    $weekend = strtotime($weekend);
	$validationResult = validatePositiveInt($weekend);
	if (!$validationResult->isValid()) {
		die(getErrorStatusWithDummyData("Invalid date: " . $validationResult->getMessage()));
	}
}

// Assuming we have valid request we can start and calculte the total weekly distance total
$data = array();

try {
	$conn = getConnection();
	foreach ($weekends as $weekend) {
        $weekend = strtotime($weekend);
		$the_date = date('Y-m-d', $weekend);
		$six_days_before = strtotime('-6 day', $weekend); // move six days back
		$six_days_before_date = date('Y-m-d', $six_days_before);
		$sql = "SELECT (SUM(run_distance) + SUM(warmup_distance) + SUM(cooldown_distance)) as weekly FROM  tl_events WHERE (runner_id = :runner_id AND run_date >= :week_start AND run_date <= :week_end)";
		$sth = $conn->prepare($sql, array (
			PDO :: ATTR_CURSOR => PDO :: CURSOR_FWDONLY
		));
		$ok = $sth->execute(array (
			':runner_id' => $request->{'member_id'},
			':week_start' => $six_days_before_date,
			':week_end' => $the_date
		));
		if (!$ok) {
			die(getErrorStatusWithDummyData("Failed to execute prepared statment"));
		} else {
			$result = $sth->fetchAll(PDO :: FETCH_COLUMN,0);
			$data[] = $result[0];
		}
	}
	echo returnJSONsuccess($data);
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}