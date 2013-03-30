<?php

require_once 'ajax_page_init.php';

$eventCommentId = $_GET['event_comment_id'];
$validationResult = validatePositiveInt($eventCommentId);
if(!$validationResult->isValid()) {
    die(getErrorStatus("Invalid event comment id: " .$validationResult->getMessage()));
}

$runnerId = $memberAuthentication->getMemberId();

try {
    $conn = getConnection();
    $count = $conn->exec("DELETE FROM tl_comments where id = '" . $eventCommentId . "' AND runner_id = '" . $runnerId . "'");
    if($count != 1) {
        // we have a problem here - only 1 record should be deleted
        echo getErrorStatus("Expecting 1 record to be deleted. However . $count . were deleted.");
    }
    else {
        echo returnJSONsuccess("");
    }

    $conn = null;
}
catch(PDOException $e) {
    die(getErrorStatus($e->getMessage()));
}