<?php
include('../common.php');
include('_form.php');

$data = array();
if(isset($QUERY['action']) and $QUERY['action']=='Add') {
	if($id = $Task->create($QUERY['name'], $QUERY['description'], $QUERY['completed_on'], $QUERY['status'], $QUERY['type'], $QUERY['project_id'])) {
		showMessage("Task created successfully","index.php",'success',$id);
	}
}

render();
