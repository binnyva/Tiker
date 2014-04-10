//The Globals
var current_task_duration_id = 0;
var current_task_id = 0;

function init() {
	JSL.dom("show-add-task-form").click(showTaskForm);

	JSL.dom("#tabs li a").click(activateTab);
	
	JSL.dom(".task-list li").click(taskClickHandler);
	JSL.dom(".task-list li input").click(taskDoneClickHandler);

	JSL.dom("add-task-form").on("submit",addTask);
	JSL.dom("cancel-add-task").click(hideTaskForm);
	shortcut.add("Alt+z", showTaskForm);
	shortcut.add("Alt+;", showTaskForm);//Dovark
}

function activateTab(e) {
	var ele = JSL.event(e).getTarget();
	var tab = ele.href.replace(/.*\#/,"");
	var li  = ele.parentNode;
	JSL.dom("#tabs li").removeClass("active");
	JSL.dom(li).addClass("active");

	JSL.dom(".task-list").hide();
	JSL.dom("#"+tab).show();
}

function showTaskForm(e) {
	shortcut.add("Escape", hideTaskForm);
	shortcut.add("Enter", addTask);
	
	JSL.dom("add-task-form").toggle();
	JSL.dom("name").focus();
	JSL.event(e).stop();
}
function hideTaskForm() {
	JSL.dom("add-task-form").hide();
	shortcut.remove("Escape");
	shortcut.remove("Enter");
}

function removePauseIndecators() {
	JSL.dom("#task-list li").removeClass("paused");
}

///////////////////////////// Event Handlers ///////////////////////////////////
/// Called when the checkbox next to the task is clicked - ends the task. But if the task is a recurring one, it will just pasue the task.
function taskDoneClickHandler(e) {
	var ele = JSL.event(e).getTarget();
	
	var task_id = ele.value;
	stopTask(task_id);
}

/// This happens when a task is clicked - it toggles between starts and pause for that task.
function taskClickHandler(e) {
	var ele = JSL.event(e).getTarget();
	if(ele.tagName == "INPUT") return; //The checkbox was clicked - This should be handled by taskDoneClickHandler().
	
	var task_id = ele.id.replace(/task\-/,"");
	
	if(JSL.dom(ele).hasClass('working')) { //The task is started - Pause it
		pauseTask(task_id);

	} else { //Start the task
		JSL.dom(ele).addClass('working');
		startTask(task_id);
	}
}

/// Takes an task id and fetches the text part of that <li> element.
function getTaskName(task_id) {
	var ele = JSL.dom("task-"+task_id);
	if(ele.textContent) return ele.textContent.replace(/^\s*/,'');
	else return ele.innerHTML.replace(/<[^>]+>/g,'').replace(/^\s*/,'');
}

function startTask(task_id) {
	var task_name = getTaskName(task_id);
	JSL.dom("timer-task").innerHTML = task_name;
	if(!current_task_id != task_id) clock.stop(); //New Task - not a unpausing
	current_task_id = task_id;

	//Visual Reminders
	JSL.dom("timer").removeClass('status-paused');
	JSL.dom("task-"+task_id).removeClass("paused");
	removePauseIndecators();

	JSL.ajax(site_url+"tasks/duration.php?ajax=1&action=start&task_id="+task_id).load(function(data) {
		clock.total.minutes = data.total_time_minutes;
		clock.total.hours = data.total_time_hours;
		clock.start();
		current_task_duration_id = data.duration_id;
	},'j');
}

function pauseTask(task_id) {
	var task_name = getTaskName(task_id);
	$("timer-task").innerHTML = task_name + " [PAUSED]";
	current_task_id = task_id;
	clock.pause();
	
	//Visual Reminders
	JSL.dom("timer").addClass('status-paused');
	var task_item = JSL.dom("task-"+task_id);
	task_item.addClass("paused");
	task_item.removeClass("working");

	//Almost nothing will be returned for this call.
	JSL.ajax(site_url+"tasks/duration.php?ajax=1&action=pause&task_id="+task_id).load(function(data) {
		current_task_duration_id = 0;
	},'j');
}

//If the user closes the popup without stoping/pausing the task - the task will continue 
// - and this function will be called at the next startup
function continueTask(task_id) {
	JSL.dom("task-"+task_id).addClass('working');
	var task_name = getTaskName(task_id);
	JSL.dom("timer-task").innerHTML = task_name;
	current_task_id = task_id;
	
	//Returns should be duration_id, total_time_minutes, total_time_hours, time_taken_hours, time_taken_mins, time_taken_secs
	JSL.ajax(site_url+"tasks/duration.php?ajax=1&action=continue&task_id="+task_id).load(function(data) {
		clock.total.minutes	= data.total_time_minutes;
		clock.total.hours	= data.total_time_hours;
		clock.hours			= data.time_taken_hours;
		clock.minutes		= data.time_taken_mins;
		clock.seconds		= data.time_taken_secs;
		clock.start();
		current_task_duration_id = data.duration_id;
	},'j');
}

function stopTask(task_id) {
	JSL.ajax(site_url+"tasks/duration.php?ajax=1&action=done&task_id="+task_id).load(function(data) {
		if(data.success) { // The task is done, remove the entry
			JSL.dom("timer-task").innerHTML = "";
			current_task_id = 0;
			clock.stop();
			var task_item = JSL.dom("task-"+task_id);
			task_item.parentNode.removeChild(task_item);

			current_task_duration_id = 0;
			
		} else { //The task is a recurring one - it cannot be finished off. Pause it instead.
			if(JSL.dom("task-"+task_id).hasClass('working')) { //The task is started - Pause it
				pauseTask(task_id);
			}
		}
	},'j');
}

function addTask(e) {
	var task_name = $("name").value;
	var task_start = $("task-start").checked.toString();

	JSL.ajax(site_url+'tasks/new.php?action=Add&name='+task_name+'&task_start='+task_start+'&ajax=1').load(function(data) {
		if(data.error) {
			showMessage(error);
			return;
		}
		var task_start = $("task-start").checked;

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
		JSL.dom(li).click(taskClickHandler);
		$("task-list").appendChild(li);
		
		if(task_start) {
			removePauseIndecators();
			current_task_id = data.task_id;
			current_task_duration_id = data.duration_id;
			$("timer-task").innerHTML = task_name;
			clock.stop();
			clock.start();
		}
	},'j');
	$("name").value = '';
	JSL.dom("add-task-form").toggle();
	JSL.event(e).stop();
}

