// Event handlers and GLOBAL events

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

//Checks if the user is logged in and retrives required information depending on the settings
FB.getLoginStatus(function(response) {
	if (response.status === 'connected') {
		// Add loading element
		$("div.part_one").html('<div class="progress progress-striped active"><div class="bar"></div></div>');
		getFriendsData(function() {
			if(window.active_settings.hasOwnProperty('friends_birthday')) {
				Plugins.FriendsBirthdays.initBirthdays("div.part_one");
				Plugins.FriendsBirthdays.getOrderedBirthdays(getSelDate(), "after");
				Plugins.FriendsBirthdays.displayBirthdays(3);
			}
		//Remove loading element
		$("div.part_one > .progress").remove();
		});
	
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
jQuery.fn.addDiv =
    function(css_class, content, css_id) {
        $("<div/>", {
		  "class": css_class,
		  "id": css_id,
		  text: content,
		}).appendTo(this);			
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
	//set_hidden(el);
});

// Retreving friends data - based on totalStorage plugin
function getFriendsData(callback) {
	var timenow = new Date().getTime();
	var recived = (timenow - $.totalStorage('recived'))/1000;
	if($.totalStorage('friends_data') == null || recived > 3600) {
		FB.api('/me/friends', {fields: 'name,id,location,birthday,picture'}, function(response) {
			$.totalStorage('friends_data', response.data);
			$.totalStorage('recived', timenow);
			callback();
		});
	} else {
		callback();
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
				.html('<h3>Birthdays</h3><div class="plugin_buttons"><a class="btn btn-mini bt_hide" href="#result_birthdays" title="hide"><i class="icon-chevron-up"></i></a>'+
				'<a class="btn btn-mini" id="bd_before" href="#bd_before">Past</a>' +
				'<a class="btn btn-mini disabled" id="bd_after" href="#bd_after">Upcoming</a>' +
				'<a class="btn btn-mini" id="bd_more" href="#show_more">Show more</a></div>');
			$("div#friends_birthdays").children(".well").first().addDiv("","","result_birthdays");	
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
					var date = birthdays[i].birthday.toString();
					// Getting the month and date of birthday
					var clean = date.match(/[0-9]{2}/g);
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
				output +="<a href='http://facebook.com/" + result[i].id + "' title='"+ result[i].name +"' target='_blank'><img src='"+ result[i].picture.data.url +"'/></a>";
				output +="<span class='name'>"+ result[i].name + icon +"</span>";
				output +="<span class='date'>"+ result[i].birthday_date.getMonthName() + " " + result[i].birthday_date.getDate() + "</span>";
				output +="</div>";
				}
			$("div#result_birthdays").append(output);
		}
	}
})();

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

/* End of friends birthdays plugin */