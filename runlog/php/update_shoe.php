<?php
/*
 * Created on May 19, 2012
 *
 * Update existing user shoe - fields that can be changed: name,type,start_using_date
 */
require_once 'ajax_page_init.php';
require_once 'shoe_common.php';

//
// TODO: make sure the shoe that we try to update belongs to the user
//

$runner_id = $memberAuthentication->getMemberId();
$shoe = getShoe();

try {
	$conn = getConnection();
	$sql = 'UPDATE tl_shoes SET shoe_name=:shoe_name,type=:type,start_using_date=:start_using_date WHERE id=:id';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':shoe_name' => $shoe->shoe_name,
		':type' => $shoe->type,
		':start_using_date' => $shoe->start_using_date,
		':id' => $shoe->shoe_id
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to update shoe."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}
