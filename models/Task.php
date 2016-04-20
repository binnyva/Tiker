<?php
class Task extends DBTable {
	/**
	 * Constructor
	 * Arguments : None
	 */
	function __construct() {
		parent::__construct("Task");
	}

	/**
	 * This will create a new Task and returns the id of the newly created row.
	 */
	function create($name, $description=false, $type='once', $status='working', $project_id=0) {
		$validation_rules = $this->getValidationRules();
		
		$validation_errors = check($validation_rules,2);
		if($validation_errors) {
			$GLOBALS['QUERY']['error'] =  "Please correct the errors before continuing...<br />" . $validation_errors;
			return false;
		}
		
		$this->newRow();
		$this->field['name'] = $name;
		if($description !== false)	$this->field['description'] = $description;
		if($status !== false)		$this->field['status'] = $status;
		if($type !== false)			$this->field['type'] = $type;
		if($project_id !== false)	$this->field['project_id'] = $project_id;
		$this->field['user_id']	= 	$_SESSION['user_id'];
		$this->field['added_on']= 	'NOW()';
				
		return $this->save();
	}
	
	/**
	 * You can edit an existing Task using this function. The first argument 
	 * 		must be the id of the row to be edited
	 */
	function edit($id, $name, $description=false, $status=false, $type=false,  $completed_on=false, $project_id=false) {
		if(!$id) return -1;
		
		$validation_errors = check($this->getValidationRules(),2);
		if($validation_errors) {
			$GLOBALS['QUERY']['error'] =  "Please correct the errors before continuing...<br />" . $validation_errors;
			return false;
		}
		
		$this->newRow($id);
		$this->field['name'] = $name;
		if($description !== false) $this->field['description'] = $description;
		if($completed_on !== false) $this->field['completed_on'] = $completed_on;
		if($status !== false) $this->field['status'] = $status;
		if($type !== false) $this->field['type'] = $type;
		if($project_id !== false) $this->field['project_id'] = $project_id;

		return $this->save();
	}
	
	/**
	 * Delete the Task whose id is given
	 * Argument : $id	- The Id of the row to be deleted.
	 */
	function remove($id) {
		if(!$id) return -1;
		global $sql;
		$this->delete($id);
		
		//Remove the durations for this task as well.
		$sql->execQuery("DELETE FROM Duration WHERE task_id=$id");
		$sql->execQuery("DELETE FROM TaskTag WHERE task_id=$id"); // And delete all its tags as well.
	}
	
	/**
	 * Checks to make sure that there is no other row with the same value in the specified name.
	 * Example: Task.checkDuplicate("username", "binnyva", 4);
	 * 			Task.checkDuplicate("email", "binnyva@email.com");
	 */
	function checkDuplicate($field, $value, $not_id=0) {
		//See if an item with that name is already there.
		$others = $this->find(array(
				"select"	=> 'id',
				'where'		=> array("$field='$value'", "id!=$not_id")));
		if($others) {
			showMessage("Task '$new_name' already exists!","index.php",'error');
		}
		return false;
	}
	
	function getValidationRules() {
		return array(
			array('name'=>'name', 'is'=>'empty', 'error'=>'The Name cannot be empty'),
		);
	}
	
	
	/////////////////////////////// Custom Code ///////////////////////////
	
	function startTask($task_id) {
		checkTaskOwnership($task_id);
		$this->newRow($task_id)->set(array('status'=>'working'))->save();

		return $this->startTimer($task_id);
	}
	
	/// Stops tasks - but if the given task is recurring, this will return false - it will not be stopped(not even paused).
	function stopTask($task_id) {
		$stopped = $this->newRow($task_id)->set(array('status'=>'done', 'completed_on'=>'NOW()'))->where("type!='recurring'", "user_id=$_SESSION[user_id]")->save();
		
		if($stopped) {
			$this->pauseTimer($task_id);
			return true;
		}
		
		// Guess it was recurring task
		return false;
	}

	/// Stops all tasks - even recurring ones [why do we need this again?]
	function forceStopTask($task_id) {
		$stopped = $this->newRow($task_id)->set(array('status'=>'done', 'completed_on'=>'NOW()'))->where("user_id=$_SESSION[user_id]")->save();
		
		if($stopped) $this->pauseTimer($task_id);
	}
	
	/// Start the timer - from the beginning or from a paused state.
	function startTimer($task_id) {
		global $sql;
		$sql->insert('Duration',array(
				'task_id'=>$task_id,
				'from_time'=>'NOW()'
			));
		return $sql->fetchInsertId();
	}
	
	/// Pause the timer.
	function pauseTimer($task_id) {
		global $sql;
		$current_duration_id = $this->getCurrentDuration($task_id);
		
		if($current_duration_id) {
			$sql->Update('Duration',array(
					'task_id'=>$task_id,
					'to_time'=>'NOW()'
				),"WHERE id=$current_duration_id");
		}
		return $current_duration_id;
	}
	
	/// When the user restarts a paused task, he gets info on how much time the CURRENT DURATION as already taken.
	function continueTimer($task_id) {
		global $sql;
		$duration_details = $sql->getAssoc("SELECT D.id, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(from_time) AS time_taken, T.type
												FROM Duration D INNER JOIN Task T ON T.id=D.task_id 
												WHERE task_id=$task_id AND to_time='0000-00-00 00:00:00' 
												ORDER BY from_time DESC LIMIT 0,1");
		return $duration_details;
	}
	
	/// Gets the total time spent on a given task(ALL DURATIONS)
	function getTotalTime($task_id) {
		global $sql;
		$total_time = $sql->getOne("SELECT SUM(UNIX_TIMESTAMP(to_time)-UNIX_TIMESTAMP(from_time)) FROM Duration "
									. "WHERE task_id=$task_id AND to_time!='0000-00-00 00:00:00' GROUP BY task_id");
		return $total_time;
	}
	
	/**
	* Gets the ID of the most recent duration for the given task.
	* Basically, it finds the duration with the highest 'from_time' field of this task.
	*/
	function getCurrentDuration($task_id) {
		global $sql;
		$duration_id = $sql->getOne("SELECT id FROM Duration WHERE task_id=$task_id AND to_time='0000-00-00 00:00:00' "
											. " ORDER BY from_time DESC LIMIT 0,1");
		return $duration_id;
	}
}
$GLOBALS['Task'] = new Task;

/*
Controllor Constructor Code(JSON):

For Duration: {"title":"Duration","class_name":"Duration","object_name":"$Duration","table":"Duration","name_single":"Duration","name_plural":"Durations",
"controller_name":"duration","model_file":"Duration.php","edit_funcionality":"1","delete_funcionality":"1","field_name_1":"id",
"auto_handler_1":"primary_key","field_title_1":"Id","field_type_1":"text","field_date_format_1":"","field_password_encrypt_1":"",
"field_password_salt_1":"","field_filetype_1":"","list_values_1":"","field_foreign_key_reference_1":"id","field_validation_1":["must","unique"],"field_name_2":"task_id","auto_handler_2":"off","field_title_2":"Task","field_type_2":
"foreign_key","field_date_format_2":"","field_password_encrypt_2":"","field_password_salt_2":"","field_filetype_2":"",
"list_values_2":"","field_foreign_key_reference_2":"task.id","field_name_3":"from_time","auto_handler_3":"off",
"field_title_3":"From Time","field_type_3":"time","field_date_format_3":"%d %b %Y, %h:%i %p","field_show_time_3":"1","field_password_encrypt_3":"","field_password_salt_3":"","field_filetype_3":"",
"list_values_3":"","field_foreign_key_reference_3":"from.time","field_validation_3":["must"],"field_name_4":"to_time","auto_handler_4":"off","field_title_4":"To Time","field_type_4":"time",
"field_date_format_4":"%d %b %Y, %h:%i %p","field_show_time_4":"1","field_password_encrypt_4":"","field_password_salt_4":"","field_filetype_4":"",
"list_values_4":"","field_foreign_key_reference_4":"to.time","field_validation_4":["must"],"total_fields":"4","pager_status":"1","pager_items_per_page":"20","upload_path":"..\/uploads","mandatory_text":"*","main_query":"","generate_files":["model.php","templates\/_form.php",
"templates\/edit.php","controllers\/_form.php","controllers\/edit.php","controllers\/delete.php"],"action":"Create Code","error":"","success":""}


For Task...
{"title":"Task","class_name":"Task","object_name":"$Task","table":"Task","name_single":"Task","name_plural":"Tasks",
"controller_name":"task","model_file":"Task.php","add_funcionality":"1","edit_funcionality":"1","delete_funcionality":"1",
"field_name_1":"id","auto_handler_1":"primary_key","field_title_1":"Id","field_type_1":"text","field_date_format_1":"",
"field_password_encrypt_1":"","field_password_salt_1":"","field_filetype_1":"","list_values_1":"","field_foreign_key_reference_1":"id",
"field_validation_1":["must","unique"],"field_name_2":"name","auto_handler_2":"off","field_title_2":"Name","field_list_2":"1",
"field_type_2":"text","field_date_format_2":"","field_password_encrypt_2":"","field_password_salt_2":"","field_filetype_2":"",
"list_values_2":"","field_foreign_key_reference_2":"name","field_validation_2":["must"],"field_name_3":"description","auto_handler_3":"off","field_title_3":"Description","field_type_3":"textarea",
"field_date_format_3":"","field_password_encrypt_3":"","field_password_salt_3":"","field_filetype_3":"","list_values_3":"",
"field_foreign_key_reference_3":"description","field_name_4":"added_on","auto_handler_4":"time_of_insert",
"field_title_4":"Added On","field_type_4":"text","field_date_format_4":"%d %b %Y, %h:%i %p","field_show_time_4":"1","field_password_encrypt_4":"","field_password_salt_4":"","field_filetype_4":"","list_values_4":"",
"field_foreign_key_reference_4":"added.on","field_name_5":"completed_on","auto_handler_5":"off",
"field_title_5":"Completed On","field_type_5":"date","field_date_format_5":"%d %b %Y, %h:%i %p",
"field_show_time_5":"1","field_password_encrypt_5":"","field_password_salt_5":"","field_filetype_5":"","list_values_5":"",
"field_foreign_key_reference_5":"completed.on","field_name_6":"status","auto_handler_6":"off","field_title_6":"Status",
"field_type_6":"list","field_date_format_6":"","field_password_encrypt_6":"","field_password_salt_6":"","field_filetype_6":"",
"list_values_6":"'working'=>'Working', 'scheduled'=>'Scheduled', 'suspended'=>'Suspended', 'done'=>'Done', ",
"field_foreign_key_reference_6":"status","field_name_7":"type","auto_handler_7":"off","field_title_7":"Type",
"field_list_7":"1","field_type_7":"list","field_date_format_7":"","field_password_encrypt_7":"","field_password_salt_7":"",
"field_filetype_7":"","list_values_7":"'recurring'=>'Recurring','once'=>'Once','scheduled'=>'Scheduled',",
"field_foreign_key_reference_7":"type","field_name_8":"project_id","auto_handler_8":"off","field_title_8":"Project Id",
"field_list_8":"1","field_type_8":"foreign_key","field_date_format_8":"","field_password_encrypt_8":"","field_password_salt_8":"",
"field_filetype_8":"","list_values_8":"","field_foreign_key_reference_8":"Project.id","field_name_9":"user_id",
"auto_handler_9":"current_user","field_title_9":"User Id","field_type_9":"text","field_date_format_9":"","field_password_encrypt_9":"","field_password_salt_9":"",
"field_filetype_9":"","list_values_9":"","field_foreign_key_reference_9":"user.id","field_validation_9":["must","unique"],
"total_fields":"9","status_field":"status","pager_status":"1","upload_path":"..\/uploads",
"mandatory_text":"*","main_query":"","generate_files":["model.php","templates\/_form.php",
"templates\/edit.php","templates\/index.php","templates\/add.php","controllers\/_form.php","controllers\/edit.php",
"controllers\/index.php","controllers\/add.php","controllers\/delete.php"],"action":"Create Code","error":"","success":""}

*/
