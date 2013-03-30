<?php

require_once 'ajax_page_init.php';

function getEventComment() {
    if (!isset ($_GET['event_comment'])) {
        die(getErrorStatusWithDummyData("Mandatory input - event_comment was not found."));
    }

    $eventComment = json_decode($_GET['event_comment']);

    $json_decode_error = json_last_error();
    if ($json_decode_error != JSON_ERROR_NONE) {
        die(getErrorStatusWithDummyData("Failed to decode JSON - " . getJSONDEcodeErrDesc($json_decode_error)));
    }

    return $eventComment;
}

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