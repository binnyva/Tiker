<h1>Report Generation</h1>

<?php if(i($_REQUEST, 'search')) { ?>

<h2>Report for <?php echo $_REQUEST['search']; ?></h2>

<table>
<tr><th>Task</th><th>From</th><th>To</th><th>Duration</th><th colspan="2">Actions</th></tr>
<?php
$counter = 0;
foreach($task_details as $work) {
	$difference_sec = getTimeDifference($work['from_time'], $work['to_time'], 'seconds');
	$diff = seconds2hourmin($difference_sec, 'string');
?>
<tr class="<?php echo ($counter++ % 2) ? 'odd' : 'even'?>">
<td><?php echo $work['name'] ?></td>
<td><?php echo date('d M Y, h:i a', $work['from_time'])?></td>
<td><?php echo date('h:i a', $work['to_time'])?></td>
<td><?php echo $diff?></td>
<td><a href="../tasks/duration_edit.php?duration=<?php echo $work['duration_id']?>" class="with-icon edit">Edit</a></td>
<td><a href="../tasks/duration_delete.php?duration=<?php echo $work['duration_id']?>&amp;task=<?php echo $work['id']?>" class="with-icon delete confirm">Delete</a></td></tr>

<?php } ?>
</table>

<?php printPager(); ?>

<strong>Total Time</strong>: <?php echo seconds2hourmin($total_time, 'string'); ?>

<?php } ?>
