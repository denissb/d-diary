// Event handlers

$('#fbLogin').click(function() {
	fb_login();
});

$('#addWidgets').click(function() {
	$('#settings').submit();
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

// Global variables
var ordered_birthdays;

//Checks if the user is logged in and retrives required information depending on the settings
FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
	
		if(window.active_settings.hasOwnProperty('friends_birthday')) {
			init_birthdays();
			get_friends_data(function() {
				ordered_birthdays = get_ordered_birthdays(get_sel_date(), "after");
				display_birthdays(ordered_birthdays, 3);
			});
		}
		
		if (hasScrollBar())  {
			$('button.toggle-cal').show();
		}	
		
	} else if (response.status === 'not_authorized') {
		// the user is logged in to Facebook, 
		// but has not authenticated your app
		 } else {
			// the user isn't logged in to Facebook.
		 }
	});		

function sendRequestToFriends() {
    FB.ui({
        method: 'apprequests',
        message: "Check out D-diary, it's a usefull app!"
    });     
}

//Adding a div element tot the DOM

function get_sel_date() {
	var day = $('.cal-active').find('div.day').html();
	var month_year = $('table.calendar').attr('id');
	var year = month_year.match(/[0-9]{4}/);
	var month = month_year.match(/[0-9]{2}$/);
	if( day != null )
		return (new Date(year, month - 1, day));
	else
		return (new Date());
}

jQuery.fn.addDiv =
    function(css_class, content, css_id) {
        $("<div/>", {
		  "class": css_class,
		  "id": css_id,
		  text: content,
		}).appendTo(this);			
    }
	
Storage.prototype.setObject = function(key, value) {
    this.setItem(key, JSON.stringify(value));
}

Date.prototype.getMonthName = function() {
    var monthNames = [ "January", "February", "March", "April", "May", "June", 
    "July", "August", "September", "October", "November", "December" ];
    return monthNames[this.getMonth()];
}

$('a.bt_hide').live('click', function(e) {
    var el = $(this).attr("href");
	$(el).toggle();  
	$(this).children('i').toggleClass('icon-chevron-up');
    $(this).children('i').toggleClass('icon-chevron-down');
	set_hidden(el);
});

// Sets the localStorage array of hidden elements
function set_hidden(el) {
	if(typeof(Storage) !== "undefined") {
		var hidden = new Array();
		if(localStorage.hidden !== "undefined") {
			hidden = localStorage.hidden;
			var index = hidden.indexOf(el);
		}	
		if($(el).not(":visible") && index == -1) {
			hidden.push(el);
			localStorage.setObject("hidden", hidden);
		} else {
			hidden.splice(index, 1);
			localStorage.setObject("hidden", hidden);
		}
	}	
}

/* Friends plugin  - in the future may be put to separate file*/

/*	Retreving friends data  */
function get_friends_data(onresult) {
	//Web storage
	 if(typeof(Storage) !== "undefined")
	  {
		// Updating storage if it's older than 1 hour
		var timenow = new Date().getTime();
		var recived = (timenow - localStorage.recived)/1000;
		localStorage.clear();
		if(localStorage.friends_data == "" || recived > 3600 || localStorage.friends_data == "udefined") 
		{
			FB.api('/me/friends', {fields: 'name,id,location,birthday,picture'}, function(response) {
				localStorage.setObject('friends_data', response.data);
				localStorage.setObject('recived', timenow);
				onresult();
			});
		}
	  }
	else
	  {
		FB.api('/me/friends', {fields: 'name,id,location,birthday,picture'}, function(response) {
			window.birthdays =  response.data;
			onresult();
		});
	  }
}

// Sorting friends by birthdays - parameters: condition(before or after), date - date for sorting
function get_ordered_birthdays(date, condition) {
	var birthdays = (localStorage.friends_data) ? JSON.parse(localStorage.friends_data) : window.birthdays;
	var regex = /[0-9]{2}/g;
	var now = (date) ? date : new Date();
	now.setHours(0,0,0,0);
	var result = [];
	var lap = [];
	for ( var i = 0; i < birthdays.length ; i++)
	{
		if(birthdays[i].birthday != undefined)
		{
			var date = birthdays[i].birthday.toString();
			// Getting the month and date of birthday
		    var clean = date.match(regex);
			var month = clean[0];
			var day = clean[1];
			var date = new Date(now.getFullYear(), month - 1, day);
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
	return result;
}

// Initialize the required html
function init_birthdays() {
	$("div.part_one").addDiv("span6","","friends_birthdays");
	$("div#friends_birthdays").addDiv("well");
	$("div#friends_birthdays").children(".well").first()
		.html('<h3>Birthdays</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_birthdays" title="hide"><i class="icon-chevron-up"></i></a>'+
		'<a class="btn btn-mini" id="bd_before" href="#bd_before">Past</a>' +
		'<a class="btn btn-mini disabled" id="bd_after" href="#bd_after">Upcoming</a>' +
		'<a class="btn btn-mini" id="bd_more" href="#show_more">Show more</a></div>' +
		'<div class="progress progress-striped active load-birthdays"><div class="bar"></div></div>');
	$("div#friends_birthdays").children(".well").first().addDiv("","","result_birthdays");	
}

function display_birthdays(data, length) {
	var result = data.splice(0, length);
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
		output +="<a href='http://facebook.com/" + result[i].id + "' title='"+ result[i].name +"' target='_blank'><img src='"+ result[i].picture.data.url +"'/></a>";
		output +="<span class='name'>"+ result[i].name + icon +"</span>";
		output +="<span class='date'>"+ result[i].birthday_date.getMonthName() + " " + result[i].birthday_date.getDate() + "</span>";
		output +="</div>";
		}
	$("div.load-birthdays").remove();
	$("div#result_birthdays").append(output);
}

$('div.cal-cell').click(function() {
	$("div#result_birthdays").empty();
	ordered_birthdays = get_ordered_birthdays(get_sel_date(), "after");
	display_birthdays(window.ordered_birthdays, 3);
	$("#bd_after").addClass('disabled');
	$("#bd_before").removeClass('disabled');
});

$('#bd_more').live('click', function() {
	display_birthdays(window.ordered_birthdays, 3);
});

$('#bd_after').live('click', function() {
	$("div#result_birthdays").empty();
	ordered_birthdays = get_ordered_birthdays(get_sel_date(), "after");
	display_birthdays(window.ordered_birthdays, 3);
	$(this).addClass('disabled');
	$("#bd_before").removeClass('disabled');
});

$('#bd_before').live('click', function() {
	$("div#result_birthdays").empty();
	ordered_birthdays = get_ordered_birthdays(get_sel_date(), "before");
	display_birthdays(window.ordered_birthdays, 3);
	$(this).addClass('disabled');
	$("#bd_after").removeClass('disabled');
});

/* Events plugin */