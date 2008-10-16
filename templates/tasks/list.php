<h1>Tasks</h1>

<table class="listing-table">
<?php
$row = 0;
foreach($tasks as $task) {
	$class = ($row++ % 2) ? 'even' : 'odd';
	$id = $task['id'];
	if($task['status'] == 'working') $class .= ' working';
?>
<tr class="<?=$class?>">
<td><?=$task['name']?></td>
<td><?php
if($task['status'] == 'working') print 'Still on it';
else print $task['completion_time'];
?></td>
<td><?=ucfirst($task['type'])?></td>

<td class="action"><a class="icon edit" href="edit.php?id=<?=$id?>">Edit</a></td><td class="action"><a class="icon delete confirm" href="delete.php?id=<?=$id?>">Delete</a></td></tr>
<?php } ?>
</table><br />

<div id="pager">
<?php
$pager->status_template = 'Page %%PAGE%% of %%TOTAL_PAGES%%';
print $pager->getLink("first") . $pager->getLink("previous");
$pager->printGoToDropDown();
print $pager->getLink("next") . $pager->getLink("last");print "<br />";
print $pager->getStatus(); 
?>
</div>
