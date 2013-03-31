<?php

require_once 'ajax_page_init.php';

$date = $_GET['date'];

try {
    $conn = getConnection();
    echo getDayEventsAsJSON($conn, $date);
    $conn = null;
}
catch(PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}

// Fetch all the events of a given date from DB as JSON
function getDayEventsAsJSON($conn, $date) {
    $sql =

"SELECT tl_events.id, tl_runners.member_name AS 'name', tl_run_types.type, run_distance, run_time, warmup_distance, cooldown_distance, COALESCE(tl_events.notes, '') AS 'notes', tl_events.run_type_id
    FROM tl_runners, tl_events, tl_run_types
    WHERE tl_runners.id = tl_events.runner_id
        AND tl_events.run_date = '" . $date . "'
        AND tl_events.run_type_id = tl_run_types.id
    ORDER BY tl_events.id DESC";

    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return returnJSONsuccess($result);
}