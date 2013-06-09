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
<script type="text/javascript"> window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js">\x3C/script>')</script>
<script src="/bootstrap/js/compiled/<?php echo $this->config->item('lang_short'); ?>.min.js"></script>
<script src="/bootstrap/js/compiled/script.min.js"></script>
<?php if($this->uri->segment(1) == "settings") { ?>
<script src="/bootstrap/js/register.js"></script>
<?php } ?>
<div class="temp"></div>
</body>
</html>