<!doctype html>

	<?php 

	date_default_timezone_set('America/New_York');

	$confirmed = 'http://schemas.google.com/g/2005#event.confirmed';

	if ( $_GET["now"] == "" ) {
		$right_now = time();
	} else {
		$right_now = $_GET["now"];
	}

	$next_week = strtotime("+1 week", $right_now);

	$now_str = date("Y-m-d\Th:i:sP", $right_now);
	$next_str = date("Y-m-d\Th:i:sP", $next_week);

	// Get next event from calendar

	$feed = "https://www.google.com/calendar/feeds/vum8llprhqtbstl99h09ldtmnc%40group.calendar.google.com/" . 
	"public/full?orderby=starttime&singleevents=true&" .
	"sortorder=ascending&max-results=1&" .
	"start-min=" . $now_str . "&" .
	"start-max=" . $next_str;


	//  Create a new document from the feed

	$doc = new DOMDocument(); 
	$doc->load( $feed );

	$entries = $doc->getElementsByTagName( "entry" );

	foreach ( $entries as $entry ) {

		$status = $entry->getElementsByTagName( "eventStatus" ); 
		$eventStatus = $status->item(0)->getAttributeNode("value")->value;

		if ($eventStatus == $confirmed) {

			$titles = $entry->getElementsByTagName( "title" ); 
			$title = $titles->item(0)->nodeValue;

			$contents = $entry->getElementsByTagName( "content" );
			$content = $contents->item(0)->nodeValue;

			$title = preg_replace("/ & /", " &amp; ", $title);
			$content = preg_replace("/ & /", " &amp; ", $content);

			$times = $entry->getElementsByTagName( "when" ); 
			$startTime = $times->item(0)->getAttributeNode("startTime")->value;
			$when = date( "l\, F j\, Y", strtotime( $startTime ) );

			// $web = $entry->getElementsByTagName( "link" ); 
			// $link = $web->item(0)->getAttributeNode("href")->value;
		}
	}

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
			<li class="date"><?php echo $when ?></li>
			<li class="response"><?php echo $response ?></li>
			<!--<li class="details"><?php echo $content ?></li>-->
			<li class="next-week"><a href="./?now=<?php echo $next_week ?>"><span>Cool. What about next week?</span></a></li>
		</ul>
		<p class="fine-print"><a href="http://www.thisisawesome.ca">This is Awesome</a>. At the <a href="http://www.tranzac.org/">Tranzac</a>, every Monday 7-9pm, until the end of time. <a href="./">Back to today</a></p>
	</div>
</body>
</html>