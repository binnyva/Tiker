<?php
include('../common.php');

$project_list = $sql->getById("SELECT id,name FROM Project WHERE user_id=$_SESSION[user_id]");
$project_list[0] = 'Misc';

$pager = new SqlPager("SELECT id, name, type, project_id, status,
	DATE_FORMAT(added_on,'$config[time_format]') AS added_time, DATE_FORMAT(completed_on,'$config[time_format]') AS completion_time
	FROM Task WHERE user_id=$_SESSION[user_id]
	ORDER BY status='working' DESC, added_on DESC", 20);
$tasks = $pager->getPage();

render();