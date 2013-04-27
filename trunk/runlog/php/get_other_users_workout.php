<?php
/*
 * Created on Apr 4, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

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

"SELECT tl_runners.member_name as 'name', tl_run_types.type, run_distance, run_time, warmup_distance, cooldown_distance, COALESCE(tl_events.notes, '') as 'notes', tl_events.run_type_id
    FROM tl_runners, tl_events, tl_run_types
    WHERE tl_runners.id = tl_events.runner_id
        AND tl_events.run_date = '" . $date . "'
        AND tl_events.run_type_id = tl_run_types.id
    ORDER BY tl_events.id ASC";

    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return returnJSONsuccess($result);
}