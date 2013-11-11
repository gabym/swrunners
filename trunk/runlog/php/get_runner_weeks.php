<?php
require_once 'ajax_page_init.php';

$conn = getConnection();

$runner_id = $_GET['runner_id'];
$validationResult = validatePositiveInt($runner_id);
if (!$validationResult->isValid()) {
    die(getErrorStatusWithDummyData("Invalid runner id: " . $validationResult->getMessage()));
}

try {
    $sql = "SELECT tl_runners.member_name FROM tl_runners WHERE tl_runners.id = '" . $runner_id . "'";
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO :: FETCH_ASSOC);
} catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}

$data [] = Array("Name", $result[0]['member_name'], "Weekly running sessions");


$sql = "SELECT (SUM(run_distance) + SUM(warmup_distance) + SUM(cooldown_distance)) as weekly, count(*) as a FROM  tl_events WHERE (runner_id = :runner_id AND run_date >= :week_start AND run_date <= :week_end AND run_type_id != '9' AND run_type_id != '10' AND run_type_id != '11')";
$sth = $conn->prepare($sql, array(
    PDO :: ATTR_CURSOR => PDO :: CURSOR_FWDONLY
));

$start_date = mktime(0, 0, 0, date("m", strtotime($_GET['start_date'])), date("d", strtotime($_GET['start_date'])), date("Y", strtotime($_GET['start_date'])));
$end_date = mktime(0, 0, 0, date("m", strtotime($_GET['end_date'])), date("d", strtotime($_GET['end_date'])), date("Y", strtotime($_GET['end_date'])));

$start_of_week = $start_date;
// start on sunday
if (date("l", $start_of_week) != "Sunday") {
    $start_of_week = strtotime('last Sunday', $start_of_week);
}

$counter = 0;

while ($start_of_week <= $end_date) {
    $end_of_week = strtotime('next Saturday', $start_of_week);

    $ok = $sth->execute(array(
        ':runner_id' => $runner_id,
        ':week_start' => date("Y-m-d", $start_of_week),
        ':week_end' => date("Y-m-d", $end_of_week)
    ));

    if (!$ok) {
        die(getErrorStatusWithDummyData("Failed to execute prepared statment"));
    } else {
        $result = $sth->fetchAll(PDO :: FETCH_ASSOC);
        $data[] = array(date("Y-m-d", $end_of_week), intval($result[0]['weekly']), intval($result[0]['a']));
        if (intval($result[0]['weekly']) > 0) {
            $counter++;
        }
    }

    $start_of_week = strtotime('+1 week', $start_of_week);
}

$conn = null;
if ($counter == 0) {
    $data = "";
}

echo returnJSONsuccess($data);