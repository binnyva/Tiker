<?php
require_once(joinPath($config['site_folder'], 'models/Task.php'));

$_SESSION['user_id'] = 1;

function isRequest($key, $value = false) {
	if($value === false) {
		if(isset($_REQUEST[$key]) and $_REQUEST[$key]) return true;
	}
	if(isset($_REQUEST[$key]) and $_REQUEST[$key] == $value) return true;
	
	return false;
}

/// Get the time difference between the two given time and returns it as an hour, minute array
function getTimeDifference($from, $to, $return_type='hour_min') {
	// The argument can be a timestamp or a mysql date string - both are parsed correctly
	if(!is_numeric($from)) $from = strtotime($from);
	if(!is_numeric($to)) $to = strtotime($to);
	if(!$to) $to = time(); // If $to is 0, that means its an ongoing task - so give it current time.
	
	$diff = $to - $from;
	if($return_type != 'hour_min') return $diff;
	return seconds2hourmin($diff);
}

/// Converts the seconds given as the argument to a hour, minute array.
function seconds2hourmin($seconds, $return_type='array') {
	$minute_difference = ($seconds/60) % 60;
	$hour_difference = floor(($seconds/60)/60);
	
	if($return_type != 'array') {
		$diff = '';
		if($hour_difference) $diff = "$hour_difference hours, ";
		$diff .= "$minute_difference mins";
		return $diff;
	}
	return array($hour_difference, $minute_difference);
}

/// Prints a pager.
function printPager($pager = false) {
	if($pager === false) $pager = $GLOBALS['pager'];
	
	if($pager->total_pages < 2) return;
	print '<div class="pager">';
	print $pager->getLink("first") . $pager->getLink("previous");
	$pager->printGoToDropDown();
	print $pager->getLink("next") . $pager->getLink("last");print "<br />";
	print '</div>';
}

