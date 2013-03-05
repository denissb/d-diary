// Event handlers and GLOBAL events

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
}

//Checks if the user is logged in and retrives required information depending on the settings
FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
		setLoading();
		getFriendsData(function() {
			Plugins.FriendsBirthdays.initBirthdays("div.part_one");
			Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
			Plugins.FriendsBirthdays.displayBirthdays(3);
			Plugins.FriendsBirthdays.addEventHandlers();
		});
		getEventsData(function() {
			Plugins.FriendsEvents.initEvents("div.part_one");
			Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "after");
			Plugins.FriendsEvents.displayEvents(3);
			Plugins.FriendsEvents.addEventHandlers();
		});
		removeLoading();
		hide_hidden();
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
        message: ui_lang['app_advert'],
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
	if(window.active_settings.hasOwnProperty('friends_birthday')) {
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
	if(window.active_settings.hasOwnProperty('friends_events')) {
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

var Plugins = {}; //namespace

/* Friends birthdays plugin */

Plugins.FriendsBirthdays = (function(){ 
	
	var orderedBirthdays =[];
	
    return{
        //Public members
		initBirthdays: function(parent) {
			$(parent).addDiv("span6","","friends_birthdays");
			$("div#friends_birthdays").addDiv("well");
			$("div#friends_birthdays").children(".well").first()
				.html('<h3>'+ ui_lang['birthdays']+ '</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_birthdays" title="hide"><i class="icon-chevron-up"></i></a>'+
				'<a class="btn btn-mini" id="bd_before" href="#bd_before">' + ui_lang['past'] + '</a>' +
				'<a class="btn btn-mini disabled" id="bd_after" href="#bd_after">' + ui_lang['upcoming']+ '</a></div>');
			$("div#friends_birthdays").children(".well").first().addDiv("","","result_birthdays");	
			$("div#friends_birthdays").children(".well").append('<div class="plugin_buttons bottom"><a class="btn btn-mini" id="bd_more" href="#show_more">' + ui_lang['show_more'] + '</a></div><div class="clearfix"></div>');
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
		
			$('#bd_more').live('click', function() {
				Plugins.FriendsBirthdays.displayBirthdays(3);
			});
			
			$('#bd_after').live('click', function() {
				$("div#result_birthdays").empty();
				Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
				Plugins.FriendsBirthdays.displayBirthdays(3);
				$(this).addClass('disabled');
				$("#bd_before").removeClass('disabled');
			});
			
			$('#bd_before').live('click', function() {
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
})();

Plugins.FriendsEvents = (function() {
	
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
				'<a class="btn btn-mini" id="evt_search" href="#evt_search"><i class="icon-search"></i></a>' +
				'<a class="btn btn-mini" id="evt_before" href="#evt_before">' + ui_lang['past'] + '</a>' +
				'<a class="btn btn-mini disabled" id="evt_after" href="#evt_after">' + ui_lang['upcoming'] + '</a></div>');
			$("div#friends_events").children(".well").first().addDiv("","","result_events");
			$("div#friends_events").children(".well").append('<div class="plugin_buttons bottom"><a class="btn btn-mini" id="evt_more" href="#show_more">' + ui_lang['show_more'] + '</a></div><div class="clearfix"></div>');
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

			$('#evt_more').live('click', function() {
				Plugins.FriendsEvents.displayEvents(3);
			});

			$('#evt_after').live('click', function() {
				$("div#result_events").empty();
				Plugins.FriendsEvents.getOrderedEvents(getSelDate(), "after");
				Plugins.FriendsEvents.displayEvents(3);
				$(this).addClass('disabled');
				$("#evt_before").removeClass('disabled');
			});

			$('#evt_before').live('click', function() {
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
})();