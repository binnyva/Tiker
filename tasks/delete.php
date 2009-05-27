<?php
include('../common.php');

if(isset($QUERY['task']) and is_numeric($QUERY['task'])) {
	checkTaskOwnership($QUERY['task']);
	$Task->remove($QUERY['task']);

	showMessage("Task deleted successfully",'tasks/index.php');
}
