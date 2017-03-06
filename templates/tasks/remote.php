<!DOCTYPE HTML>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title ?></title>
<link href="<?php echo $config['site_url']?>css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $config['site_url']; ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $config['site_url']; ?>bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
<?php echo $css_includes?>
</head>
<body>
<div id="loading">loading...</div>
<div id="progress"></div>
<div id="timer">
<div id="timer-total">
Total Time <span id="timer-total-hours">00</span>:<span id="timer-total-mins">00</span>
</div>

<span id="timer-hours">00</span>:<span id="timer-mins">00</span>:<span id="timer-secs">00</span><br />
<span id="timer-task"></span>
</div>

<ul id="tabs">
<li class="active" id="tab-once-task-list"><a href="#once-task-list">Once</a></li>
<li id="tab-task-list"><a href="#recurring-task-list">Recurring</a></li>
</ul>

<ul id="once-task-list" class="task-list">
<?php foreach($tasks as $task) { ?>
<li id="task-<?php echo $task['id']?>">
<input type="checkbox" id="task-done-<?php echo $task['id']?>" value="<?php echo $task['id']?>" />
<?php echo $task['name']?></li>

<?php } ?>
</ul>

<ul id="recurring-task-list" class="task-list">
<?php foreach($recurring_tasks as $task) { ?>
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
<a href="?action=add_form" id="show-add-task-form" class="btn btn-primary" accesskey="z">Add Task</a>
<form action="new.php" method="post" id="add-task-form">
<?php $html->buildInput('name','Task','textarea','',array('cols'=>'30')); ?>
<?php $html->buildInput('task-start','Start Task?','checkbox', '', array('checked'=>'checked')); ?>

<input type='submit' name="action" value="Add" class="btn btn-primary" />
<input type='button' id="cancel-add-task" value="Cancel" class="btn btn-info btn-sm pull-right" /><br />

</form>

<input type="button" id="refresh" value="Refresh" class="btn btn-success pull-right" />

<script src="<?php echo $abs?>bower_components/jquery/dist/jquery.js" type="text/javascript"></script>
<script src="<?php echo $abs?>js/application.js" type="text/javascript"></script>
<script type="text/javascript">
site_url = "<?php echo $config['site_url']; ?>";
</script>
<?php echo $js_includes?>
</body>
</html>
