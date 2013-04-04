<?php	

// -----------------------------------------------------------------------------------------
// This script is responsible for deleting an event.
// It expects 2 input parameters:
// 1) The member ID - in order to see if the caller is logged in
// 2) The event ID - will used in the deletion operation
// The output of the script is a JSON with 2 fields:
// 1) ecode [ERR || OK]
// 2) emessage - free text
// NOTE: since the client is expecting a JSON result - we should always return a valid JSON
// -----------------------------------------------------------------------------------------
require_once 'ajax_page_init.php';

$member_id = $_GET['member_id'];
$validationResult = validatePositiveInt($member_id);
// we have invalid member_id - lets quit
if(!$validationResult->isValid()) {
    die(getErrorStatus("Invalid member id: " .$validationResult->getMessage()));
}

$isUserLoggedIn = scriptCallerIsLoggedIn($member_id);

if(!$isUserLoggedIn) {
    //TODO - move the user to the login page
}

$event_id = $_GET['event_id'];
$validationResult = validatePositiveInt($event_id);
if(!$validationResult->isValid()) {
    die(getErrorStatus("Invalid event id: " .$validationResult->getMessage()));
}


// OK - now we have a valid input - lets try to remove the event from DB
try {
    $conn = getConnection();
    $count = $conn->exec("DELETE FROM tl_events where id = '" . $event_id . "'");
    if($count != 1) {
        // we have a problem here - only 1 record should be deleted
        echo getErrorStatus("Expecting 1 record to be deleted. However . $count . were deleted.");
    } else {
        echo returnDeleteEvenJsonSuccess();
    }

    $conn->exec("DELETE FROM tl_comments where event_id = '" . $event_id . "'");

    $conn = null;
}
catch(PDOException $e) {
    die(getErrorStatus($e->getMessage()));
}

function returnDeleteEvenJsonSuccess()
{
    return returnJSONsuccess("");
}

//TODO: check if the user with $member_id is logged in
function scriptCallerIsLoggedIn($member_id) {
    return true;
}