<h1>Task '<?=$data['name']?>'</h1>

<h2>View Details</h2>
<ul>
<?php foreach($task_details as $work) {
$difference = getTimeDifference($work['from_time'], $work['to_time']);
$diff = '';
if($difference[0]) $diff = "$difference[0] hours, ";
$diff .= "$difference[1] mins";
?>
<li>From <?=date('d M Y, H:i a', $work['from_time'])?> to <?=date('H:i a', $work['to_time'])?> (<?=$diff?>)</li>

<?php } ?>
</ul>

<h2>Edit Task</h2>
<?php
$action = 'Edit';
require("../templates/tasks/_form.php");
?>
