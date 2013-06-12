<hr>
<footer>
    <span class="copy">&copy; <?php echo lang('ui_app_name'); ?></span>
	<a class="terms" href="<?php echo base_url()."policy"; ?>" ><?php echo lang('ui_terms'); ?></a>
	<?php if ($this->session->userdata('with_fb')) { ?>
	<div id="sendRequests"><span><a class="fb_button fb_button_small"><span class="fb_button_text"><?php echo lang('ui_invite'); ?></span></a></span></div>
	<fb:like data-href="https://apps.facebook.com/d-diary/" width="350" layout="button_count" 
		style="position: relative; top: 8px; height: 24px;"/>
	<?php } ?>
</footer>

</div> <!-- /container fluid-->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<div id="fb-root"></div>
    <script type="text/javascript">
	$(document).ready(function() {
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo  $this->config->item('appId'); ?>', // App ID
          channelUrl : '<?php echo base_url(); ?>channel.php', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });
		
		$.getScript("/bootstrap/js/fb_func.js", function(){
			<?php if(!is_array($settings) && $settings) { ?>
			window.active_settings = JSON.parse('<?php echo $settings; ?>');
			<?php } else { ?>
			window.active_settings = "";	
			<?php } ?>
		});
		
        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          window.location = window.location;
        });
      }
	 });
      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
<script type="text/javascript"> window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js">\x3C/script>')</script>
<script src="/bootstrap/js/compiled/<?php echo $this->config->item('lang_short'); ?>.min.js"></script>
<script src="/bootstrap/js/compiled/script.min.js"></script>
<?php if($this->uri->segment(1) == "settings") { ?>
<script src="/bootstrap/js/register.js"></script>
<?php } ?>
<div class="temp"></div>
</body>
</html>