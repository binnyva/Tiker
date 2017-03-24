<?php
class Task extends DBTable {
	private $_tag;
	/**
	 * Constructor
	 * Arguments : None
	 */
	function __construct() {
		parent::__construct("Task");

		// http://www.jqueryscript.net/form/Creating-An-Tweet-Like-Text-Box-with-jQuery-Tweetbox.html
		$this->_tag = new Tagging;
		$this->_tag->setReferenceTable('TaskTag', 'term_id', 'task_id');
		$this->_tag->setTagTable('Term');
		$this->_tag->setItemTable('Task');
		// $this->_tag->checkSetup(); // :DEBUG:
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
		$this->field['name'] = $this->getTaskName($name);
		if($description !== false)	$this->field['description'] = $description;
		if($status !== false)		$this->field['status'] = $status;
		if($type !== false)			$this->field['type'] = $type;
		if($project_id !== false)	$this->field['project_id'] = $project_id;
		$this->field['user_id']	= 	$_SESSION['user_id'];
		$this->field['added_on']= 	'NOW()';

		$task_id = $this->save();
		
		$this->parseTags($name, $task_id);
		return $task_id;
	}
	
	/**
	 * You can edit an existing Task using this function. The first argument 
	 * 		must be the id of the row to be edited
	 * Not sure if this is ever used. 
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
		if($description !== false)	$this->field['description'] = $description;
		if($completed_on !== false)	$this->field['completed_on'] = $completed_on;
		if($status !== false)		$this->field['status'] = $status;
		if($type !== false)			$this->field['type'] = $type;
		if($project_id !== false)	$this->field['project_id'] = $project_id;

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
		return array(array(	
			'name'	=> 'name', 
			'is'	=> 'empty', 
			'error'	=> 'The Name cannot be empty'
		));
	}
	
	/// Start a task
	function startTask($task_id) {
		checkTaskOwnership($task_id);
		$this->stopAllTasks();
		$this->newRow($task_id)->set(array('status'=>'working'))->save();

		return $this->startTimer($task_id);
	}
	
	/// Stops tasks - change status to 'done'
	function stopTask($task_id) {
		global $sql;

		$current_duration_id = $this->getCurrentDuration($task_id);
		if($current_duration_id) $sql->update("Duration", array('to_time' => 'NOW()'), "WHERE id=$current_duration_id");
		$stopped = $this->newRow($task_id)->set(array('status'=>'done', 'completed_on'=>'NOW()'))->where("type!='recurring'", "user_id=$_SESSION[user_id]")->save();
		
		return $stopped;
	}

	/// Stops all tasks - even recurring ones [why do we need this again?]
	function forceStopTask($task_id) {
		$stopped = $this->newRow($task_id)->set(array('status'=>'done', 'completed_on'=>'NOW()'))->where("user_id=$_SESSION[user_id]")->save();
		
		if($stopped) $this->pauseTimer($task_id);
	}

	/// Stops all the tasks that are running for the current user
	function stopAllTasks() {
		global $sql;

		$working_tasks = $this->select('id')->where(array("status" => 'working', "user_id"=> $_SESSION['user_id']))->get('col');
		$task_update_count = 0;
		foreach ($working_tasks as $task_id) {
			$current_duration_id = $this->getCurrentDuration($task_id);
			if($current_duration_id) $sql->update("Duration", array('to_time' => 'NOW()'), "WHERE id=$current_duration_id");
			$task_update_count += $this->newRow($task_id)->set(array('status' => 'paused', 'completed_on'=>'NOW()'))
										->where(array("status" => 'working', "user_id" => $_SESSION['user_id']))->save();
		}
		return $task_update_count;
	}
	
	/// Start the timer - from the beginning or from a paused state.
	function startTimer($task_id) {
		global $sql;
		$sql->insert('Duration',array(
				'task_id'	=> $task_id,
				'from_time'	=> 'NOW()'
			));
		return $sql->fetchInsertId();
	}
	
	/// Pause the timer.
	function pauseTimer($task_id) {
		global $sql;
		$current_duration_id = $this->getCurrentDuration($task_id);
		
		if($current_duration_id) {
			$sql->update('Task',array('status' => 'paused'),"WHERE id=$task_id");

			$sql->update('Duration',array(
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
		$total_time = $sql->getOne("SELECT SUM(UNIX_TIMESTAMP(to_time)-UNIX_TIMESTAMP(from_time)) FROM Duration 
										WHERE task_id=$task_id AND to_time!='0000-00-00 00:00:00' GROUP BY task_id");
		return $total_time;
	}
	
	/**
	* Gets the ID of the most recent duration for the given task.
	* Basically, it finds the duration with the highest 'from_time' field of this task.
	*/
	function getCurrentDuration($task_id) {
		global $sql;
		$duration_id = $sql->getOne("SELECT id FROM Duration 
										WHERE task_id=$task_id AND to_time='0000-00-00 00:00:00' 
										ORDER BY from_time DESC LIMIT 0,1");
		return $duration_id;
	}

	/// Parse the name of the task to figure out the Tags in the name
	function parseTags($body, $entry_id = 0) {
		preg_match_all("/#([\w\-]+)/", $body, $matches);

		if($matches and $entry_id) {
			$this->_tag->setTags($entry_id, $matches[1]);
		}

		return preg_replace("/#[\w\-]+/", '', $body);
	}

	/// Strips tag, project and time from task and just returns the task name
	function getTaskName($body) {
		$task_name = preg_replace("/#[\w\-]+/", '', $body);
		// $task_name = preg_replace("/\@[\w\-]+/", '', $task_name);
		// $task_name = preg_replace("/\:\s*[\d\:]+\s*(Mins|Minutes|Hr|Hrs|Hours|H|M)\s*$/i", '', $task_name);

		return $task_name;
	}
}
$GLOBALS['Task'] = new Task;
