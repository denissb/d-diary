<!DOCTYPE html>
<html lang="<?php echo $this->config->item('lang_short'); ?>">
    <head>
        <meta charset="utf-8">
        <title>D-diary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <!-- Le styles -->
        <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="/bootstrap/css/datepicker.css" rel="stylesheet">
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
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo base_url(); ?>">D-diary<span class="status">beta</span></a>
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