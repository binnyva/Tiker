<?php
require('../common.php');
 
if(i($_REQUEST, 'action') == 'Generate Report') {
	$pager = new SqlPager("SELECT Task.id,Task.name,Duration.id AS duration_id, UNIX_TIMESTAMP(Duration.from_time) AS from_time, 
		IF(Duration.to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(Duration.to_time)) AS to_time 
		FROM Task INNER JOIN Duration ON Duration.task_id=Task.id
		WHERE Task.user_id=$_SESSION[user_id] AND Task.name LIKE '%$QUERY[search]%' 
		ORDER BY Duration.from_time DESC", 200);
	$task_details = $pager->getPage();
	$total_time = $sql->getOne("SELECT SUM(IF(to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(to_time)) - UNIX_TIMESTAMP(from_time)) 
		FROM Task INNER JOIN Duration ON Duration.task_id=Task.id
		WHERE Task.user_id=$_SESSION[user_id] AND Task.name LIKE '%$QUERY[search]%'");
}
 
render();