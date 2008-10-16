<?php
include('../common.php');

$html = new HTML;
$template->addResource('libraries/check.js','js');
$task_id_list = $sql->getById("SELECT id,name FROM task WHERE user_id=$_SESSION[user_id]");

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($Duration->edit($QUERY['id'], $QUERY['task_id'], $QUERY['from_time'])) {
		showMessage("Duration updated successfully",'index.php');
	}
}

$data = $Duration->find($QUERY['id']);
render();
