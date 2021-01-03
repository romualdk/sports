<!DOCTYPE html>

<html>
  <head>
      <meta charset="utf-8">
      <title>2021 weekly</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">

      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">

  <style type="text/css">
/* http://meyerweb.com/eric/tools/css/reset/ 
   v2.0 | 20110126
   License: none (public domain)
*/

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}


h1 {
  margin: 16px;
  font-size: 48px;
}


body {
  font-family: 'Roboto', sans-serif;
  text-align: center;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
    text-align: center;
    margin: 0 auto;
}

table td {
  padding: 2px;
  color: white;
}

table th {
  color: #a5a5a5;
  padding: 2px;
}

table tbody th {
  padding-right: 8px;
}

table thead th {
  padding-bottom: 8px;
}

td.min .cell { background-color: #99D492; }
td.below .cell { background-color: #56B870; }
td.normal .cell { background-color: #1D9A6C; }
td.above .cell { background-color: #137177;}
td.max .cell { background-color: #0A2F51; }
td.total .cell { background-color: #ffffff; }

td.total { color: #a5a5a5;}

.cell {
  height: 36px;
  width: 36px;
  line-height: 36px;
  font-size: 16px;
  border-radius: 50%;
}

  </style>
  </head>
  <body>

<?php
require __DIR__ . '/vendor/autoload.php';

function getData ($path) {
  $files = glob($path);
  $parser = new \Waddle\Parsers\GPXParser();

  $data = [];

  foreach($files as $file) {
    $activity = $parser->parse($file);

    array_push($data, $act = [
      "year" => intval($activity->getStartTime('Y')),
      "month" => intval($activity->getStartTime('n')),
      "day" => intval($activity->getStartTime('j')),
      "week" => intval($activity->getStartTime('W')),
      "weekday" => $activity->getStartTime('D'),
      "type" => ucfirst(strtolower($activity->getType())),
      "distance" => intval($activity->getTotalDistance()), # In metres, e.g. 1000
      "time" => \Waddle\Converter::convertSecondsToHumanReadable($activity->getTotalDuration()) # In seconds, e.g. 255
    ]);
  }

  return $data;
}

function sumDistance ($carry, $item) {
  $carry += $item["distance"];
  return $carry;
}

function getTotalDistance ($data) {
  return array_reduce($data, "sumDistance");
}

function filterByWeekAndWeekday($data, $week, $weekday) {
  return array_filter($data, function ($item) use ($week, $weekday) {
    return $item["week"] == $week && $item["weekday"] == $weekday;
  });
}

function getDailyDistanceByWeekAndWeekday($data, $week, $weekday) {
  return getTotalDistance(filterByWeekAndWeekday($data, $week, $weekday));
}

function toKM ($distance) {
  return round($distance / 1000, 2);
}

$path = '../data/gpx-2021-r/*.gpx';
$data = getData($path);
$totalDistance = getTotalDistance($data);
$totalDistanceKM = toKM($totalDistance);
$json = json_encode($data, JSON_PRETTY_PRINT);

echo "<pre>";
// echo "Total distance: " . $totalDistanceKM  . " km" . "\r\n\r\n";
// echo $json . "\r\n\r\n";

function getStates ($data) {
  $nums = array_map(function ($item) { return $item["distance"] / 1000; }, $data);

  $ranges = 5;
  $names = ["min", "below", "normal", "above", "max"];

  $max = max($nums);
  $step = $max / $ranges;

  $states = [];
  foreach ($names as $i => $name) {
    array_push($states, [
      "min" => round($i * $step, 2),
      "max" => round(($i + 1) * $step, 2),
      "name" => $name
    ]);
  }

  return $states;
}

function getState($value, $states) {
  if (is_null($value)) {
    return "empty";
  }

  foreach ($states as $state) {
    if ($value >= $state["min"] && $value <= $state["max"]) {
      return $state["name"];
    }
  }

  return "unknown";
}

function getTableDataWeekly ($data, $title) {
  $weekdays = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

  function getWeek($var) {
    return $var["week"];
  }

  $weeks = array_unique(array_map("getWeek", $data));

  $arr = [
    array_merge(["Week"], $weekdays, ["Total"])
  ];

  $grandTotal = 0;
  $weekdayTotal = [];

  foreach ($weeks as $week) {
    $row = [$week];
    $total = 0;

    foreach ($weekdays as $key => $weekday) {
      $value = getDailyDistanceByWeekAndWeekday($data, $week, $weekday);
      $value = !is_null($value) ? $value / 1000 : 0;
      array_push($row, round($value, 2));

      $total += $value;
      $grandTotal += $value;

      if (!array_key_exists($key, $weekdayTotal)) {
        $weekdayTotal[$key] = 0;
      }

      $weekdayTotal[$key] += $value;
    }

    $total = round($total, 2);

    array_push($arr, array_merge($row, [$total]));
  }

  foreach ($weekdayTotal as $key => $value) {
    $weekdayTotal[$key] = round($value, 2);
  }

  $grandTotal = round($grandTotal, 2);

  array_push($arr, array_merge(["Total"], $weekdayTotal, [$grandTotal]));

  return [
    "title" => $title,
    "header-first-row" => true,
    "header-first-column" => true,
    "total-last-row" => true,
    "total-last-column" => true,
    "states" => getStates($data),
    "data" => $arr
  ];
}

function getTableHtml ($tbl) {
  $hasTitle = array_key_exists("title", $tbl);
  $headerFirstRow = array_key_exists("header-first-row", $tbl) && $tbl["header-first-row"];
  $headerFirstCol = array_key_exists("header-first-column", $tbl) && $tbl["header-first-column"];
  $totalLastRow = array_key_exists("total-last-row", $tbl) && $tbl["total-last-row"];
  $totalLastCol = array_key_exists("total-last-column", $tbl) && $tbl["total-last-column"];
  $hasData = array_key_exists("data", $tbl);

  $data = array_key_exists("data", $tbl) ? $tbl["data"] : [];
  $states = array_key_exists("states", $tbl) ? $tbl["states"] : [];

  $html = "";

  if ($hasTitle) {
    $html .= "<h1>" . $tbl["title"] . "</h1>";
  }

  $html .= "<table>\r\n";

  if ($headerFirstRow) {
    $html .= "<thead>\r\n";
    $html .= "<tr>\r\n";
    
    foreach ($data[0] as $value) {
      $html .= '<th>' . $value  . '</th>' . "\r\n";
    }

    $html .= "</tr>\r\n";
    $html .= "</thead>\r\n";
  }

  $html .= "<tbody>\r\n";

  foreach ($data as $key => $row) {
    if ($headerFirstRow && $key == 0) {
      continue;
    }

    $html .= "<tr>\r\n";

    $isTotalRow = $totalLastRow && $key == (count($data) - 1);
    
    foreach($row as $col => $value) {
      $isHeadCol = $headerFirstCol && $col == 0;
      $isTotalCol = $totalLastCol && $col == (count($row) - 1);

      if ($isHeadCol) {
        $html .= '<th>' . $value . '</th>' . "\r\n";
      }
      else if ($isTotalCol) {
        $html .= '<td class="total"><div class="cell">' . round($value) . '</div></td>' . "\r\n";
      }
      else if ($isTotalRow) {
        $html .= '<td class="total"><div class="cell">' . round($value) . '</div></td>' . "\r\n";
      }
      else {
        $class = getState($value, $states);
        $html .= '<td class="' . $class .'"><div class="cell">' . round($value) . '</div></td>' . "\r\n";
      }
    }

    $html .= "</tr>\r\n";
  }

  $html .= "<tbody>\r\n";
  $html .= "</table>\r\n";

  if ($totalLastCol && $totalLastRow) {
    $rows = count($data);
    $cols = count($data[$rows-1]);

    $grandTotal = round($data[$rows-1][$cols-1]);

    $html .= "<h2>Total " . $grandTotal . " km</h2>";
  }

  return $html;
}

$tbl = getTableDataWeekly($data, "2021 weekly");

// echo json_encode($tbl, JSON_PRETTY_PRINT);

echo getTableHtml($tbl);
