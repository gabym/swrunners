<?php

/*
 * Created on May 19, 2012
 *
 * Deactivate a shoe
 */

require_once 'ajax_page_init.php';

//
// TODO: make sure the shoe that we try to update belongs to the user
//

if (!isset ($_GET['shoe_id'])) {
	die(getErrorStatusWithDummyData("Missing mandatory parameter: shoe_id"));
}
if (!isset ($_GET['active'])) {
	die(getErrorStatusWithDummyData("Missing mandatory parameter: active"));
}

$shoeId = $_GET['shoe_id'];
$active = $_GET['active'];

try {
	$conn = getConnection();
	$sql = 'UPDATE tl_shoes SET active=:active WHERE id=:id';
	$sth = $conn->prepare($sql);
	$ok = $sth->execute(array (
		':active' => $active,
		':id' => $shoeId
	));
	if (!$ok) {
		die(getErrorStatusWithDummyData("Failed to update shoe activation."));
	} else {
		echo returnJSONsuccess("");
	}
	$conn = null;
} catch (PDOException $e) {
	die(getErrorStatusWithDummyData($e->getMessage()));
}