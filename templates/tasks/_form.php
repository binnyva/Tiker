<form action="" method="post" id="task-form" class="form-area">

<?php
$html->buildInput("name", "Name", "text", i($data,"name"));
$html->buildInput("description", "Description", "textarea", i($data,"description"));
if($action == "Edit") $html->buildInput("completed_on", "Completed On", "text", i($data,"completed_on"));
$html->buildInput("status", "Status", "select", i($data,"status"), array("options"=>$status_list));
$html->buildInput("type", "Type", "select", i($data,"type"), array("options"=>$type_list));
$html->buildInput("project_id", "Project", "select", i($data,"project_id"), array("options"=>$project_list));

if($action == "Edit") { ?>
<input type="hidden" name='id' value="<?=$data['id']?>" />
<?php } ?>
<input name="action" value="<?=$action?>" type='submit' />
</form>
