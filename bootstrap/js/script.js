/* Configuration */
	var config = {};
	config.home= 'http://simplecalendar.pagodabox.com/';
	config.popup = config.home + 'ajax/showpopup';
	config.add_event = 	config.home + 'ajax/addevent';
	config.events = config.home +'ajax/events';
	config.edit = config.home + 'ajax/editevent';
	config.remove = config.home + 'ajax/delete';
	config.done = config.home+ 'ajax/done';
	config.loc = window.location.pathname.split( '/' );
	if(config.loc.length > 2) {
		config.add_event += '/' + config.loc[2] + '/' +config.loc[3];
		config.events += '/' + config.loc[2] + '/' +config.loc[3];
	}	

/* The javascript magic */
$(document).ready(function() {	
	// Load in form html ASYNCH
	$.get(config.popup, function(data) {
			  $('div.temp').data('popup', data);
			});
	// Load the events
	load_events($('#day-events'));
	
	// Register popover		
	$('div.cal-cell').popover({trigger: 'manual' , html: true, 			
			content: function() { return $('div.temp').data('popup'); } 
			});	
			
	//Enable alerts		
	$(".alert").alert();
	
	// Call popover		
	$('div.cal-cell').click(function() {
			// Get the date from DOM
			var day = $(this).find('div.day').html();
			// Remove other popovers
			$(".calendar .cal-cell").not(this).popover('hide');
			// 
			if($(this).hasClass('cal-active')==false) { 
					load_events( $('#day-events'), day ); 
				} else {
					// Toggle popover
					$(this).popover('toggle');
					// Make sure description field is hidden
					$('.popover-content .details').hide();
					// Set focus to first intput and enable typeahead
					ready_time($('input#time'));
				}
			// Remove selected date highligting
			$('.calendar .cal-cell').removeClass('cal-active');
			//Highlight selected date
			$(this).addClass('cal-active');	
      });
	
	// Close popover with x
	$('.close-popover').live('click', function(){
		$('.calendar .cal-cell').popover('hide');
		$('.popover-content .details').hide();
    });
	
	// Close popover with Esc key
	$(document).keyup(function(e) {
	  if (e.keyCode == 27) { 
		  $('.calendar .cal-cell').popover('hide'); 
		  $('.popover-content .details').hide();
	  } 
	});
	
	// Show/hide description
	$('.show-details').live('click', function(e) {
		e.preventDefault();
		$('.popover-content .details').toggle();
		// Append wysiwyg only once
		if ($("div.wysiwyg").length == 0){
			// Instantiate wysiwyg
			var wysiwyg  = new Wysiwyg;	
			$('.area').before(wysiwyg.el);
		}
		$(this).children('i').toggleClass('icon-chevron-down');
		$(this).children('i').toggleClass('icon-chevron-up');
	});
	
	//Add event with in popover
	$('#add-event').live('click', function(e) {
		e.preventDefault();
		var day = $('div.cal-active').find('.day').html();
		var time = $('input.time_p').val();
		var event = $('input.event_p').val();
		var description = $('div.area')[0].innerHTML;
		if(day != "" & event != "") {
			$.ajax({
				type: 'POST',
				url: config.add_event,
				dataType: "json",
				data: {
					day: day,
					time: time,
					event: event,
					description: description
				},
				success: function(data) { 
					if(data.result == 'Added') { 
							change_count("+"); 
							$('.popover-add').response(data.result, true); 
							//Add element with javascript
							add_event(data.id, time, event, description);
							// Sort elements by time
							$("div#event-conteiner > div").tsort("span.time");
						} else {
							$('.popover-add').response(data.result, false);	
						}
				},
				fail: function() {
					$('.popover-add').response("Server side error", false); 
				}
			});
		} else {
			alert('No values provided');
		}
	});
	
	//Show hide event description
	$('button.show-desc').live('click', function() {
		$(this).parent('.description').toggleClass('closed');
		$(this).children('i').toggleClass('icon-chevron-down');
		$(this).children('i').toggleClass('icon-chevron-up');
	});
	
	//Show hide calendar
	$('button.toggle-cal').live('click', function() {
		$('tbody.cal-body').toggle();
		$(this).children('i').toggleClass('icon-chevron-up');
		$(this).children('i').toggleClass('icon-chevron-down');
	});
	
	// Edit event
	$('button.edit-event').live('click', function() {
		// Remake the button to save
		$(this).removeClass('edit-event btn-primary');
		$(this).addClass('btn-success save-event');
		$(this).html('Save');
		// Enable editing of contents
		var event = $(this).parents(':eq(2)');
		event.find('span.time').attr('contentEditable', 'true').forcetime();
		event.find('h3.event-name').attr('contentEditable', 'true');
		// Handle the description - fuzzy  :/
		var desc = event.find('div.desc-field');
		if(desc.length > 0 ) {
			event.find('div.description').removeClass('closed');
			desc.attr('contentEditable', 'true');
			if ($("div.wysiwyg").length == 0){
				var wysiwyg  = new Wysiwyg;	
				desc.before(wysiwyg.el);
			}
		} else {
			event.append('<div class="description"><div class="desc-field" contentEditable="true"></div></div>');
			if ($("div.wysiwyg").length == 0){
				var wysiwyg  = new Wysiwyg;	
				$("div.desc-field").before(wysiwyg.el);
			}
		}
	});
	
	// Delete event
	$('a.delete-event').live('click', function() {
		var event = $(this).parents(':eq(4)');
		var id = event.find('button.edit-event').attr('id');
		if(id != undefined) {
			$.ajax({
				type: 'POST',
				url: config.remove,
				data: { id: id },
				success: function(data) { 
					if( data == 'deleted' ) {
					event.parent().response("Event deleted", true);
					change_count("-");
					event.remove(); 
					}
					else {
						event.response(data, false);
					}
				},
				fail: function() {
					alert("Failed!");
				}
			});
		} else {
			alert('No event ID provided');
		}
	});
	
	// Mark event as done
	$('a.done-event').live('click', function() {
		var event = $(this).parents(':eq(4)');
		var id = event.find('button.edit-event').attr('id');
		if(id != undefined) {
			$.ajax({
				type: 'POST',
				url: config.done,
				data: { id: id },
				success: function(data) { 
					if( data == 'done' ) {
					event.find('h3.event-name').addClass('event-done');
					event.find('a.done-event').remove();
					event.find('.divider').remove();
					}
					else {
						event.response(data, false);
					}
				},
				fail: function() {
					alert("Failed!");
				}
			});
		} else {
			alert('No event ID provided');
		}
	});
	
	
	// Save event
	$('button.save-event').live('click', function() {
		// Get the DOM elements
		var event = $(this).parents(':eq(2)');
		time_el = event.find('span.time');
		event_el = event.find('h3.event-name');
		desc_el = event.find('div.desc-field');
		// Get values and call ajax and execute update
		var id = $(this).attr('id');
		var day = $('h2#active-date').html().slice(0, - 1);
		var time = time_el.html();
		var data = event_el.html();
		var description = desc_el.html();
		// Make $(this) available through a variable
		var pointer = $(this);
		if(day != "" & data != "") {
			$.ajax({
				type: 'POST',
				url: config.edit,
				data: {
					id : id,
					day: day,
					time: time,
					data: data,
					description: description
				},
				success: function(data) { 
					switch(data) {
						case "error":
							event.response("Changes couldn't be saved!", false);
							//Disable editing of contents - not to confuse the user
							time_el.attr('contentEditable', 'false');
							event_el.attr('contentEditable', 'false');
							desc_el.attr('contentEditable', 'false');
							// Remake the button to save
							pointer.removeClass('save-event btn-success');
							pointer.addClass('btn-primary edit-event');
							pointer.html('Edit');
							break;
						case "reserved":
							event.response("Time already reserved!");
							break;
						default:
							//On success disable editing of contents
							time_el.attr('contentEditable', 'false');
							event_el.attr('contentEditable', 'false');
							desc_el.attr('contentEditable', 'false');
							// Remove wysiwyg
							$("div.wysiwyg").remove();
							// Remake the button to save
							pointer.removeClass('save-event btn-success');
							pointer.addClass('btn-primary edit-event');
							pointer.html('Edit');
							// Sort elements by time
							$("div#event-container > div").tsort("span.time"); 
					}		
				},
				fail: function() {
					event.response("Error", false);
					// Disable editing of cotnents
					time_el.attr('contentEditable', 'false');
					event_el.attr('contentEditable', 'false');
					desc_el.attr('contentEditable', 'false');
				}
			});
		} else {
			alert('NO values');
		}
		desc_button();
	});
	
	// Show the main adding form
	$('#show-add').live('click', function() {
		$('#event-container').hide();
		$(".calendar .cal-cell").popover('hide');
		$('#add-section').fadeIn('fast');
		// Instantiate wysiwyg only once
		if ($("div.wysiwyg").length == 0){
			var wysiwyg  = new Wysiwyg;	
			$('.area').before(wysiwyg.el);
		}
		$(this).hide();
		ready_time($('input#time'));
	});
	
	$('#hide-add').live('click', function(e) {
		e.preventDefault();
		$("div.wysiwyg").remove();
		$('#add-section').hide();
		$('#show-add').fadeIn('fast');
		$('#event-container').fadeIn('fast');
	});
	
	//Add event with in popover
	$('#main-add').live('click', function(e) {
		e.preventDefault();
		var day = $('h2#active-date').html().slice(0, - 1);
		var time = $('#time').val();
		var event = $('#event').val();
		var description = $('div.area')[0].innerHTML;
		if(day != "" & event != "") {
			$.ajax({
				type: 'POST',
				url: config.add_event,
				dataType: "json",
				data: {
					day: day,
					time: time,
					event: event,
					description: description
				},
				success: function(data) { 
					if(data.result == 'Added') { 
							change_count("+"); 
							//Add element with javascript
							add_event(data.id, time, event, description);
							// Sort elements by time
							$("p.event-info").remove();
							$("div#event-container > div").tsort("span.time");
							$('#hide-add').trigger('click');
							$('div#event-container').response(data.result, true); 
						} else {
							$('.popover-add').response(data.result, false);	
						}
				},
				fail: function() {
					$('.popover-add').response("Server side error", false); 
				}
			});
		} else {
			alert('No values provided');
		}
	});
// End document.ready
});

/* Functions */
	
//Time input mask
jQuery.fn.forcetime =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and semicolons ONLY
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
				key == 186 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

// change_count event count
function change_count(action) {
			//Increase event count asynch
			var events = $('.cal-active').find('span.events');
				if(events.length > 0) {
					var count = events.text();
					count = parseInt(count);
					if( action == "-" ) {
						count--;
					} else { 
						count++; 
					}
					if( count > 0 ) {
						events.text(count); }
					else {
						$('.cal-active').find('span.events').remove();
						$('.cal-active').find('div.day').addClass('no-events');
					}
				} else {
					$('.cal-active').find('div.day').removeClass('no-events');
					$('.cal-active').append('<span class="events">1</span>');
				}
}

// Response function
jQuery.fn.response =
function(data, success) {
	//Set alert type
	var type = 'alert-error';
	if(success == true) {
		type = 'alert-success'; 
		}
	//Append html
	this.prepend(
		'<div class="alert ' + type + '">' + data 
			+ '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');
	setTimeout("$('.alert').alert('close');", 5000);				
	}				

// Load events
	function load_events(el, day) {
		// If the day is not given get it from DOM
		if(day == null) {
			day= $('div.cal-active').find('div.day').html();
		}
		// Load in day event info
		$("#day-events").html('<div class="progress progress-striped active"><div class="bar"></div></div>');
		if(day != "") {
			$.ajax({
				type: 'POST',
				url: config.events,
				data: {
					day: day
				},
				success: function(data) { 
					el.html(data); 
					desc_button(); 
				},
				fail: function() {
					el.html('<p>Server side error</p>');
				}
			});
		} else {
			alert('NO values');
		}
	}		
	
// Hide buttons on short descriptions
	function desc_button() {
		$('div.desc-field').each(function() {
			if($(this).height() > 58) {
				if($(this).parent().find('button.show-desc').length == 0 ) {
					$(this).before('<button class="btn show-desc" title="details.."><i class="icon-chevron-down"></i></button>');
				}	
			} else {
				$(this).parent().find('button.show-desc').remove();
			}
			if($(this).text() == '') {
				$(this).parent().remove();
				$(this).remove();
			}
		});
	}
	
	function add_event(id, time, event, description) {
		var item = '<div class="event-item">' +
			'<div class="controlls">' + '<div class="btn-group">' +
			'<button class="btn btn-primary edit-event" id="' + id + '">Edit</button>' +
			'<button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">' +
			'<span class="caret"></span></button>' +
			'<ul class="dropdown-menu">' +
				'<li><a href="#" class="done-event">Done</a></li>' +
				'<li class="divider"></li>' +
				'<li><a href="#" class="delete-event">Delete</a></li>' +
			'</ul></div></div>';
		item+= '<span class="time">'+ time + '</span>' +
			'<h3 class="event-name">&nbsp;' + event + '</h3>';
		if(description != '') {
			item+='<div class="description closed">' + 
				'<div class="desc-field">' + description +
				'</div></div>';
			}
		item+= '</div>';	
		$('div#event-container').append(item);
	}
	
	function ready_time(el) {
		el.focus().typeahead({
			source:["00:00","1:00","2:00","3:00","4:00","5:00","6:00","7:00","8:00","9:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","1:10","1:20","1:30","1:40","1:50","2:10","2:20","2:30","2:40","2:50","3:10","3:20","3:30","3:40","3:50","4:10","4:20","4:30","4:40","4:50","5:10","5:20","5:30","5:40","5:50","6:10","6:20","6:30","6:40","6:50","7:10","7:20","7:30","7:40","7:50","8:10","8:20","8:30","8:40","8:50","9:10","9:20","9:30","9:40","9:50","10:10","10:20","10:30","10:40","10:50","11:10","11:20","11:30","11:40","11:50","12:10","12:20","12:30","12:40","12:50","13:10","13:20","13:30","13:40","13:50","14:10","14:20","14:30","14:40","14:50","15:10","15:20","15:30","15:40","15:50","16:10","16:20","16:30","16:40","16:50","17:10","17:20","17:30","17:40","17:50","18:10","18:20","18:30","18:40","18:50","19:10","19:20","19:30","19:40","19:50","20:10","20:20","20:30","20:40","20:50","21:10","21:20","21:30","21:40","21:50","22:10","22:20","22:30","22:40","22:50","23:10","23:20","23:30","23:40","23:50"],
			items: '6'
			});	
		el.forcetime();
	}