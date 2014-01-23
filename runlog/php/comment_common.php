<?php

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