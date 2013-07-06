<?php
header('Content-Type: text/xml');

    $feed = "https://www.google.com/calendar/feeds/vum8llprhqtbstl99h09ldtmnc%40group.calendar.google.com/" . 
        "public/full?orderby=starttime&singleevents=true&" .
        "sortorder=ascending&" .
        "start-min=" . $right_now . "&" .
        "start-max=" . $next_week;

        echo($feed);
        ?>