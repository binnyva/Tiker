<h1>Edit Duration in '<a href="edit.php?id=<?=$data['task_id']?>"><?=$data['name']?></a>'</h1>

<form action="" method="post" id="duration-form" class="form-area">
<?php
$html->buildInput("name", "Task", "text", i($data,"name"), array("readonly"=>"readonly"));
$html->buildInput("from_time", "From Time", "time", i($data,"from_time"));
$html->buildInput("to_time", "To Time", "time", i($data,"to_time"));
?>

<input type="hidden" name='id' value="<?=$data['id']?>" />
<input name="action" value="Edit" type='submit' />
</form> 

