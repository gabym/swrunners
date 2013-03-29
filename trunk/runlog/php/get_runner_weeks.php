<?php 
require_once 'bc_ajax_page_init.php';

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
}
catch(PDOException $e) {
    die(getErrorStatusWithDummyData($e->getMessage()));
}

$data [] = Array ("Name", $result[0]['member_name'], "Weekly running sessions");


$sql = "SELECT (SUM(run_distance) + SUM(warmup_distance) + SUM(cooldown_distance)) as weekly, count(*) as a FROM  tl_events WHERE (runner_id = :runner_id AND run_date >= :week_start AND run_date <= :week_end AND run_type_id != '9' AND run_type_id != '10')";
$sth = $conn->prepare($sql, array (
	PDO :: ATTR_CURSOR => PDO :: CURSOR_FWDONLY
));

$start_date = mktime(0,0,0,date("m", strtotime($_GET['start_date'])),date("d", strtotime($_GET['start_date'])),date("Y", strtotime($_GET['start_date'])));
$end_date = mktime(0,0,0,date("m", strtotime($_GET['end_date'])),date("d", strtotime($_GET['end_date'])),date("Y", strtotime($_GET['end_date'])));

$time_between = $end_date-$start_date;
//find the days
$day_count = ceil($time_between/24/60/60);
$newtime = $start_date;
$counter=0;
//find the names/dates of the days
for($i=0;$i<=$day_count;$i++){
	if($i==0 && date("l",$newtime) != "Sunday"){
	//we're starting in the middle of a week.... show 1 earlier week than the code that follows
		for($s=1;$s<=6;$s++){
			$newtime = $start_date-($s*60*60*24);
			if(date("l",$newtime) == "Sunday"){
				$end_of_week = $newtime+(6*60*60*24);
				$ok = $sth->execute(array (
					':runner_id' => $runner_id,
					':week_start' => date("Y-m-d",$newtime),
					':week_end' => date("Y-m-d",$end_of_week)
				));
				if (!$ok) {
					die(getErrorStatusWithDummyData("Failed to execute prepared statment"));
				} else {
					$result = $sth->fetchAll(PDO :: FETCH_ASSOC);
					$data[] = array (date("Y-m-d",$end_of_week), intval($result[0]['weekly']), intval($result[0]['a']));
					if (intval($result[0]['weekly']) > 0){
						$counter++;
					}
				}
				//echo ." through ".date("Y-m-d",$end_of_week)." is a week.<br />";
			}
		}
	}else{
		$newtime = $start_date+($i*60*60*24);
		if(date("l",$newtime) == "Sunday"){
//Beginning of a week... show it
			$end_of_week = $newtime+(6*60*60*24);
			$ok = $sth->execute(array (
					':runner_id' => $runner_id,
					':week_start' => date("Y-m-d",$newtime),
					':week_end' => date("Y-m-d",$end_of_week)
			));
			if (!$ok) {
				die(getErrorStatusWithDummyData("Failed to execute prepared statment"));
			} else {
				$result = $sth->fetchAll(PDO :: FETCH_ASSOC);
				$data[] = array (date("Y-m-d",$end_of_week), intval($result[0]['weekly']), intval($result[0]['a']));
				if (intval($result[0]['weekly']) > 0){
					$counter++;
				}
			}
			//echo date("Y-m-d",$newtime)." through ".date("Y-m-d",$end_of_week)." is a week.<br />";
		}
	}
}
$conn = null;
if ($counter == 0){
	$data = "";
}

echo returnJSONsuccess($data);