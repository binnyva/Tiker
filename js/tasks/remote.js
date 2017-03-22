//The Globals
var current_task_duration_id = 0;
var current_task_id = 0;

function init() {
	$("#show-add-task-form").click(showTaskForm);

	$("#tabs li a").click(activateTab);
	
	$(".task-list li input").click(taskDoneClickHandler);
	$(".task-list li").click(taskClickHandler);

	$("#add-task-form").submit(addTask);
	$("#cancel-add-task").click(hideTaskForm);
	$(".refresh-button").click(refreshPage);
	shortcut.add("Alt+z", showTaskForm);
	shortcut.add("Alt+;", showTaskForm);//Dovark

	fixTaskNameWidth();
}

function refreshPage () {
	document.location.reload();
}

function activateTab(e) {
	var tab = this.href.replace(/.*\#/,"");
	var li  = this.parentNode;
	$("#tabs li").removeClass("active");
	$(li).addClass("active");

	$(".task-list").hide();
	$("#"+tab).show();
}

/// The argument 'tab' should either 'once' or 'recurring'
function openTab(tab) {
	var tab_id = "tab-once-task-list";
	var tab_content_id = "once-task-list";
	if(tab == "recurring") {
		tab_id = "tab-task-list";
		tab_content_id = "recurring-task-list";
	}

	$("#tabs li").removeClass("active");
	$("#" + tab_id).addClass("active");

	$(".task-list").hide();
	$("#"+tab_content_id).show();
}

function showTaskForm(e) {
	shortcut.add("Escape", hideTaskForm);
	shortcut.add("Enter", addTask);
	
	$("#control-area").toggle();
	$("#add-task-form").toggle();
	$("#name").focus();
	e.stopPropagation();
	return false;
}
function hideTaskForm() {
	$("#control-area").toggle();
	$("#add-task-form").hide();
	shortcut.remove("Escape");
	shortcut.remove("Enter");
}

function removePauseIndecators() {
	$(".task-list li").removeClass("paused");
}

///////////////////////////// Event Handlers ///////////////////////////////////
/// Called when the checkbox next to the task is clicked - ends the task. But if the task is a recurring one, it will just pause the task.
function taskDoneClickHandler(e) {
	var task_id = this.value;
	stopTask(task_id);
	e.stopPropagation();
}

/// This happens when a task is clicked - it toggles between starts and pause for that task.
function taskClickHandler(e) {
	var ele = this;
	if(ele.tagName == "INPUT") return; //The checkbox was clicked - This should be handled by taskDoneClickHandler().
	
	var task_id = ele.id.replace(/task\-/,"");
	
	if($(ele).hasClass('working')) { //The task is started - Pause it
		pauseTask(task_id);

	} else { //Start the task
		$(".working").removeClass('working');
		$(ele).addClass('working');
		startTask(task_id);
	}
}

/// Takes an task id and fetches the text part of that <li> element.
function getTaskName(task_id) {
	var ele = $("#task-"+task_id);
	if(ele.text()) return ele.text().replace(/^\s*/,'');
	else return ele.html().replace(/<[^>]+>/g,'').replace(/^\s*/,'');
}

function startTask(task_id) {
	var task_name = getTaskName(task_id);
	$("#timer-task").html(task_name);
	if(!current_task_id != task_id) clock.stop(); //New Task - not a unpausing
	current_task_id = task_id;

	//Visual Reminders
	$("#timer").removeClass('status-paused');
	$("#task-"+task_id).removeClass("paused");
	removePauseIndecators();

	loading();
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=start&task_id="+task_id,
		"dataType": 'json',
		"success": function(data) {
			loaded();
			clock.total.minutes = data.total_time_minutes;
			clock.total.hours = data.total_time_hours;
			clock.start();

			var estimate = calculateEstimate(task_name);
			clock.setEstimate(estimate);

			current_task_duration_id = data.duration_id;
		},
		"error" : function(data) {
			loaded();
			alert("Error retriving data from server.");
		}
	});
}

function pauseTask(task_id) {
	var task_name = getTaskName(task_id);
	$("#timer-task").html(task_name + " [PAUSED]");
	current_task_id = task_id;
	clock.pause();
	
	//Visual Reminders
	$("#timer").addClass('status-paused');
	var task_item = $("#task-"+task_id);
	task_item.addClass("paused");
	task_item.removeClass("working");

	loading();
	//Almost nothing will be returned for this call.
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=pause&task_id="+task_id,
		"success": function(data) {
			loaded();
			current_task_duration_id = 0;
		},
		"error" : function(data) {
			loaded();
			alert("Error retriving data from server.");
		}
	});
}

//If the user closes the popup without stoping/pausing the task - the task will continue 
// - and this function will be called at the next startup
function continueTask(task_id) {
	$("#task-"+task_id).addClass('working');
	var task_name = getTaskName(task_id);
	$("#timer-task").html(task_name);
	current_task_id = task_id;
	loading();

	//Returns should be duration_id, total_time_minutes, total_time_hours, time_taken_hours, time_taken_mins, time_taken_secs
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=continue&task_id="+task_id,
		"dataType": "json",
		"success": function(data) {
			loaded();
			clock.total.minutes	= data.total_time_minutes;
			clock.total.hours	= data.total_time_hours;
			clock.hours			= data.time_taken_hours;
			clock.minutes		= data.time_taken_mins;
			clock.seconds		= data.time_taken_secs;
			var estimate = calculateEstimate(task_name);
			clock.setEstimate(estimate);
			clock.start();

			current_task_duration_id = data.duration_id;

			openTab(data.type);
		},
		"error" : function(data) {
			loaded();
			alert("Error retriving data from server.");
		}
	});
}

function stopTask(task_id) {
	loading();
	$.ajax({
		"url": site_url+"tasks/duration.php?ajax=1&action=done&task_id="+task_id,
		"dataType" : "json",
		"success": function(data) {
			loaded();
			if(data.success) { // The task is done, remove the entry
				$("#timer-task").html("");
				current_task_id = 0;
				clock.stop();
				$("#task-"+task_id).remove();

				current_task_duration_id = 0;
				
			} else { //The task is a recurring one - it cannot be finished off. Pause it instead.
				if($("#task-"+task_id).hasClass('working')) { //The task is started - Pause it
					pauseTask(task_id);
				}
			}
		},
		"error" : function(data) {
			loaded();
			alert("Error retriving data from server.");
		}
	});
}

function addTask(e) {
	var task_name = $("#name").val();
	var task_name_url = encodeURI(task_name);
	var task_start = $("#task-start").attr("checked").toString();

	loading();
	$.ajax({
		"url": site_url+'tasks/new.php',
		"dataType": "json",
		"data": 'action=Add&name='+task_name_url+'&task_start='+task_start+'&ajax=1',
		"method": 'POST',
		"success": function(data) {
			loaded();

			if(data.error) {
				showMessage(error);
				return;
			}
			var task_start = $("#task-start").attr("checked");

			var li = $("<li>", {
					"class": (task_start) ? "working" : "added", 
					"id": "task-"+data.task_id, 
				}).click(taskClickHandler);
			var input = $("<input>", {
					"type": "checkbox", 
					"id": "task-done-"+data.task_id, 
					"value":data.task_id
				}).click(taskDoneClickHandler);
			li.append(input).append($("<span>", {"text":task_name, "class":"task-name"}));

			var task_list = $("#once-task-list");
			task_list.append(li);

			// Show the Once Tab.
			$("#tabs li").removeClass("active");
			$("#tab-once-task-list").addClass("active");

			$(".task-list").hide();
			task_list.show();
			
			if(task_start) {
				removePauseIndecators();
				current_task_id = data.task_id;
				current_task_duration_id = data.duration_id;
				$("#timer-task").html(task_name);
				clock.stop();
				clock.start();

				var estimate = calculateEstimate(task_name);
				clock.setEstimate(estimate);
			}	
		},
		"error" : function(data) {
			loaded();
			alert("Error retriving data from server.");
		}
		});
	$("#name").val("");
	$("#add-task-form").toggle();
	$("#show-add-task-form").toggle();
	e.stopPropagation();
	return false;
}

function calculateEstimate(task_name) {
	var estimate = 0;

	var matches = task_name.match(/(\d+) M(in)?(ute)?s?/i);
	if(matches) {
		estimate += Number(matches[1]);
	}
	matches = task_name.match(/(\d+) H(ou)?r?s?/i);
	if(matches) {
		estimate += Number(matches[1]) * 60;
	}

	return estimate;
}

function fixTaskNameWidth() {
	var container_width = $(".task-list").width();
	var tasks = $(".task-name");

	for (var i = tasks.length - 1; i >= 0; i--) {
		var task = $(tasks[i]);
		var span_length = task.text().length; 

	    var width_ratio = Math.floor((container_width / span_length) * 1.8);
	    var max = 18;
	    var min = 6;
	    if(width_ratio > max) width_ratio = max;
	    if(width_ratio < min) width_ratio = min;

	    var current_width = task.css('font-size').replace(/[^\.\d]/g, "");

	    if(width_ratio < current_width) {
			task.css('font-size', width_ratio + 'px');
		}
	}
}

