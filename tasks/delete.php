<?php
include('../common.php');

if(i($QUERY, 'task')) {
	checkTaskOwnership($QUERY['task']);
	$Task->remove($QUERY['task']);

	showMessage("Task deleted successfully",'tasks/index.php');
}
