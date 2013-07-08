<?php 

// Set the timezone or you don't get anything!
date_default_timezone_set('America/Toronto');

// Is there a query variable? If so, set the time by taht. If not, set it by today.
if ( $_GET["now"] == "" ) {
	$rightNow = time();
} else {
	$rightNow = $_GET["now"];
}

// Is it Monday? If not, look ahead to next Monday
if (date('w', $rightNow) == 1) {
	$thisMonday = strtotime('noon', $rightNow);
} else {
	$thisMonday = strtotime('next Monday', $rightNow);
	$thisMonday = strtotime('noon', $thisMonday);
}
$nextMonday = strtotime("+1 week", $thisMonday);

// Debug:
// echo('<p>' . date("r", $thisMonday) . '</p>');

// Set up some strings for Google Cal and display:
$thisMondayStr = date("Y-m-d\Th:i:sP", $thisMonday);
$nextMondayStr = date("Y-m-d\Th:i:sP", $nextMonday);
$thisMondayDisplay = date( "l\, F j\, Y", $thisMonday );

// Get next event from calendar:
$queryUrl = "https://www.google.com/calendar/feeds/vum8llprhqtbstl99h09ldtmnc%40group.calendar.google.com/" . 
"public/full?orderby=starttime&singleevents=true&" .
"sortorder=ascending&max-results=1&" .
"start-min=" . $thisMondayStr . "&" .
"start-max=" . $nextMondayStr;

$xml = simplexml_load_file($queryUrl);

// There should only be one element returned, so you don't *really* need to loop through it, but it's easy:
foreach ($xml->entry as $entry) {
	$title = $entry->title;
	$content = $entry->content;
	// Getting the date is snaky - I copied this and it works. We don't actually use start time, so this is just left in for giggles:
	// $gd = $entry->children('http://schemas.google.com/g/2005');
    // $startTime = $gd->when->attributes()->startTime;
}

// What to do with our results:
if ( $title == "" ) {
	$title = "Dunno";
	$content = "but you should go anyway, because there’s bound to be something cool happening.";
}
if ( $content == "" ) {
	$response = $title . ".";
} else {
	$response = $title . ", " . $content;
}

?>

<?php
// Okay - now display the page...
?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>What is awesome this week?</title>
	<link rel="stylesheet" href="stylesheets/screen.css">
</head>
<body>
	<div class="wrap">
		<h1>Are Christine and Dafydd playing at the Tranzac this Week?</h1>
		<ul>
			<li class="date"><?php echo $thisMondayDisplay; ?></li>
			<li class="response"><?php echo $response; ?></li>
			<li class="next-week"><a href="./?now=<?php echo $nextMonday; ?>"><span>Cool. What about next week?</span></a></li>
		</ul>
		<p class="fine-print"><a href="http://www.thisisawesome.ca">This is Awesome</a>. At the <a href="http://www.tranzac.org/">Tranzac</a>, every Monday 7-9pm, until the end of time. <a href="./">Back to today</a></p>
	</div>
</body>
</html>