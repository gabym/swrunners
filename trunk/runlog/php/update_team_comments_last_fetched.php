<?php

require_once 'ajax_page_init.php';
$runner_id = $memberAuthentication->getMemberId();

try {
    $conn = getConnection();
    $sql = 'INSERT INTO tl_team_comments_last_fetched (runner_id) VALUES (:runner_id) ON DUPLICATE KEY UPDATE timestamp=CURRENT_TIMESTAMP';
    $sth = $conn->prepare($sql);
    $ok = $sth->execute(array (
        ':runner_id' => $runner_id,
    ));

    if (!$ok) {
        die(getErrorStatusWithDummyData("Failed to update team comments last visited."));
    }
    else {
        echo returnJSONsuccess("");
    }
    $conn = null;
}
catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}