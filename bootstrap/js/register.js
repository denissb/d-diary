$(document).ready(function() {
    
	var config = {};
	config.home = "//ddiary.loc/";
	config.change_pass = config.home+ 'settings/change_pass';
	window.err_msg = ui_lang['check_all_fields']; // Storing error data
	
    // Run email validation
    $("#email-input").keydown(function() {
        if(is_valid_email($(this).val())) {
            $(this).removeClass('error');
            $(this).addClass('success');
        } else {
			window.err_msg = ui_lang['invalid_email'];
            $(this).removeClass('success');
            $(this).addClass('error');
        }
    });
   
    // Run password strength check   
    $("#pass-input").keydown(function() {
        check_strength($(this));
    });
   
    // Check that login is longer than 3 symbols
    $("#login-input").keydown(function() {
        if ($(this).val().length > 3) {
            $(this).removeClass('error');
            $(this).addClass('success');
        } else {
			window.err_msg = ui_lang['short_username'];
            $(this).removeClass('success');
            $(this).addClass('error');
        }
    });
    
    //  Run confirmation
    confirm_val($("#email-confirm"));
    confirm_val($("#pass-confirm"));
    
    // Submit form when button is clicked
    $('#signup').click(function() {
        if($(".success").size() == 5 ) {
				$.ajax({
                type: 'POST',
                url: config.home + 'signup/validate',
                data: {
                    username: $("#login-input").val(),
					password: $("#pass-input").val(),
					email: $("#email-input").val(),
					terms: $("#agree-terms:checked").val(),
					capatcha: $("#capatcha-confirm").val()
                },
                success: function(data) { 
                    if(data == 'valid') { 
                        $('#registration').submit();
                    } else {
                        $('.register').response(data, false);
                    }
                },
                fail: function() {
                    $('.register').response(ui_lang['server_error'], false); 
                }
            });
        } else {
            $('.register').response(window.err_msg, false); 
        } 
    });
	
	  // Run password strength check   
    $("#refresh_capatcha").click(function() {
		$.ajax({
            type: 'GET',
            url: config.home + 'signup/capatcha',
            success: function(data) { 
					$('.terms_td').children("img").replaceWith(data);
                },
                fail: function() {
                    $('.register').response(ui_lang['server_error'], false); 
                }
            });
    });
    
	$("#old-pass").keydown(function() {
       check_strength($(this));
    });
	
	$('#change_password').click(function(e) {
		e.preventDefault();
		if ($(".success").size() < 3) {
			$('.pass-change').response(window.err_msg, false); 
		} else {
			var old_pass = $('#old-pass').val();
			var new_pass = $('#pass-input').val();
			if(old_pass == new_pass) {
				$('.pass-change').response(ui_lang['pass_is_same'], false);
				$('#pass-input').removeClass('success');
				$('#pass-input').addClass('error');
				$('.icon-thumbs-up').remove();
				return;
			}
			$.ajax({
				type: 'POST',
				url: config.change_pass,
				dataType: 'json',
				data: {
					old_pass: old_pass,
					new_pass: new_pass,
					new_pass_repeat: $('#pass-confirm').val()
                },
				success: function(data) {
					$('.pass-change').response(data.msg, data.success);
					if(data.success) {
						$('input[type="password"]').val('');
						$('input[type="password"]').removeClass('success');
						$('.icon-thumbs-up').remove();
					}	
				}
			});
		}
	});
// End document ready    
});

// Confirm that identical fields are identical
function confirm_val(elem) {
    $(elem).change(function() {
        var link_id = "#" + $(this).attr('id').replace("confirm", "input");
        if(elem.val() == $(link_id).val() && ($(this).val() != "")) {
            $(this).removeClass('error');
            $(this).addClass('success');
        } else {
            $(this).removeClass('success');
            $(this).addClass('error');  
			window.err_msg = ui_lang['invalid_confrimation'];
        }
    });
}

// Validate email by regex
function is_valid_email(email) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(email);
}

// Check the strength of the password
function check_strength(password){
    //initial strength
    var strength = 0;
    var set_pass = password.val();
    // if longet then 6 symbols increase strength
    if (set_pass.length > 6) {
        strength +=1;
    } else {
		window.err_msg = ui_lang['short_pass'];
	}
    //if password contains both lower and uppercase characters, increase strength value
    if (set_pass.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1
    //if it has numbers and characters, increase strength value
    if (set_pass.match(/([a-zA-Z])/) && set_pass.match(/([0-9])/))  strength += 1 
    //if it has one special character, increase strength value
    if (set_pass.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1
    //if it has two special characters, increase strength value
    if (set_pass.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) strength += 1
    
    //if value is less than 2
    if (strength < 2) {
        password.removeClass('success');
		window.err_msg = ui_lang['invalid_pass_combo'];
        password.addClass('error');  
        $("#pass_result").remove();
    } else if (strength == 2 ) {
        password.removeClass('error');
        password.addClass('success');
        $("#pass_result").remove();
    } else {
        password.removeClass('error');
        password.addClass('success');
        password.parent().append('<i class="icon-thumbs-up" id="pass_result"></i>');
    }
}

// Response function
(function( $ ) {
	jQuery.fn.response =
		function(data, success) {
			//Set alert type
			var type = 'alert-error';
			if(success == true) {
				type = 'alert-success'; 
			}
			//Append html
			this.before(
				'<div class="alert ' + type + '">' + data 
				+ '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');
			setTimeout("$('.alert').alert('close');", 7000);				
		};
})( jQuery );