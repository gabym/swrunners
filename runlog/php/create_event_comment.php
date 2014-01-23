<?php

require_once 'ajax_page_init.php';
require_once 'comment_common.php';

$runner_id = $memberAuthentication->getMemberId();
$eventComment = getEventComment();

try {
    $conn = getConnection();
    $sql = 'INSERT INTO tl_comments (event_id,runner_id,comment) VALUES (:event_id,:runner_id,:comment)';
    $sth = $conn->prepare($sql);
    $ok = $sth->execute(array (
        ':event_id' => $eventComment-> {"event_id" },
        ':runner_id' => $runner_id,
        ':comment' => $eventComment-> {"comment" },
    ));

    if (!$ok) {
        die(getErrorStatusWithDummyData("Failed to Create an event comment."));
    }
    else {
        $eventCommentId = $conn->lastInsertId();
        echo returnJSONsuccess(array(
            'comment_id' => $eventCommentId,
        ));
    }
    $conn = null;
}
catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}