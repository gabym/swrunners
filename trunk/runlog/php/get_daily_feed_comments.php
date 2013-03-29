<?php

require_once 'bc_ajax_page_init.php';

// day to get comments for
$date = $_GET['date'];

try {
    $conn = getConnection();
    echo getDayEventCommentsAsJSON($conn, $date);
    $conn = null;
}
catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}

// Fetch all event comments of a given date from DB as JSON
function getDayEventCommentsAsJSON($conn, $date) {
    $sql =

"SELECT events.id AS event_id, comments.id AS comment_id, comments.runner_id, runners.member_name as 'commenter_name', COALESCE(comments.comment, '') AS comment
 FROM tl_events events, tl_event_comments comments, tl_runners runners
 WHERE events.run_date = '".$date."'
    AND events.id = comments.event_id
    AND comments.runner_id = runners.id
 ORDER BY events.id DESC, comments.id ASC";

    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return returnJSONsuccess($result);
}