<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title><?php echo $title ?></title>
<link href="<?php echo $config['site_url']?>css/style.css" rel="stylesheet" type="text/css" />
<?php echo $css_includes?>
</head>
<body>
<div id="loading">loading...</div>

<div id="timer">
<div id="timer-total">
Total Time <span id="timer-total-hours">00</span>:<span id="timer-total-mins">00</span>
</div>

<span id="timer-hours">00</span>:<span id="timer-mins">00</span>:<span id="timer-secs">00</span><br />
<span id="timer-task"></span>
</div>

<ul id="task-list">
<?php foreach($tasks as $task) { ?>
<li id="task-<?php echo $task['id']?>">
<input type="checkbox" id="task-done-<?php echo $task['id']?>" value="<?php echo $task['id']?>" />
<?php echo $task['name']?></li>

<?php } ?>
</ul>

<?php if($active_tasks) { ?>
<script type="text/javascript">
function main() {
<?php foreach($active_tasks as $task) { ?>
continueTask(<?php echo $task['id']?>);
<?php } ?>
}
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

<script src="<?php echo $abs?>js/library/jsl.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/application.js" type="text/javascript"></script>
<script type="text/javascript">
site_url = "<?php echo $config['site_url']; ?>";
</script>
<?php echo $js_includes?>
</body>
</html>
