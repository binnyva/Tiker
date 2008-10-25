<?php
include('../common.php');

if(isset($QUERY['duration']) and is_numeric($QUERY['duration'])) {
	$Task->remove($QUERY['duration']);

	showMessage("Task deleted successfully",'tasks/index.php');
}
