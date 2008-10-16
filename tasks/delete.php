<?php
include('../common.php');

if(isset($QUERY['id']) and is_numeric($QUERY['id'])) {
	$Task->remove($QUERY['id']);

	showMessage("Task deleted successfully",'tasks/list.php');
}
