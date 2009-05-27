<?php
include('../common.php');

if(isset($QUERY['duration']) and is_numeric($QUERY['duration'])) {
	checkDurationOwnership($QUERY['duration']);
	$sql->execQuery("DELETE FROM Duration WHERE id=$QUERY[duration]");

	showMessage("Duration deleted successfully",'tasks/edit.php');
}
