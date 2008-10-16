<h1>Edit Duration '<?=$data['name']?>'</h1>

<form action="" method="post" id="duration-form" class="form-area">
<?php
$html->buildInput("task_id", "Task", "select", i($data,"task_id"), array("options"=>$task_id_list));
$html->buildInput("from_time", "From Time", "time", i($data,"from_time"));
?>

<input type="hidden" name='id' value="<?=$data['id']?>" />
<input name="action" value="Edit" type='submit' />
</form> 

