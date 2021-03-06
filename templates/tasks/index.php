<h1>Tasks</h1>

<table class="table table-stripped">
<tr><th>Task</th><th>Completed On</th><th>Type</th><th colspan="2">Action</th>
<?php
$row = 0;
foreach($tasks as $task) {
	$class = ($row++ % 2) ? 'even' : 'odd';
	$id = $task['id'];
	if($task['status'] == 'working') $class .= ' working';
?>
<tr class="<?=$class?>">
<td><a href="edit.php?task=<?=$id?>"><?=$task['name']?></a></td>
<td><?php
if($task['status'] == 'working') print 'Still on it';
else print $task['completion_time'];
?></td>
<td><?=ucfirst($task['type'])?></td>

<td class="action"><a class="icon edit" href="edit.php?task=<?=$id?>&amp;action=show_form">Edit</a></td>
<td class="action"><a class="icon delete confirm" href="delete.php?task=<?=$id?>">Delete</a></td></tr>
<?php } ?>
</table>

<?php printPager(); ?>
