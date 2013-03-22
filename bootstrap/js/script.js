/* Configuration defined in global scope */
var config = {};
config.home= "//ddiary.loc/"
config.popup = config.home + 'ajax/showpopup';
config.add_event = config.home + 'ajax/addevent';
config.events = config.home +'ajax/events';
config.edit = config.home + 'ajax/editevent';
config.changedate = config.home + 'ajax/changedate';
config.remove = config.home + 'ajax/delete';
config.done = config.home+ 'ajax/done';
config.not_done = config.home+ 'ajax/not_done';
config.loc = window.location.pathname.split( '/' );
if(config.loc.length > 3) {
    config.add_event += '/' + config.loc[3] + '/' +config.loc[4];
    config.events += '/' + config.loc[3] + '/' +config.loc[4];
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
    $('div.cal-cell').popover({
        trigger: 'manual' ,
        html: true,			
        content: function() {
            return $('div.temp').data('popup');
        } 
    });	
			
    //Enable alerts		
    $(".alert").alert();
	
	//Show opened tab after refresh
	show_active_tab();
	
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
        var description = $('div.area').text();
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
                        $('.popover-add').response(ui_lang['cal_added'], true); 
						$("p.event-info").remove();
                        //Add element with javascript
                        add_event(data.id, time, event, description);
                        // Sort elements by time
                        $("div#event-conteiner > div").tsort("span.time");
						$('.close-popover').trigger('click');
                    } else {
                        $('.popover-add').response(data.result, false);	
                    }
                },
                fail: function() {
                    $('.popover-add').response(ui_lang['server_side_error'], false); 
                }
            });
        } else {
            $('.popover-add').response(ui_lang['cal_no_values'], false); 
        }
    });
	
    //Show hide event description
    $('button.show-desc').live('click', function() {
        $(this).parent('.description').toggleClass('closed');
        $(this).children('i').toggleClass('icon-chevron-down');
        $(this).children('i').toggleClass('icon-chevron-up');
    });
	
    //Show or hide calendar
	$(window).resize(showCalendarToggle);

	$(document).ajaxComplete(function() {
		showCalendarToggle();
	});
	
	function showCalendarToggle() {
		if (hasScrollBar()) { 
			$('button.toggle-cal').show();
		} else {
			$('button.toggle-cal').hide();
		}
	}
	
    $('button.toggle-cal').live('click', function() {
        $('tbody#cal-body').toggle();
        $(this).children('i').toggleClass('icon-chevron-up');
        $(this).children('i').toggleClass('icon-chevron-down');
		hide_cal_body('tbody#cal-body');
    });
	
    // Edit event
    $('button.edit-event').live('click', function() {
        // Remake the button to save
        $(this).removeClass('edit-event btn-primary');
        $(this).addClass('btn-success save-event');
        $(this).html(ui_lang['save']);
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
                data: {
                    id: id
                },
                success: function(data) { 
                    if( data == 'deleted' ) {
                        event.parent().response(ui_lang['cal_evt_deleted'], true);
                        change_count("-");
                        event.remove(); 
                    }
                    else {
                        event.response(ui_lang['cal_error_del'], false);
                    }
                },
                fail: function() {
                    alert(ui_lang['fail']);
                }
            });
        } else {
            alert(ui_lang['finish_editing']);
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
                data: {
                    id: id
                },
                success: function(data) { 
                    if( data == 'done' ) {
                        event.find('h3.event-name').addClass('event-done');
                        event.find('a.done-event').addClass('hide');
						event.find('a.not-done-event').removeClass('hide');
                    }
                    else {
                        event.response(data, false);
                    }
                },
                fail: function() {
                    alert(ui_lang['fail']);
                }
            });
        } else {
            alert(ui_lang['finish_editing']);
        }
    });
	
	// Mark done event back to not done
    $('a.not-done-event').live('click', function() {
        var event = $(this).parents(':eq(4)');
        var id = event.find('button.edit-event').attr('id');
        if(id != undefined) {
            $.ajax({
                type: 'POST',
                url: config.not_done,
                data: {
                    id: id
                },
                success: function(data) { 
                    if( data == 'done' ) {
                        event.find('h3.event-name').removeClass('event-done');
                        event.find('a.done-event').removeClass('hide');
						event.find('a.not-done-event').addClass('hide');
                    }
                    else {
                        event.response(data, false);
                    }
                },
                fail: function() {
                    alert(ui_lang['fail']);
                }
            });
        } else {
            alert(ui_lang['finish_editing']);
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
                            event.response(ui_lang['cal_couldnt_save'], false);
                            //Disable editing of contents - not to confuse the user
                            time_el.attr('contentEditable', 'false');
                            event_el.attr('contentEditable', 'false');
                            desc_el.attr('contentEditable', 'false');
                            // Remake the button to save
                            pointer.removeClass('save-event btn-success');
                            pointer.addClass('btn-primary edit-event');
                            pointer.html(ui_lang['edit']);
                            break;
                        case "reserved":
                            event.response(ui_lang['cal_time_reserved']);
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
                            pointer.html(ui_lang['edit']);
                            // Sort elements by time
                            $("div#event-container > div").tsort("span.time"); 
                    }		
                },
                fail: function() {
                    event.response(ui_lang['fail'], false);
                    // Disable editing of cotnents
                    time_el.attr('contentEditable', 'false');
                    event_el.attr('contentEditable', 'false');
                    desc_el.attr('contentEditable', 'false');
                }
            });
        } else {
            alert(ui_lang['no_values']);
        }
        desc_button();
    });
	
	//Add datepicker
	$(document).on('hover', '.change-event', function(){
		$(this).datepicker('show');
		$(document).on('hover', '.delete-event', function(){
			$('.change-event').datepicker('hide');
		});
		$(document).on('hover', '.done-event', function(){
			$('.change-event').datepicker('hide');
		});
		$(this).datepicker()
			.on('changeDate', function(ev){
				var event = $(this).parents(':eq(4)');
				var id = event.find('button.edit-event').attr('id');
				var newdate = getFormatedDate(ev.date);
				if(id != undefined) {
					if( ev.date.getTime() == getSelDate().getTime()) {
						return false;
					}
					$.ajax({
						type: 'POST',
						url: config.changedate,
						data: {
							id: id,
							newdate: newdate
						},
						success: function(data) { 
							if( data == 'changed' ) {
								event.parent().response(ui_lang['cal_date_changed'] + newdate , true);
								change_count("-");
								find_increment(ev.date.getDate());
								event.remove(); 
							}
							else {
								event.response(ui_lang['cal_error_change'], false);
							}
						},
						fail: function() {
							alert(ui_lang['fail']);
						}
					});
				} else {
					alert(ui_lang['finish_editing']);
				}
				$(this).datepicker('hide');
				ev.stopPropogation();
			});
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
	
    //Add event with in main
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
                        $('div#event-container').response(ui_lang['cal_added'], true); 
                    } else {
                        $('.popover-add').response(data.result, false);	
                    }
                },
                fail: function() {
                    $('.popover-add').response(ui_lang['server_side_error'], false); 
                }
            });
        } else {
            $('.popover-add').response(ui_lang['no_values'], false); 
        }
    });
	
	//Listening for changes in forms
	$("form :input").change(function() {
		$(this).closest('form').data('changed', true);
	});
	
	//Saving opened tabs
	$("ul.nav-tabs li a").click(function() {
		$.totalStorage('opened_tab', $(this).attr('href'));
	});
	
	//Submit user settings with validation
	$('#userSettings').click(function(e) {
		e.preventDefault();
		var form = $('#user_settings');
		if($(this).closest('form').data('changed')) {
			var empty = false;
			$.each(form.find('input'),
			function() { 
				if(this.value == "" || this.value == undefined)
					empty = true;
					return;
			});
			if(!empty) {
				var form = $('#user_settings');
				form.attr('action', config.home + 'settings/process_user');
				form.submit();
			} else {
				$('#user_settings').response(ui_lang['no_values'], false);
			}		
		} else {
			$('#user_settings').response(ui_lang['no_change'], false);
		}
	});
	
	$('a.bt_hide').live('click', function(e) {
		e.preventDefault();
		var el = $(this).attr("href");
		$(el).toggle();  
		$(el).next().toggle();
		$(this).children('i').toggleClass('icon-chevron-up');
		$(this).children('i').toggleClass('icon-chevron-down');
		set_hidden(el);
	});
	
	$('#delete_acc_button').click(function() {
	var answer = confirm(ui_lang['delete_acc']);
		if (answer){
			window.location = "http://ddiary.loc/logout/app/delete";
		} else {
			return false;
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
			$(this).focusout(function() {
				if(this.innerHTML == "") {
					this.innerHTML = "0:00";
				}
			});
			
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

//Hide hidden elements
function hide_hidden() {
	var items = $.totalStorage('hidden_sections');
	if($.isArray(items)) {
		for(var i in items) {
			$(items[i]).hide();
			$(items[i]).next().hide();
			$('a[href$='+ items[i] +']').children('i').attr('class', 'icon-chevron-down');
		}
	}
}	
	
//Saves hidden elements to hide them after page reload
function set_hidden(el) {
	var items = [];
	var items = $.totalStorage('hidden_sections');
	if($.isArray(items)) {
		if($.inArray(el, items) == -1) {
			items.push(el);
		} else {
			var index = items.indexOf(el);
			items.splice(index, 1);
		}
		$.totalStorage('hidden_sections', items);
	} else {
		items = [];
		items.push(el);
		$.totalStorage('hidden_sections', items);
	}
}	
	
// change_count event count
function change_count(action, day) {
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
            events.text(count);
        }
        else {
            $('.cal-active').find('span.events').remove();
            var day = $('.cal-active').find('div.day');
            if(!day.hasClass('bold')) {
                day.addClass('no-events');
            }
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
                el.html('<p>'+ ui_lang['server_side_error']+'</p>');
            }
        });
    } else {
        alert(ui_lang['no_values']);
    }
}		
	
// Hide buttons on short descriptions
function desc_button() {
    $('div.desc-field').each(function() {
        if($(this).height() > 60) {
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
    if(time == "") {
        time ="0:00";
    }
	
	// Maybe use a plugin or write my own?
	var seldate = getFormatedDate(getSelDate());
	
    var item = '<div class="event-item">' +
    '<div class="controlls">' + '<div class="btn-group">' +
    '<button class="btn btn-primary edit-event" id="' + id + '">' + ui_lang['edit'] + '</button>' +
    '<button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">' +
    '<span class="caret"></span></button>' +
    '<ul class="dropdown-menu">' +
    '<li><a href="#" class="done-event">' + ui_lang['done'] + '</a></li>' +
	'<li><a href="#" class="change-event" data-date-format="yyyy-mm-dd" data-date="'+ seldate +'">'+ ui_lang['change_date'] +'</a></li>'+
    '<li class="divider"></li>' +
    '<li><a href="#" class="delete-event">'+ ui_lang['delete'] + '</a></li>' +
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

// Getting the selected date in js
function getSelDate() {
	var day = $('.cal-active').find('div.day').html();
	var month_year = $('table.calendar').attr('id');
	var year = month_year.match(/[0-9]{4}/);
	var month = month_year.match(/[0-9]{2}$/);
	if( day != null )
		return (new Date(year, month - 1, day));
	else
		return (new Date());
}
/* Hiding calendar body - need rewriting */

// Get the date in yyyy-mm-dd
function getFormatedDate(date) {
	var cd = date.getDate();
	var cm = date.getMonth() + 1;
	var cy = date.getFullYear();
	var result = cy + "-" + cm + "-" + cd;
	return result;
}

function hasScrollBar() {
	return $('body').outerHeight() > $(window).height();
}

function find_increment(day) {
	var target = $('#cal-body').find("div.day:textEquals('"+ day +"')");
	target.removeClass('no-events');
	events = target.parent().find('span.events');
	if(events.length > 0) {
		var count = events.text();
        count = parseInt(count);
		count++; 
		events.text(count);
	} else {
		target.parent().append('<span class="events">1</span>');
	}
}

function show_active_tab() {
	var target = $.totalStorage('opened_tab');
	if(target != undefined)
		$('.nav-tabs a[href="' +target+ '"]').tab('show');
	else
		$('.nav-tabs a:first').tab('show');
}

$.expr[':'].textEquals = function(a, i, m) {
    var match = $(a).text().match("^" + m[3] + "$")
    return match && match.length > 0;                                                                                                                                                                                                                                            
}