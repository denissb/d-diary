// Event handlers and GLOBAL events
var loading_queue = 0;

$('#fbLogin').click(function() {
	fb_login();
});

$('#unlink_button').click(function() {
	var answer = confirm(ui_lang['facebook_unlink']);
	if (answer){
		window.location = "http://ddiary.loc/logout/app/unlink";
	} else {
		return false;
	}
});

$('#addWidgets').click(function() {
	if($('#widget_settings').data('changed')) {
		var form = $('#widget_settings');
		form.attr('action', config.home + 'settings/process_widgets');
		form.submit();
	} else {
		$('#widget_settings').response(ui_lang['no_change'], false);
	}
});

$('#sendRequests').click(function() {
	sendRequestToFriends();
});
  
// Params requires a list of parameters e.g 'email,user_likes' 
function fb_login(params) {
	FB.login(function(response) {
		console.log("Logging you in..");
	}, {scope: params});
}

//Remove loading element - this solution is temporary	
function removeLoading() {
	$("div.progress").remove();
	$("div.part_one").show();
}

//Remove loading element
function setLoading() {
	var elem = $("div.part_one");
	elem.hide();
	elem.before('<div class="progress progress-striped active"><div class="bar"></div></div>');
	$('div.part_one').on('data_fetch', function(event) {
	  if(window.loading_queue <= 1) {
		removeLoading();
	  } else {
		window.loading_queue--;
	  }
	});
}

//Checks if the user is logged in and retrives required information depending on the settings
FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
		window.loading_queue = Object.keys(window.active_settings).length;
		setLoading();
		getFriendsData(function() {
			Plugins.FriendsBirthdays.initBirthdays("div.part_one");
			Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
			Plugins.FriendsBirthdays.displayBirthdays(3);
			Plugins.FriendsBirthdays.addEventHandlers();
			$('div.part_one').trigger('data_fetch');
		});
		getEventsData(function() {
			Plugins.FriendsEvents.initEvents("div.part_one");
			Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "after");
			Plugins.FriendsEvents.displayEvents(3);
			Plugins.FriendsEvents.addEventHandlers();
			$('div.part_one').trigger('data_fetch');
		});
		getPhotosData(function() {
			Plugins.DayPhotos.initPhotos("div.part_two");
			Plugins.DayPhotos.getOrderedPhotos(getSelDate(), "before");
			Plugins.DayPhotos.displayPhotos(3);
			Plugins.DayPhotos.addEventHandlers();
			$('div.part_one').trigger('data_fetch');
		});
		
		if (window.active_settings.hasOwnProperty('publish_stream')) {
			Plugins.PublishNotes.initNotes();
			$('div.part_one').trigger('data_fetch'); //trigering data fetch to notify that all plugins are loaded
		}
		
		hide_hidden(); // Hiding hidden parts
	} else if (response.status == 'not_authorized') {
		// the user is logged in to Facebook, 
		// but has not authenticated this app
	} else {
			// the user isn't logged in to Facebook.
		}
	});		

function sendRequestToFriends() {
    FB.ui({
        method: 'apprequests',
        message: ui_lang['app_advert']
    });     
}

//Adding a div element to the DOM
jQuery.fn.addDiv =
    function(css_class, content, css_id) {
        $("<div/>", {
		  "class": css_class,
		  "id": css_id,
		  text: content
		}).appendTo(this);
    }

Date.prototype.getMonthName = function() {
    var monthNames = ui_lang['month_names'];
    return monthNames[this.getMonth()];
}

// Retreving friends data - based on totalStorage plugin
function getFriendsData(callback) {
	if (window.active_settings.hasOwnProperty('friends_birthday')) {
		var timenow = new Date().getTime();
		var recived = (timenow - $.totalStorage('recived_friends'))/1000;
		if(!$.totalStorage('friends_data') || recived > 3600 || $.totalStorage('friends_data').error) {
			FB.api('/me/friends', {fields: 'name,id,location,birthday,picture'}, function(response) {
				$.totalStorage('friends_data', response.data);
				$.totalStorage('recived_friends', timenow);
				callback();
			});
		} else {
			callback();
		}
	}
}

// Retreving events data
function getEventsData(callback) {
	if (window.active_settings.hasOwnProperty('friends_events')) {
		var timenow = new Date().getTime();
		var recived = (timenow - $.totalStorage('recived_events'))/1000;
		if(!$.totalStorage('events_data') || $.totalStorage('events_data').error || recived > 3600) {
			FB.api(
			{
				method: "fql.query",
				query: "SELECT eid, name, start_time, end_time, location, pic_small FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()))"
			},
			function(response) {
				$.totalStorage('events_data', response);
				$.totalStorage('recived_events', timenow);
				callback();
			});
		} else {
			callback();
		}
	}	
}

function getPhotosData(callback) {
	if (window.active_settings.hasOwnProperty('user_photos')) {
		var timenow = new Date().getTime();
		var recived = (timenow - $.totalStorage('recived_photos'))/1000;
		if(!$.totalStorage('photos_data') || $.totalStorage('photos_data').error || recived > 360) {
			FB.api(
			{
				method: "fql.query",
				query: "SELECT created, src, src_big, link, caption FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner=me()) OR pid IN (SELECT pid FROM photo_tag WHERE subject = me())"
			},
			function(response) {
				$.totalStorage('photos_data', response);
				$.totalStorage('recived_photos', timenow);
				callback();
			});
		} else {
			callback();
		}
	}	
}

var Plugins = {}; //namespace

/* Friends birthdays plugin */

Plugins.FriendsBirthdays = (function($){ 
	
	var orderedBirthdays =[];
	
    return {
        //Public members
		initBirthdays: function(parent) {
			$(parent).addDiv("span6","","friends_birthdays");
			$("div#friends_birthdays").addDiv("well");
			$("div#friends_birthdays").children(".well").first()
				.html('<h3>'+ ui_lang['birthdays']+ '</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_birthdays" title="hide"><i class="icon-chevron-up"></i></a>'+
				'<a class="btn btn-mini" id="bd_before" href="javascript:void(0)">' + ui_lang['past'] + '</a>' +
				'<a class="btn btn-mini disabled" id="bd_after" href="javascript:void(0)">' + ui_lang['upcoming']+ '</a></div>');
			$("div#friends_birthdays").children(".well").first().addDiv("","","result_birthdays");	
			$("div#friends_birthdays").children(".well").append('<div class="plugin_buttons bottom">' +
			'<a class="btn btn-mini" id="bd_less" href="javascript:void(0)"><i class="icon-chevron-up"></i></a>' +
			'<a class="btn btn-mini" id="bd_more" href="javascript:void(0)">' + ui_lang['show_more'] + '</a></div><div class="clearfix"></div>');
		},
		getOrderedBirthdays: function(date, condition) {
			var birthdays = $.totalStorage('friends_data');
			var now = (date) ? date : new Date();
			now.setHours(0,0,0,0);
			var result = [];
			var lap = [];
			for (i in birthdays)
			{
				if(birthdays[i].birthday != undefined)
				{
					var date = fbBirthdayToJs(birthdays[i].birthday, now);
					// Adding the date to the array for further modification
					birthdays[i].birthday_date = date;
					// Filling up the arrays depending on the requirements
					if(condition == "after")
					{
						if(date >= now) 
						{
							if(date.getTime() == now.getTime())
								birthdays[i].has_bd = true;
							result.push(birthdays[i]);
						} else {
							lap.push(birthdays[i]);
						}
					} else {
						if(date < now)
						{
							if(date.getTime() == now.getTime())
								birthdays[i].has_bd = true;
							result.push(birthdays[i]);
						} else {
							lap.push(birthdays[i]);
						}	
					}
				}
			}
			// Sorting the arrays based on condition
			if(condition == "after")
			{
				var sorting = function(a, b) { return a.birthday_date - b.birthday_date; }
				result = result.sort(sorting);
				lap = lap.sort(sorting);
				result = result.concat(lap);
			} else {
				var sorting = function(a, b) { return b.birthday_date - a.birthday_date; }
				result = result.sort(sorting);
				lap = lap.sort(sorting);
				result = result.concat(lap);
			}
			orderedBirthdays = result;
		},
		displayBirthdays: function(length) {
			var result = orderedBirthdays.splice(0, length);
			var output = "";
			var icon = "";
			for(var i in result)
				{
				if(result[i].hasOwnProperty('has_bd')) {
					output +="<div class='birthday is_today'>";
					icon ="<i class='icon-gift'></i>";
				} else {
					output +="<div class='birthday'>";
					icon="";
				}
				output +="<div class='img_holder'><a href='https://facebook.com/" + result[i].id + "' title='"+ result[i].name +"' target='_blank'><img src='"+ result[i].picture.data.url +"'/></a></div>";
				output +="<span class='name'>"+ result[i].name + icon +"</span>";
				output +="<span class='date'>"+ result[i].birthday_date.getMonthName() + " " + result[i].birthday_date.getDate() + "</span>";
				output +="</div>";
				}
			$("div#result_birthdays").append(output);
		},
		addEventHandlers: function() {
			// Event handlers
			$('div.cal-cell').click(function() {
				$("div#result_birthdays").empty();
				Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
				Plugins.FriendsBirthdays.displayBirthdays(3);
				$("#bd_after").addClass('disabled');
				$("#bd_before").removeClass('disabled');
			});
		
			$('#bd_more').on('click', function() {
				Plugins.FriendsBirthdays.displayBirthdays(3);
				$('#bd_less').show();
			});
			
			$('#bd_less').on('click', function() {
				$('#bd_after').trigger('click');
				$(this).hide();
			});
			
			$('#bd_after').on('click', function() {
				$("div#result_birthdays").empty();
				Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
				Plugins.FriendsBirthdays.displayBirthdays(3);
				$(this).addClass('disabled');
				$("#bd_before").removeClass('disabled');
			});
			
			$('#bd_before').on('click', function() {
				$("div#result_birthdays").empty();
				Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "before");
				Plugins.FriendsBirthdays.displayBirthdays(3);
				$(this).addClass('disabled');
				$("#bd_after").removeClass('disabled');
			});
		}
	}
	
	function fbBirthdayToJs(date, now) {
		date = date.toString();
		// Getting the month and date of birthday
		var clean = date.match(/[0-9]{2}/g);
		var month = clean[0];
		var day = clean[1];
		return new Date(now.getFullYear(), month - 1, day);
	}
})(jQuery);

Plugins.FriendsEvents = (function($) {
	
	var orderedEvents = [];
	var events = [];
	
	//Helpers
	function showPeriod(start, end) {
		var showEndDate = (end.getDate() != start.getDate() && end.getTime() != 0) ? 
			" - " + end.getMonthName() + " " + end.getDate() + " " + end.getHours() + ":" + showMins(end.getMinutes()) : "";
		return start.getMonthName()+ " " + start.getDate() + " " + start.getHours() + ":" + showMins(start.getMinutes())
				 + showEndDate;
		function showMins(min) {
			return (min < 10) ? '0' + min : min
		}		
	}
	
	function showEvents(result) {
		var output = "";
		var icon = "";
		for(var i in result)
			{
			if(result[i].hasOwnProperty('is_today')) {
				output +="<div class='event is_today'>";
				icon ="<i class='icon-bell'></i>";
			} else {
				output +="<div class='event'>";
				icon="";
			}
			output +="<div class='img_holder'><a href='https://facebook.com/" + result[i].eid + "' title='"+ result[i].name +"' target='_blank'><img src='"+ result[i].pic_small +"'/></a></div>";
			output +="<span class='name'>"+ result[i].name + icon +"</span>";
			var startTime = new Date(result[i].start_time);
			var endTime = new Date(result[i].end_time);
			output +="<span class='date'><i class='icon-time'></i>"+ showPeriod(startTime, endTime) +"</span>";
			if(result[i].location)
				output +="<span class='place'><i class='icon-map-marker'></i>"+ result[i].location +"</span>";
			output +="<div class='clearfix'></div></div>";
			}
		$("div#result_events").append(output);
	}
	
	function searchEvents() {
		var result = [];
		var needle = $('#evt_search_field').val().toLowerCase();
		if(needle.length > 2)
			{
			for(var i in events){
				var res_name = events[i].name.toLowerCase().indexOf(needle);
				res_loc = -1;
				if(events[i].location) {
					res_loc = events[i].location.toLowerCase().indexOf(needle);
				}
				if(res_name != -1 || res_loc != -1) {
					result.push(events[i]);
				}
			}
			$("div#result_events").empty();
			$("#evt_before").removeClass('disabled');
			$("#evt_after").removeClass('disabled');
			showEvents(result);
		}
	}
			
	return {
	//Public methods
		initEvents : function(parent) {
			$(parent).addDiv("span6","","friends_events");
			$("div#friends_events").addDiv("well");
			$("div#friends_events").children(".well").first()
				.html('<h3>' + ui_lang['events'] + '</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_events" title="hide"><i class="icon-chevron-up"></i></a>'+
				'<a class="btn btn-mini" id="evt_search" href="javascript:void(0)"><i class="icon-search"></i></a>' +
				'<a class="btn btn-mini" id="evt_before" href="javascript:void(0)">' + ui_lang['past'] + '</a>' +
				'<a class="btn btn-mini disabled" id="evt_after" href="javascript:void(0)">' + ui_lang['upcoming'] + '</a></div>');
			$("div#friends_events").children(".well").first().addDiv("","","result_events");
			$("div#friends_events").children(".well").append('<div class="plugin_buttons bottom">' +
			'<a class="btn btn-mini" id="evt_less" href="javascript:void(0)"><i class="icon-chevron-up"></i></a>' +
			'<a class="btn btn-mini" id="evt_more" href="javascript:void(0)">' + ui_lang['show_more'] + '</a></div><div class="clearfix"></div>');
		},
		getOrderedEvents: function(date, condition) {
			events = $.totalStorage('events_data');
			var now = (date) ? date : new Date();
			now.setHours(0,0,0,0);
			var result = [];
			var iter = 0; //counter
			for(i in events) {
				if(iter > 500) { break; } else { iter++; }
				var startTime = new Date(events[i].start_time);
				if(condition == "after")
				{
					if(startTime >= now) 
					{
						if(startTime.setHours(0,0,0,0) == now.getTime())
							events[i].is_today = true;
						result.push(events[i]);
					}
				} else {
					if(startTime < now)
					{
						if(startTime.setHours(0,0,0,0) == now.getTime())
							events[i].is_today = true;
						result.push(events[i]);
					}
				}
			}
			//Sorting the evnts
			if(condition == "after")
			{
				result = result.sort(function(a, b) { return new Date(a.start_time)- new Date(b.start_time); });
			} else {
				result = result.sort(function(a, b) { return new Date(b.start_time) - new Date(a.start_time); });
			}
			orderedEvents = result;
		},
		displayEvents: function(length) {
			var result = orderedEvents.splice(0, length);
			showEvents(result);
		},
		addEventHandlers: function() {
			$('div.cal-cell').click(function() {
				$("div#result_events").empty();
				Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "after");
				Plugins.FriendsEvents.displayEvents(3);
				$("#evt_after").addClass('disabled');
				$("#evt_before").removeClass('disabled');
			});

			$('#evt_more').on('click', function() {
				Plugins.FriendsEvents.displayEvents(3);
				$('#evt_less').show();
			});
			
			$('#evt_less').on('click', function() {
				$('#evt_after').trigger('click');
				$(this).hide();
			});

			$('#evt_after').on('click', function() {
				$("div#result_events").empty();
				Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "after");
				Plugins.FriendsEvents.displayEvents(3);
				$(this).addClass('disabled');
				$("#evt_before").removeClass('disabled');
			});

			$('#evt_before').on('click', function() {
				$("div#result_events").empty();
				Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "before");
				Plugins.FriendsEvents.displayEvents(3);
				$(this).addClass('disabled');
				$("#evt_after").removeClass('disabled');
			});
			$('#evt_search').live('click', function(e) {
				e.preventDefault();
				$(this).popover('toggle');
				$('#evt_search_field').focus();
			});
			
			$('#evt_search').popover({
				trigger: 'manual',
				html: true,
				content: function() {
					return '<div class="arrow"></div><div class="popover-inner small">' +
					'<div class="popover-add">' +
					'<div class="input-append"><input class="span2" id="evt_search_field" type="text"><button class="btn" id="evt_find" type="button">' + ui_lang['find'] + '</button>'+
					'</div></div></div>';
				},
				placement: 'bottom'
			});
			
			$('#evt_find').live('click', function() {
				searchEvents();
				$('#evt_search').popover('hide');
			});
			
			$('#evt_search_field').live('keypress', function(e) {
				if(e.which == 13) {
					searchEvents();
					$('#evt_search').popover('hide');
				}
			});
		}
	}
})(jQuery);

Plugins.PublishNotes = (function($) {

	return {
		initNotes: function() {
			$("#day-events").on('data_fetch', function(event) {
				$('div.desc-field').before('<button class="btn add-fb-note" title="'+ ui_lang['fb_add_note'] +'">f</button>');
			});	
			$('.add-fb-note').live('click', function() {
				// Prvoiding parameters
				var params = {};
				var parent = $(this).parents('.event-item');
				params['subject'] = parent.find('h3.event-name').html();
				params['message'] = parent.find('.desc-field').html().trim();
				//Providing date to parameters..
				var date = {}; 
				date['date'] = $('#active-date').html();
				date['month'] = $('.month-name').html();
				date['year'] = $('.year').html();
				params['subject'] = params['subject'].concat(" (",date['date'],date['month'],", ",date['year'],")");
				// Triggering facebooks API call
				console.log(params);
				if(params['subject'])
				{	
					FB.api(
					'/me/notes',
					'post', 
					params,
					function(response) {
						 if (!response || response.error) {
							$('div#event-container').response(ui_lang['fb_note_failed'], false); 
						 } else {
							$('div#event-container').response(ui_lang['fb_note_added'], true); 
						 }
					});
				}
			});
		}
	}
})(jQuery);

Plugins.DayPhotos = (function($) {
	//Instance variables
	var orderedPhotos = [];
	var photos = [];
	
	function initHtml(parent) {
		$(parent).addDiv("span12","","user_photos");
			$("div#user_photos").addDiv("well");
			$("div#user_photos").children(".well").first()
				.html('<h3>' + ui_lang['day_photos'] + '</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_photos" title="hide"><i class="icon-chevron-up"></i></a>' +
				'<a class="btn btn-mini disabled" id="photos_before" href="javascript:void(0)">' + ui_lang['past'] + '</a>' +
				'<a class="btn btn-mini" id="photos_after" href="javascript:void(0)">' + ui_lang['upcoming'] + '</a></div>');
			//The div that will contain results	
			$("div#user_photos").children(".well").first().addDiv("","","result_photos");
			$("div#user_photos").children(".well").append('<div class="plugin_buttons bottom">' +
			'<a class="btn btn-mini" id="photo_less" href="javascript:void(0)"><i class="icon-chevron-up"></i></a>' +
			'<a class="btn btn-mini" id="photo_more" href="javascript:void(0)">' + ui_lang['show_more'] + '</a></div><div class="clearfix"></div>');
			$("#photos_after").hide();
			$("div#user_photos").append("<div id='photoModal' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='accessModalLabel' aria-hidden='true'><div class='modal-header'>" +
				"<h3 id='photoModalHeader'></h3><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>" +
				"</div><div class='modal-body'></div></div>"
			);
	}
	
	function showPhotos(result) {
		var output = "";
		var is_today = "";
		if (result.length == 0) {
			output += "<p>" + "No photos present!" + "</p>";
			$("#photo_more").hide();
		} else {
			for(var i in result) {
				if(result[i].hasOwnProperty('is_today')) {
					is_today = " is_today";
				} else {
					is_today = "";
				}
				output += "<div class='photo'>";
				output += "<div class='img_holder" + is_today + "'><a href='" + result[i].link + "' title='"+ result[i].caption +"' target='_blank'><div class='img_container'><img src='"+ result[i].src +"'/></div></a>";
				output += "<a class='btn btn-mini zoom-in' href='javascript:void(0)' data-image='" + result[i].src_big + "' data-caption='" + result[i].caption+ "'><i class='icon-zoom-in'></i></a></div>";
				output += "</div>";
			}
			$("#photo_more").show();
		}
		$("div#result_photos").append(output);
	}

	return {
		initPhotos: function(parent) {
			initHtml(parent);
		},
		getOrderedPhotos: function(date, condition) {
			photos = $.totalStorage('photos_data');
			var now = (date) ? date : new Date();
			var iter = 0; //counter
			var result = [];
			for (i in photos) {
				var createdTime = new Date(photos[i].created * 1000);
				// setting time to 0 to compate the date by day
				if(createdTime.setHours(0,0,0,0) == now.setHours(0,0,0,0)) {
					photos[i].is_today = true;
				}
				createdTime -= (86400) * 1000; // we need to go one day before
				if (iter > 500) { break; } else { iter++; }
				if (condition == "after") {
					if (createdTime > now) {
						result.push(photos[i]);
					}
				} else {
					if (createdTime < now) {
						result.push(photos[i]);
					}
				}		
			}	
			//Sorting the photos
			if(condition == "after")
			{
				result = result.sort(function(a, b) { return new Date(a.created * 1000)- new Date(b.created * 1000); });
			} else {
				result = result.sort(function(a, b) { return new Date(b.created * 1000) - new Date(a.created * 1000); });
			}
			orderedPhotos = result;
		},
		displayPhotos: function(length) {
			var result = orderedPhotos.splice(0, length);
			showPhotos(result);
		},
		addEventHandlers: function() {
			$('div.cal-cell').click(function() {
				$("div#result_photos").empty();
				Plugins.DayPhotos.getOrderedPhotos(getSelDate(), "before");
				Plugins.DayPhotos.displayPhotos(3);
				$("#photos_before").addClass('disabled');
				$("#photos_after").removeClass('disabled');
				if(getSelDate() < new Date().setHours(0,0,0,0)) {
					$("#photos_after").show();
				} else {
					$("#photos_after").hide();
				}
			});
			
			$('#photo_more').on('click', function() {
				Plugins.DayPhotos.displayPhotos(3);
				$('#photo_less').show();
			});
			
			$('#photo_less').on('click', function() {
				$('#photos_before').trigger('click');
				$(this).hide();
			});
			
			$('#photos_after').on('click', function() {
				$("div#result_photos").empty();
				Plugins.DayPhotos.getOrderedPhotos(getSelDate(), "after");
				Plugins.DayPhotos.displayPhotos(3);
				$(this).addClass('disabled');
				$("#photos_before").removeClass('disabled');
			});

			$('#photos_before').on('click', function() {
				$("div#result_photos").empty();
				Plugins.DayPhotos.getOrderedPhotos(getSelDate(), "before");
				Plugins.DayPhotos.displayPhotos(3);
				$(this).addClass('disabled');
				$("#photos_after").removeClass('disabled');
			});
			
			$('div.img_holder').live({
				mouseenter: function() {
					$(this).find("a.zoom-in").css("visibility","visible");
				},
				mouseleave: function() {
					$(this).find("a.zoom-in").css("visibility","hidden");
				}
			});
			
			$("a.zoom-in").live('click', function() {
				// Get the title and link as data attributes
				var caption = $(this).attr('data-caption');
				if(!caption) {
					caption = "&nbsp;";
				}
				var img = "<img src='" + $(this).attr('data-image') + "' title='" + caption + "' class='modal-img'/>";
				// Adding content and showing the modal
				$('#photoModalHeader').html(caption);
				$('#photoModal > .modal-body').html(img);
				$('#photoModal > .modal-body').append('<div class="progress progress-striped active photo"><div class="bar"></div></div>');
				$('#photoModal').modal('show');
				$('.modal-img').load(function() {
					$("div.progress").remove();
					$(this).show();
				});
			});
		}	
	}
})(jQuery);