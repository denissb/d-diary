<div class="container-fluid">
    <div class="row-fluid">	
	<div class="well full-page">
		<h2 class="shadow"><?php echo lang('ui_settings'); ?></h2>
			<?php if($msg) echo $msg; ?>
			<ul class="nav nav-tabs">
			  <li class="active"><a href="#extensions" data-toggle="tab"><?php echo lang('ui_extensions'); ?></a></li>
			  <li><a href="#account" data-toggle="tab"><?php echo lang('ui_account'); ?></a></li>
			</ul>
			
		<div class="tab-content">
			<div class="tab-pane active" id="extensions">
				<?php if ($this->session->userdata('with_fb')) { ?>
				<div>
				<form action="settings/process" method="post" name="process" id="settings" style="margin: 0px;">
					<div class="ext">
						<label class="checkbox">
						  <input type="checkbox" class="widget" name="events" value="friends_events" <?php if(isset($enabled['friends_events'])) echo "checked"; ?>><h4><?php echo lang('ui_friends_events'); ?></h4>
						</label>
						<p><?php echo lang('ui_friends_events_info'); ?></p>
					</div>
					<div class="ext">
						<label class="checkbox">
						  <input type="checkbox" class="widget" name="birthdays" value="friends_birthday"<?php if(isset($enabled['friends_birthday'])) echo "checked"; ?>><h4><?php echo lang('ui_friends_birthdays'); ?></h4>
						</label>
						<p><?php echo lang('ui_friends_birthdays_info'); ?></p>
					</div>
				</form>
				</div>
				<div style="padding: 8px 7px 0 0;"><span id="addWidgets"><a class="fb_button fb_button_medium"><span class="fb_button_text"><?php echo lang('ui_change_settings'); ?></span></a></span></div>
				<?php } else { ?>
				<div style="padding: 7px 0;">
					<strong><?php echo lang('ui_extensions_use_facebook'); ?></strong>
				</div>
				<?php } ?>
			</div>
			
			<div class="tab-pane" id="account">
			  <?php echo $this->session->userdata('settings'); ?>
			</div>
			
		</div>	
	</div>