/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.B
 * By Binny V A
 * License : BSD
 * Shortcut is a library that makes it easy to add keyboard shortcuts(or accelerators) to your javascript application.
 */
shortcut = {
	'event_attached':false,
	'all_shortcuts':{},//All the shortcuts are stored in this array
	//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
	'shift_keys': {
		')':48,'!':49,'@':50,'#':51,'$':52,'%':53,'^':54,'&':55,'*':56,'(':57,
		':':59,'_':109,'~':192,'{':219,'}':221,'?':191,'+':61,"\"":222,'<':188,'>':190,"|":220
	},
	//Keys - and their codes
	'key_map': {
		//Special Keys 
		'esc':27,'escape':27,'tab':9,'space':32,'return':13,'enter':13,'backspace':8,
		'scrolllock':145,'scroll_lock':145,'scroll':145,'capslock':20,'caps_lock':20,
		'caps':20,'numlock':144,'num_lock':144,'num':144,
		'pause':19,'break':19,'insert':45,'home':36,'delete':46,'del':46,'end':35,'pageup':33,
		'page_up':33,'pu':33,'pagedown':34,'page_down':34,'pd':34,

		'left':37,'up':38,'right':39,'down':40,

		'f1':112,'f2':113,'f3':114,'f4':115,'f5':116,'f6':117,'f7':118,'f8':119,
		'f9':120,'f10':121,'f11':122,'f12':123,
		
		//Alphabets
		'a':65,'b':66,'c':67,'d':68,'e':69,'f':70,'g':71,'h':72,'i':73,
		'j':74,'k':75,'l':76,'m':77,'n':78,'o':79,'p':80,'q':81,
		'r':82,'s':83,'t':84,'u':85,'v':86,'w':87,'x':88,'y':89,'z':90,
		
		//Numbers
		'0':48,'1':49,'2':50,'3':51,'4':52,'5':53,'6':54,'7':55,'8':56,'9':57,
		
		//The 'other' keys
		';':59,'-':109,'`':192,'[':219,']':221,'/':191,'=':61,"\'":222,',':188,'.':190,"\\":220
	},
	
	'add': function(shortcut_combination,callback,opt) {
		//Provide a set of default options
		var default_options = {
			'type':'keydown',
			'propagate':false,
			'disable_in_input':false,
			'target':document,
			'keycode':false
		}
		
		if(!opt) opt = default_options;
		else {
			for(var dfo in default_options) {
				if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
			}
		}

		var ele = opt.target
		if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
		
		var ths = this;
		shortcut_combination = shortcut_combination.toLowerCase();
		shortcut_combination = shortcut_combination.replace(/(^|\+)\+($|\+)/, "$1shift+=$2");//We cannot use the + char as it is - it must be specified as shift+=
		
		this.all_shortcuts[shortcut_combination] = {
			'callback':callback,
			'target':ele,
			'options': opt
		};
		
		var ths = this; // The closure trick
		
		if(!this.event_attached) { //We need to attach the event just 1 time.
			//Attach the function with the with 3 different events
			this._addEvent(ele,function(e){return ths._pressed.call(ths, e);}, 'keyup');
			this._addEvent(ele,function(e){return ths._pressed.call(ths, e);}, 'keydown');
			this._addEvent(ele,function(e){return ths._pressed.call(ths, e);}, 'keypress');
		}
		this.event_attached = true;
	},

	//Remove the shortcut - just specify the shortcut and I will remove the binding
	'remove':function(shortcut_combination) {
		shortcut_combination = shortcut_combination.toLowerCase();
		shortcut_combination = shortcut_combination.replace(/(^|\+)\+($|\+)/, "$1shift+=$2");
		
		var binding = this.all_shortcuts[shortcut_combination];
		if(!binding) return;

		delete(this.all_shortcuts[shortcut_combination]);
	},
	
	/// This function will be called every time a key is pressed - this is the IMPORTANT function...
	"_pressed": function(e) {
		e = e || window.event;

		var modifiers = { 
			shift: { wanted:false, pressed:false},
			ctrl : { wanted:false, pressed:false},
			alt  : { wanted:false, pressed:false},
			meta : { wanted:false, pressed:false}	//Meta is Mac specific
		};

		if(e.ctrlKey)	modifiers.ctrl.pressed = true;
		if(e.shiftKey)	modifiers.shift.pressed = true;
		if(e.altKey)	modifiers.alt.pressed = true;
		if(e.metaKey)   modifiers.meta.pressed = true;

		//Find Which key is pressed
		var code = 0;
		if (e.keyCode) code = e.keyCode;
		else if (e.which) code = e.which;
		
		//Find target of the event - for disable_in_input
		var element;
		if(e.target) element=e.target;
		else if(e.srcElement) element=e.srcElement;
		if(element.nodeType==3) element=element.parentNode;
		
		//* :DEBUG: */ var timer_title = " For " + code;
		//* :DEBUG: */ if(window.console) console.time(timer_title); 
		for(var shortcut_combination in this.all_shortcuts) {
			var shortcut_details = this.all_shortcuts[shortcut_combination];
			if(typeof(shortcut_details) != 'object') continue;//Or the prototype library will make a mess.
			if(!shortcut_details['options']) continue;
			
			var opt = shortcut_details['options'];
			var callback = shortcut_details['callback'];
			
			if(opt['type'] != e.type) continue;

			if(opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
				if(element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') continue;
			}

			//Remove the + at the beginning and the end.
			shortcut_combination = shortcut_combination.replace(/^\++/,'');
			shortcut_combination = shortcut_combination.replace(/\++$/,'');

			var needed_keys = shortcut_combination.split("+");
			//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
			var kp = 0;
			
			modifiers.ctrl.wanted	= false;
			modifiers.shift.wanted	= false;
			modifiers.alt.wanted	= false;
			modifiers.meta.wanted	= false;

			for(var i=0; k=needed_keys[i],i<needed_keys.length; i++) {
				//Modifiers
				if(k == 'ctrl' || k == 'control') {//Control is needed
					if(!modifiers.ctrl.pressed) continue; //Is it pressed? If not, don't continue
					kp++;
					modifiers.ctrl.wanted = true;

				} else if(k == 'shift') {
					if(!modifiers.shift.pressed) continue;
					kp++;
					modifiers.shift.wanted = true;

				} else if(k == 'alt') {
					if(!modifiers.alt.pressed) continue;
					kp++;
					modifiers.alt.wanted = true;

				} else if(k == 'meta') {
					if(!modifiers.meta.pressed) continue;
					kp++;
					modifiers.meta.wanted = true;

				//Keys
				} else if(opt['keycode']) {
					if(opt['keycode'] == code) kp++;

				} else if(this.key_map[k] == code) {//Find the pressed key using all the key codes
					kp++;
				
				} else if(this.shift_keys[k] == code && e.shiftKey) { //Stupid Shift key bug created by using lowercase
					kp++;
					modifiers.shift.wanted = true; //If the key is in the shift_keys map, shift is required - even if it is not explicitly specified.

				} else if(!this.key_map[k]) { //Nothing in the keys map matched - Might be a foreign language key.
					var character = String.fromCharCode(code);
					if(character == k) kp++;
				}
			}
			
			if(kp == needed_keys.length && 
						modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
						modifiers.shift.pressed == modifiers.shift.wanted &&
						modifiers.alt.pressed == modifiers.alt.wanted &&
						modifiers.meta.pressed == modifiers.meta.wanted) {
				callback(e);
				//* :DEBUG: */ if(window.console) console.timeEnd(timer_title);

				if(!opt['propagate']) { //Stop the event
					//e.cancelBubble is supported by IE - this will kill the bubbling process.
					e.cancelBubble = true;
					e.returnValue = false;
	
					//e.stopPropagation works in Firefox.
					if (e.stopPropagation) {
						e.stopPropagation();
						e.preventDefault();
					}
					return false;
				}
				return true;
			}
		}
		//* :DEBUG: */ if(window.console) console.timeEnd(timer_title);
	},

	//The add event function
	'_addEvent':function(ele,func, type) {
		if(ele.addEventListener) return ele.addEventListener(type, func, false);
		else if(ele.attachEvent) return ele.attachEvent('on'+type, func);
		else ele['on'+type] = func;
	}
}