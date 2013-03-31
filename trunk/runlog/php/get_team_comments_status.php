<?php

require_once 'ajax_page_init.php';

$runnerId = $memberAuthentication->getMemberId();

try {
    $conn = getConnection();

    $sql = "SELECT timestamp AS last_fetched FROM tl_team_comments_last_fetched WHERE runner_id = '" . $runnerId . "'";
    $stmt = $conn->query($sql);
    $teamCommentsLastFetchedByRunner = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($teamCommentsLastFetchedByRunner) == 0){
        $teamCommentsLastFetchedByRunner = array(
            0 => array(
                'last_fetched' => '0000-00-00 00:00:00',
            ),
        );
    }


    $sql = "SELECT count(*) AS count_new_comments FROM tl_comments WHERE runner_id != '" . $runnerId . "' AND timestamp >= '" . $teamCommentsLastFetchedByRunner[0]['last_fetched'] . "'";
    $stmt = $conn->query($sql);
    $countNewTeamComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;

    $result = array_merge($teamCommentsLastFetchedByRunner[0], $countNewTeamComments[0]);
    echo returnJSONsuccess($result);
}
catch (PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}