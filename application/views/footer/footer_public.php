<hr>
<footer>
    <span class="copy">&copy; <?php echo lang('ui_app_name'); ?></span>
	<a class="terms" href="<?php echo base_url()."policy"; ?>" ><?php echo lang('ui_terms'); ?></a>
	<fb:like data-href="https://apps.facebook.com/d-diary/" width="350" layout="button_count" 
		style="position: relative; top: 8px; height: 24px;"/>
</footer>

</div> <!-- /container fluid-->

<div class="progress progress-striped active front-page"><div class="bar"></div></div>

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<div id="fb-root"></div>
    <script type="text/javascript">
	
	function preloader(){
            $(".progress").hide();
			$(".navbar").show();
            $(".container-fluid").show();
        }//preloader
    window.onload = preloader;
	
	$(document).ready(function() {
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo  $this->config->item('appId'); ?>', // App ID
          channelUrl : '<?php echo base_url(); ?>channel.php', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });
		
		var fbLogin = document.getElementById("fbLogin");
		
		if(fbLogin) {
			fbLogin.onclick = function() { fb_login(); }
		}
		
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
<script src="/bootstrap/js/compiled/<?php echo $this->config->item('lang_short'); ?>.min.js"></script>
<script src="/bootstrap/js/compiled/script.min.js"></script>
<div class="temp"></div>
</body>
</html>