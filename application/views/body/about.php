<div class="container-fluid">

    <div class="row-fluid">
        <div class="span4 slogan">
            <h2 class="shadow"><?php echo lang('ui_front_slogan'); ?></h2>
            <ul class="features">
				<?php foreach(lang('ui_features') as $feature) {
					echo "<li>".$feature."</li>";
				} ?>
            </ul>
        </div><!--/span-->
        <div class="span8 slogan_img">
            <img src="/bootstrap/img/slogan_img.png" alt="concept" title="D-diary"/>
        </div>    
    </div><!--/row-->
	<br/>
	<div class="row-fluid">
		<div class="span1"></div>
		<div class="span3 advert">
		   <div class="well">
			<img src="/bootstrap/img/icon-facebook.png" alt="concept" title="D-diary"/>
			<h2 class="shadow"><?php echo lang('ui_facebook'); ?></h2>
				<p><?php echo lang('ui_facebook_slogan'); ?></p>
			<div class="clearfix"></div>
			<ul>
				<?php foreach(lang('ui_fb_features') as $feature) {
					echo "<li>".$feature."</li>";
				} ?>
			</ul>
		   </div>
		</div><!--/span-->
		<div class="span4 advert">
		   <div class="well">
			 <img src="/bootstrap/img/icon-plug.png" alt="concept" title="D-diary"/>
			<h2 class="shadow"><?php echo lang('ui_plugins'); ?></h2>
				<p><?php echo lang('ui_plugins_slogan'); ?></p>
			<div class="clearfix"></div>
			<ul>
				<?php foreach(lang('ui_plugins_features') as $feature) {
					echo "<li>".$feature."</li>";
				} ?>
			</ul>
		   </div>
		</div><!--/span-->
		<div class="span3 advert">
		   <div class="well">
			 <img src="/bootstrap/img/icon-privacy.png" alt="concept" title="D-diary"/>
			<h2 class="shadow"><?php echo lang('ui_privacy'); ?></h2>
				<p><?php echo lang('ui_privacy_slogan'); ?></p>
			<div class="clearfix"></div>
			<ul>
				<?php foreach(lang('ui_privacy_features') as $feature) {
					echo "<li>".$feature."</li>";
				} ?>
			</ul>
		   </div>
		</div><!--/span-->
		<div class="span1"></div>
	</div>
    <div class="row-fluid">
        <div class="span2"></div>
        <div class="span8" style="text-align:center;">
           <p>
            <span class="quote">-The project is curently still in early development, and hopefully may provide you with some basic functionality, 
               the emphasis was set on user friendlyness for people who want really basic stuff. 
               The idea behind starting this project was to try and create a UI that would fit my own super simple needs.
            </span>  
           </p>
        </div>    
    </div><!--/row-->