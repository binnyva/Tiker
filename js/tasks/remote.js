//The Globals
var current_task_duration_id = 0;
var current_task_id = 0;

function init() {
	jQuery("#show-add-task-form").click(showTaskForm);

	jQuery("#tabs li a").click(activateTab);
	
	jQuery(".task-list li").click(taskClickHandler);
	jQuery(".task-list li input").click(taskDoneClickHandler);

	jQuery("#add-task-form").submit(addTask);
	jQuery("#cancel-add-task").click(hideTaskForm);
	shortcut.add("Alt+z", showTaskForm);
	shortcut.add("Alt+;", showTaskForm);//Dovark
}

function activateTab(e) {
	var tab = this.href.replace(/.*\#/,"");
	var li  = this.parentNode;
	jQuery("#tabs li").removeClass("active");
	jQuery(li).addClass("active");

	jQuery(".task-list").hide();
	jQuery("#"+tab).show();
}

function showTaskForm(e) {
	shortcut.add("Escape", hideTaskForm);
	shortcut.add("Enter", addTask);
	
	jQuery("#add-task-form").toggle();
	jQuery("#name").focus();
	e.stopPropagation();
	return false;
}
function hideTaskForm() {
	jQuery("#add-task-form").hide();
	shortcut.remove("Escape");
	shortcut.remove("Enter");
}

function removePauseIndecators() {
	jQuery(".task-list li").removeClass("paused");
}

///////////////////////////// Event Handlers ///////////////////////////////////
/// Called when the checkbox next to the task is clicked - ends the task. But if the task is a recurring one, it will just pasue the task.
function taskDoneClickHandler(e) {
	var task_id = this.value;
	stopTask(task_id);
}

/// This happens when a task is clicked - it toggles between starts and pause for that task.
function taskClickHandler(e) {
	var ele = this;
	if(ele.tagName == "INPUT") return; //The checkbox was clicked - This should be handled by taskDoneClickHandler().
	
	var task_id = ele.id.replace(/task\-/,"");
	
	if(jQuery(ele).hasClass('working')) { //The task is started - Pause it
		pauseTask(task_id);

	} else { //Start the task
		jQuery(ele).addClass('working');
		startTask(task_id);
	}
}

/// Takes an task id and fetches the text part of that <li> element.
function getTaskName(task_id) {
	var ele = jQuery("#task-"+task_id);
	if(ele.text()) return ele.text().replace(/^\s*/,'');
	else return ele.html().replace(/<[^>]+>/g,'').replace(/^\s*/,'');
}

function startTask(task_id) {
	var task_name = getTaskName(task_id);
	jQuery("#timer-task").html(task_name);
	if(!current_task_id != task_id) clock.stop(); //New Task - not a unpausing
	current_task_id = task_id;

	//Visual Reminders
	jQuery("#timer").removeClass('status-paused');
	jQuery("#task-"+task_id).removeClass("paused");
	removePauseIndecators();

	$.ajax({
			"url": site_url+"tasks/duration.php?ajax=1&action=start&task_id="+task_id,
			"dataType": 'json',
			"success": function(data) {
				clock.total.minutes = data.total_time_minutes;
				clock.total.hours = data.total_time_hours;
				clock.start();
				current_task_duration_id = data.duration_id;
			}
		});
	
}

function pauseTask(task_id) {
	var task_name = getTaskName(task_id);
	jQuery("#timer-task").html(task_name + " [PAUSED]");
	current_task_id = task_id;
	clock.pause();
	
	//Visual Reminders
	jQuery("#timer").addClass('status-paused');
	var task_item = jQuery("#task-"+task_id);
	task_item.addClass("paused");
	task_item.removeClass("working");

	//Almost nothing will be returned for this call.
	$.ajax({
			"url": site_url+"tasks/duration.php?ajax=1&action=pause&task_id="+task_id,
			"success": function(data) {
					current_task_duration_id = 0;
				}
			});
}

//If the user closes the popup without stoping/pausing the task - the task will continue 
// - and this function will be called at the next startup
function continueTask(task_id) {
	jQuery("#task-"+task_id).addClass('working');
	var task_name = getTaskName(task_id);
	jQuery("#timer-task").html(task_name);
	current_task_id = task_id;
	
	//Returns should be duration_id, total_time_minutes, total_time_hours, time_taken_hours, time_taken_mins, time_taken_secs
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=continue&task_id="+task_id,
		"dataType": "json",
		"success": function(data) {
			clock.total.minutes	= data.total_time_minutes;
			clock.total.hours	= data.total_time_hours;
			clock.hours			= data.time_taken_hours;
			clock.minutes		= data.time_taken_mins;
			clock.seconds		= data.time_taken_secs;
			clock.start();
			current_task_duration_id = data.duration_id;
		}
	});
}

function stopTask(task_id) {
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=done&task_id="+task_id,
		"dataType" : "json",
		"success": function(data) {
			if(data.success) { // The task is done, remove the entry
				jQuery("#timer-task").html("");
				current_task_id = 0;
				clock.stop();
				jQuery("#task-"+task_id).remove();

				current_task_duration_id = 0;
				
			} else { //The task is a recurring one - it cannot be finished off. Pause it instead.
				if(jQuery("#task-"+task_id).hasClass('working')) { //The task is started - Pause it
					pauseTask(task_id);
				}
			}
		}});
}

function addTask(e) {
	var task_name = jQuery("#name").val();
	var task_start = jQuery("#task-start").attr("checked").toString();

	$.ajax({
			"url": site_url+'tasks/new.php?action=Add&name='+task_name+'&task_start='+task_start+'&ajax=1',
			"dataType": "json",
			"success": function(data) {
				if(data.error) {
					showMessage(error);
					return;
				}
				var task_start = jQuery("#task-start").attr("checked");

				var li = document.createElement("li");
				li.setAttribute('id',"task-"+data.task_id);
				if(task_start) li.className = 'working';
				var input = document.createElement("input");
				input.setAttribute("type","checkbox");
				input.setAttribute("id","task-done-"+data.task_id);
				input.setAttribute("value",data.task_id);
				input.onclick=taskDoneClickHandler;
				li.appendChild(input);
				li.appendChild(document.createTextNode(task_name));
				jQuery(li).click(taskClickHandler);
				var task_list = jQuery("#once-task-list");
				task_list.append(li);

				
				if(task_start) {
					removePauseIndecators();
					current_task_id = data.task_id;
					current_task_duration_id = data.duration_id;
					jQuery("#timer-task").innerHTML = task_name;
					clock.stop();
					clock.start();
				}
			}
		});
	jQuery("#name").val("");
	jQuery("#add-task-form").toggle();
	e.stopPropagation();
	return false;
}

