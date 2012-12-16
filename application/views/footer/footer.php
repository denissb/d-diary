<hr>
<footer>
    <span class="copy">&copy; <?php echo lang('ui_app_name'); ?></span>
	<a class="terms" href="<?php echo base_url()."policy"; ?>" ><?php echo lang('ui_terms'); ?></a>
	<?php if ($this->session->userdata('with_fb')) { ?>
	<div id="sendRequests"><span><a class="fb_button fb_button_small"><span class="fb_button_text"><?php echo lang('ui_invite'); ?></span></a></span></div>
	<fb:like data-href="http://apps.facebook.com/d-diary/" width="350" layout="button_count" 
		style="position: relative; top: 4px; height: 20px;"/>
	<?php } ?>
</footer>

</div> <!-- /container fluid-->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="/bootstrap/js/lang/<?php echo $this->config->item('lang_short'); ?>.js"></script>
<script src="/bootstrap/js/bootstrap.js"></script>
<script src="/bootstrap/js/plugins/jquery.cookie.js"></script>
<script src="/bootstrap/js/plugins/jquery.total-storage.min.js"></script>
<script src="/bootstrap/js/plugins/bootstrap-datepicker.<?php echo $this->config->item('lang_short'); ?>.js"></script>
<script src="/bootstrap/wysiwyg/lib/wysiwyg.js"></script>
<script src="/bootstrap/js/tinysort.js"></script>
<script src="/bootstrap/js/script.js"></script>
<div class="temp"></div>
</body>
</html>