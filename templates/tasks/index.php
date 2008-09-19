<div id="timer">
<div id="timer-total">
Total Time <span id="timer-total-hours">00</span>:<span id="timer-total-mins">00</span>
</div>

<span id="timer-hours">00</span>:<span id="timer-mins">00</span>:<span id="timer-secs">00</span><br />
<span id="timer-task"></span>
</div>

<ul id="task-list">
<?php foreach($tasks as $task) { ?>
<li id="task-<?=$task['id']?>">
<input type="checkbox" id="task-done-<?=$task['id']?>" value="<?=$task['id']?>" />
<?=$task['name']?></li>

<?php } ?>
</ul>

<?php if($active_tasks) { ?>
<script type="text/javascript">
<?php foreach($active_tasks as $task) { ?>
continueTask(<?=$task['id']?>);
<?php } ?>
</script>
<?php } ?>

<br />
<a href="?action=add_form" id="show-add-task-form" accesskey="z">Add Task</a>
<form action="new.php" method="post" id="add-task-form" class="form-area">
<?php $html->buildInput('name','Task','textarea','',array('cols'=>'30')); ?>
<label>&nbsp;</label><input type='submit' name="action" value="Add" />
<input type='button' id="cancel-add-task" value="Cancel" /><br />
<?php $html->buildInput('task-start','Start Task?','checkbox', '', array('checked'=>'checked')); ?>

</form>
