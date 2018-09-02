<?php
if(isset($_GET['t']) && isset($_GET['sd']) && isset($_GET['ed']) && isset($_GET['d']) && isset($_GET['a']) && isset($_GET['u'])){
	header("content-type:text/".(isset($mime)?$mime:'x-icalendar'));
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Disposition: attachment; filename=".$_GET['u'].'.'.(isset($format)?$format:'ics').';' );

	$date_start = $_GET['sd'];
	$vtz = $_GET['tz'];
	$gmt = $_GET['gmt'];
	$date_end = $_GET['ed'];

        $separator = "\n";

        $props = array();

        // General
        $props[] =  'BEGIN:VCALENDAR';
        $props[] =  'PRODID://WordPress//Event-Post V'. file_get_contents(('../VERSION')).'//EN';
        $props[] =  'VERSION:2.0';

        // Timezone
        if(!empty($vtz)){
            array_push($props,
                'BEGIN:VTIMEZONE',
                'TZID:'.$vtz,
                'BEGIN:DAYLIGHT',
                'TZOFFSETFROM:+0100',
                'TZOFFSETTO:'.($gmt).'00',
                //'TZNAME:CEST',
                'DTSTART:19700329T020000',
                'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3',
                'END:DAYLIGHT',
                'BEGIN:STANDARD',
                'TZOFFSETFROM:'.($gmt).'00',
                'TZOFFSETTO:+0100',
                'TZNAME:CET',
                'DTSTART:19701025T030000',
                'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10',
                'END:STANDARD',
                'END:VTIMEZONE'
            );
        }

        // Event
        array_push($props,
            'BEGIN:VEVENT',
            'SUMMARY:'.stripslashes($_GET['t']),
            'UID:'.$_GET['u'],
            'LOCATION:'.stripslashes($_GET['a']),
            'DTSTART'.(!empty($vtz)?';TZID='.$vtz:'').':'.$date_start.(!empty($vtz)?'':'Z'),
            'DTEND'.(!empty($vtz)?';TZID='.$vtz:'').':'.$date_end.(!empty($vtz)?'':'Z'),
            'DESCRIPTION:'.stripslashes($_GET['d']),
            'END:VEVENT'
        );

        // End
        $props[] =  'END:VCALENDAR';

	echo implode($separator, $props);
}
?>