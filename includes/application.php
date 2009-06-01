<?php
require_once(joinPath($config['site_folder'], 'models/Task.php'));
require_once(joinPath($config['site_folder'], 'models/User.php'));

$User = new User;

//////////////////////////////////// Authenitication Checks ////////////////////////////////////
function checkUser($redirect = true) {
	global $config;
	
	if((!isset($_SESSION['user_id']) or !$_SESSION['user_id'])) {
		if($redirect) showMessage("Please login to use this feature", $config['site_url'] . 'user/login.php', "error");
		return false;
	}
	return true;
}

/// See if the given task's owner is the currently logined user.
function checkTaskOwnership($task_id, $return_only = false) {
	global $sql;
	$task_owner = $sql->getOne("SELECT user_id FROM Task WHERE id=$task_id");
	$correct_owner = ($task_owner == $_SESSION['user_id']);
		
	if($return_only) return $correct_owner;
	if(!$correct_owner) showMessage("That task don't belong to you.", 'index.php', 'error');
}

/// See if the given duration's owner is the currently logined user.
function checkDurationOwnership($duration_id, $return_only = false) {
	global $sql;
	$task_owner = $sql->getOne("SELECT user_id FROM Task INNER JOIN Duration on Duration.task_id=Task.id 
										WHERE Duration.id=$duration_id");
	$correct_owner = ($task_owner == $_SESSION['user_id']);

	if($return_only) return $correct_owner;
	if(!$correct_owner) showMessage("That Task duration don't belong to you.", 'index.php', 'error');
}


////////////////////////////////////////// Time Functions ///////////////////////
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

//////////////////////////////////////////////// Misc Stuff ////////////////////////////
// Old stuff - can use i() to replace this.
function isRequest($key, $value = false) {
	if($value === false) {
		if(isset($_REQUEST[$key]) and $_REQUEST[$key]) return true;
	}
	if(isset($_REQUEST[$key]) and $_REQUEST[$key] == $value) return true;
	
	return false;
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

