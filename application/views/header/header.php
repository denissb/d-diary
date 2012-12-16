<!DOCTYPE html>
<html lang="<?php echo $this->config->item('lang_short'); ?>">
    <head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta charset="utf-8">
        <title>D-diary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?php echo lang('ui_descr'); ?>">
        <meta name="author" content="Deniss Borisovs">
        <!-- Le styles -->
        <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

		<!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="/bootstrap/img/favicon.ico">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/bootstrap/img/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/bootstrap/img/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="/bootstrap/img/apple-touch-icon-57-precomposed.png">
    </head>

    <body>
	<div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo  $this->config->item('appId'); ?>', // App ID
          channelUrl : '<?php echo base_url(); ?>channel.php', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });
		
		$('#fbLogin').click(function() {
			fb_login();
		});
		
		function fb_login(params) {
			FB.login(function(response) {
			console.log("Logging you in..");
			}, {scope: params});
		}
		
        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          window.location = window.location;
        });
      };
	 
      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo base_url(); ?>"><?php echo lang('ui_app_name'); ?></a>
                    <div class="nav-collapse">
                        <?php echo $navlist; ?>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
		<!--- Javascript is a must! --->
		<noscript>
			<div class="well" style="margin: 0 auto ; width:70%; text-align:center; padding: 10px 0;">
				<style type="text/css">
					div.container-fluid { visibility: hidden;}
				</style>
				<b>Your browser does not support JavaScript or it is disabled! 
				Please enable it for a richer browsing expirience!
				</b>
			</div>
		</noscript>