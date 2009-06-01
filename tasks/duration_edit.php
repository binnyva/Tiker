<?php
include('../common.php');
checkDurationOwnership($QUERY['duration']);

$html = new HTML;
$template->addResource('library/check.js','js');

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($sql->execQuery("UPDATE Duration SET from_time='$QUERY[from_time]', to_time='$QUERY[to_time]' WHERE id=$QUERY[id]")) {
		showMessage("Duration updated successfully");
	}
}

$data = $sql->getAssoc("SELECT Duration.id, Duration.task_id, Task.name, from_time, to_time 
							FROM Duration INNER JOIN Task ON Duration.task_id=Task.id 
							WHERE Task.user_id=$_SESSION[user_id] AND Duration.id=$QUERY[duration]");
render();
