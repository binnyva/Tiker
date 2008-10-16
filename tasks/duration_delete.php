<?php
include('../common.php');

if(isset($QUERY['id']) and is_numeric($QUERY['id'])) {
	$Duration->remove($QUERY['id']);

	showMessage("Duration deleted successfully",'index.php');
}
