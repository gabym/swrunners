<?php

require_once 'ajax_page_init.php';
$runner_id = $memberAuthentication->getMemberId();

try {
    $conn = getConnection();
    $sql = "SELECT IFNULL(MIN(timestamp), '0000-00-00 00:00:00') AS last_fetched FROM tl_team_comments_last_fetched WHERE runner_id = '" . $runner_id . "'";
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;

    echo returnJSONsuccess($result);
}
catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}