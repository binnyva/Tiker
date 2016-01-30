<?php
include('../common.php');
include('_form.php');

$data = array();
if(i($QUERY, 'action') == 'Add') {
	if($id = $Task->create(urldecode($QUERY['name']), $QUERY['description'], $QUERY['completed_on'], $QUERY['status'], $QUERY['type'], $QUERY['project_id'])) {
		showMessage("Task created successfully","index.php",'success',$id);
	}
}

render();
