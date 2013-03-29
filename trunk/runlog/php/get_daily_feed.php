<?php

require_once 'bc_ajax_page_init.php';

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
    $sql = "select tl_events.id, tl_runners.member_name as 'name', tl_run_types.type, run_distance, run_time, warmup_distance, cooldown_distance, COALESCE(tl_events.notes, '') as 'notes', tl_events.run_type_id from tl_runners, tl_events, tl_run_types where tl_runners.id = tl_events.runner_id and tl_events.run_date = '" . $date . "' and tl_events.run_type_id = tl_run_types.id order by tl_events.id desc";
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return returnJSONsuccess($result);
}