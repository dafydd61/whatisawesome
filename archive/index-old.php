<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Title</title>

  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<!--
   This lets the world know of the corresponding RSS feed, in the
   script google_calendar_xml_parse_to_rss.php:
-->

<link rel="alternate" type="application/rss+xml"
    title="This Week" href="http://yourwebsite.com/google_calendar_xml_parse_to_rss.php" />


</head>

<body>

<h1>Title</h1>

<!-- We're going to make everything into a list, so start
our HTML with the appropriate marker: -->

<ul>

<!-- Start up the PHP script -->

<?php 
// Modified from P.J. Cabrera's "Listing 5" at 
// http://www.ibm.com/developerworks/opensource/library/os-php-xpath/
// License at http://www.ibm.com/developerworks/apps/download/index.jsp?contentid=270615&filename=os-php-xpath.google-calendar-api.zip&method=http&locale=worldwide:w

//  Set the time zone.  See the supported time zones here:
//   http://php.net/manual/en/timezones.php
//  As an example, we'll use US Eastern time, so

    date_default_timezone_set('America/New_York');

//  This tells the code where to look in Google's data protocal
//   to find the tags used in the calendar feed.  Note that
//   we're only looking at "confirmed" links.  For more details, see
//   http://code.google.com/apis/gdata/docs/1.0/elements.html

    $confirmed = 'http://schemas.google.com/g/2005#event.confirmed';

// This puts the date in a form Google will read:

    $right_now = date("Y-m-d\Th:i:sP", time());

//  For our purposes, a week will be 8 days.  This allows next
//   Sunday's schedule to appear on the preceeding Sunday
//  Adjust for your own purposes

    $week_in_seconds = 60 * 60 * 24 * 8;
    $next_week = date("Y-m-d\Th:i:sP", time() + $week_in_seconds);

//  This is my version of the call to Google's API.  See
//   http://code.google.com/apis/calendar/data/2.0/reference.html#Parameters
//   for alternatives.

//   This version gets all the events happening starting from right now until
//   eight days from now.

//  Don't forget to replace "yourcalendaraddress" by your Google
//   calendar address.  For your default calendar, it's just your gmail
//   address before the "@gmail.com"

    $feed = "https://www.google.com/calendar/feeds/vum8llprhqtbstl99h09ldtmnc%40group.calendar.google.com/" . 
        "public/full?orderby=starttime&singleevents=true&" .
        "sortorder=ascending&max-results=1&" .
        "start-min=" . $right_now . "&" .
        "start-max=" . $next_week;

        echo($feed);

//  Create a new document from the feed

    $doc = new DOMDocument(); 
    $doc->load( $feed );

//  We're looking for all the entries in the feed, denoted, logically
//   enough, by the tag "entry"

    $entries = $doc->getElementsByTagName( "entry" ); 

//  This is pretty much self-explanatory

    foreach ( $entries as $entry ) { 
    
// Find the status of a given entry

        $status = $entry->getElementsByTagName( "eventStatus" ); 
        $eventStatus = $status->item(0)->getAttributeNode("value")->value;

// If it's confirmed, parse it

        if ($eventStatus == $confirmed) {

// This looks at the "title" tag.

            $titles = $entry->getElementsByTagName( "title" ); 
            $title = $titles->item(0)->nodeValue;

            $d = $entry->getElementsByTagName( "content" );
            $description = $d->item(0)->nodeValue;


// $title might have an unescaped isolated ampersand in it (as in
// "Chat & Chew".)  This will fix that so that the web page will validate

            $title = ereg_replace(" & ", " &amp; ", $title);

// This looks at the "gd:when" tag,
//  to get the actual time the event is going to happen.
// Note that the "gd" indicates this is part of the Google schema

            $times = $entry->getElementsByTagName( "when" ); 

// Pull off the time

            $startTime = $times->item(0)->getAttributeNode("startTime")->value;

// Parse it into something we like.  For other formatting options see
// http://php.net/manual/en/function.date.php

	    $when = date( "l\, F j\, Y \a\\t h:i A T", strtotime( $startTime ) );

// Ditto for location

            $places = $entry->getElementsByTagName( "where" ); 
            $where = $places->item(0)->getAttributeNode("valueString")->value;

// There may be multiple link elements in the file.  This picks off
//  the first one, which takes you to the event page for the Google
//  calendar.  Note that "link", like "title", is not part of the
//  Google schema, so it's referenced by "<link ...>" rather than
//  "<gd:link ...>"

            $web = $entry->getElementsByTagName( "link" ); 
            $link = $web->item(0)->getAttributeNode("href")->value;

//  You can pick off other tags, of course, but these are the ones
//   I need.

//  Now print out the HTML for this element.  Be careful to
//   escape all of the double quotation marks.  Note that
//   you don't really need the "\n" end of line characters,
//   I just put them in to make the resulting page easier to read
//   for debugging purposes

            echo "<li>\n";
            echo "<strong>What:</strong> ";
// If you don't specify the time zone here, (leaving off &amp;...New_York),
//  Then anyone actually clicking on the link will get the time of the event
//  in GMT.  So change America/New_York to your default time zone
//  (added 2 October 2010):
            echo "<a target=\"_blank\" href=\"$link&amp;ctz=America/New_York\">";
            echo "$title</a> <br />\n";
            echo "$description</a> <br />\n";
            echo "<strong>When:</strong> $when <br />\n";
            echo "<strong>Where:</strong> $where\n";
            echo "</li>\n\n"; 
	}
}
?>

<!-- That's the end of the PHP code, close up the list and end the page -->

</ul>

</body>

</html>
