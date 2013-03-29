<?php
 
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


