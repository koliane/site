<?php
require_once "../prolog.php";
require_once "../lib/get_data_from_db.php";

$pdo = connectToDB();

$tableName = $_REQUEST['table'];
$timeFrame = $_REQUEST['timeFrame'];
$startTime = $_REQUEST['startTime'];

if( isset($_REQUEST['endTime']) )
    $endTimeOrCount = $_REQUEST['endTime'];
else
    $endTimeOrCount = false;

//$tableName = 'eurusd';
//$timeFrame = 'm1';
//$startTime = '';
//$endTimeOrCount = 6;

if(!$endTimeOrCount)
    $data = getDataFromDB( $tableName, $timeFrame, $startTime);
else {
    if ($endTimeOrCount === "true")
        $endTimeOrCount = true;
    elseif (is_string($endTimeOrCount) && !strpos($endTimeOrCount, ','))
        $endTimeOrCount = (int)$endTimeOrCount;

    $data = getDataFromDB( $tableName, $timeFrame, $startTime, $endTimeOrCount);
}

//echo '<pre>';
//print_r($data);
//echo '</pre>';
$data = json_encode( $data );
echo $data;
?>