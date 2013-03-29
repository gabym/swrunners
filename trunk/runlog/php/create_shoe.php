<?php

/*
 * Created on May 18, 2012
 *
 * Create a new user shoe
 * 
 * http://www.bc-running.com/runlog/php/create_shoe.php?shoeStr={"name" : "Puma3","type" : 2, "runner_id" : 12345, "start_using_date" : 123134434 }
 * 
 * TODO: create a new table with the shoe types + update the tl_shoes table  - add the shoe type
 * 
 * 
 */
 
require_once 'ajax_page_init.php';
require_once 'shoe_common.php';

$runner_id = $memberAuthentication->getMemberId();
$shoe = getShoe();

try {
	$conn = getConnection();
	$sql = 'INSERT INTO tl_shoes (shoe_name,type,start_using_date,active,runner_id) VALUES (:shoe_name,:type,:start_using_date,:active,:runner_id)';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':shoe_name' => $shoe->shoe_name,
		':type' => $shoe->type,
		':start_using_date' => $shoe->start_using_date,
		':active' => 1,
		':runner_id' => $runner_id
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to Create a shoe."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}


