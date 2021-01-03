<?php
require __DIR__ . '/vendor/autoload.php';

$format = $format = "d/m/Y H:i:s";

$files = glob('../data/endomondo-workouts-r/*.tcx');
$parser = new \Waddle\Parsers\TCXParser();

$data = [];

foreach($files as $file) {


  $activity = $parser->parse($file);

  $type = $activity->getType();
  $start = $activity->getStartTime($format);
  $year = intval($activity->getStartTime('Y'));
  $month = intval($activity->getStartTime('m'));
  $day = intval($activity->getStartTime('d'));
  $distance = intval($activity->getTotalDistance()); # In metres, e.g. 1000
  $duration = \Waddle\Converter::convertSecondsToHumanReadable($activity->getTotalDuration()); # In seconds, e.g. 255
  $calories = intval($activity->getTotalCalories()); # e.g. 100

  $act = [
    "start" => $start,
    "year" => $year,
    "month" => $month,
    "day" => $day,
    "type" => strtolower($type),
    "distance" => $distance,
    "time" => $duration,
    "calories" => $calories
  ];

 array_push($data, $act);
}


/*function inYear($activity) {
  global $YEAR;
  return $activity["year"] == $YEAR;
}

$data = array_filter($data, "inYear");
*/

echo '<pre>';
echo '[' . "\r\n";
foreach($data as $row) {
  echo json_encode($row) . ',';
  echo "\r\n";
}
echo ']';
