<div class="container-fluid">
    <div class="row-fluid">	
	<div class="well full-page">
		<h2 class="shadow"><?php echo lang('ui_settings'); ?></h2>
			<?php if($msg) echo $msg; ?>
			<ul class="nav nav-tabs">
			  <li><a href="#extensions" data-toggle="tab"><?php echo lang('ui_extensions'); ?></a></li>
			  <li><a href="#account" data-toggle="tab"><?php echo lang('ui_account'); ?></a></li>
			  <li><a href="#security" data-toggle="tab"><?php echo lang('ui_security'); ?></a></li>
			</ul>
			
		<div class="tab-content">
			<div class="tab-pane active" id="extensions">
				<?php if ($this->session->userdata('with_fb')) { ?>
				<div>
				<form action="" method="post" name="process" id="widget_settings" style="margin-top: 7px;">
					<div class="ext">
						<label class="checkbox">
						  <input type="checkbox" class="widget" name="events" value="friends_events" <?php if(isset($enabled['friends_events'])) echo "checked"; ?>><h4><?php echo lang('ui_friends_events'); ?></h4>
						</label>
						<p><?php echo lang('ui_friends_events_info'); ?></p>
					</div>
					<div class="ext">
						<label class="checkbox">
						  <input type="checkbox" class="widget" name="birthdays" value="friends_birthday" <?php if(isset($enabled['friends_birthday'])) echo "checked"; ?>><h4><?php echo lang('ui_friends_birthdays'); ?></h4>
						</label>
						<p><?php echo lang('ui_friends_birthdays_info'); ?></p>
					</div>
					<div class="ext">
						<label class="checkbox">
						  <input type="checkbox" class="widget" name="notes" value="publish_stream" <?php if(isset($enabled['notes'])) echo "checked"; ?>><h4>Publish notes</h4>
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
				<div>
					<div class="ext">
					<form class="form-horizontal" style="margin-bottom: 8px;" action="" method="post" id="user_settings">
					  <div class="control-group">
						<label class="control-label" for="f_name"><?php echo lang('ui_f_name'); ?>:</label>
						<div class="controls">
						  <input type="text" name="f_name" class="input-large" required placeholder="<?php echo lang('ui_f_name'); ?>" size="50" value="<?php echo $user->f_name; ?>">
						</div>
					  </div>
					  <div class="control-group">
						<label class="control-label" for="l_name"><?php echo lang('ui_l_name'); ?>:</label>
						<div class="controls">
						  <input type="text" name="l_name" class="input-large" required placeholder="<?php echo lang('ui_l_name'); ?>" size="50" value="<?php echo $user->l_name; ?>">
						</div>
					  </div>
					    <div class="control-group">
						<label class="control-label" for="language"><?php echo lang('ui_language'); ?>:</label>
						<div class="controls">
						<?php
							$available = array(
								  'english' => lang('ui_english'),
								  'russian' => lang('ui_russian'),
								  'latvian' => lang('ui_latvian')
								);
							echo form_dropdown('language', $available, $user->language);	
						?>
						</div>
					  </div>
					  <div class="control-group" style="margin-bottom: 8px;">
						<div class="controls">
						   <button type="submit" class="btn btn-primary" id="userSettings"><?php echo lang('ui_apply_changes'); ?></a>
						</div>
					  </div>
					</form>	
					</div>
					<?php if ($fb_id) { ?>
					<div class="ext">
					<form class="form-horizontal" style="margin-bottom: 5px;">
					  <div class="control-group" style="margin-bottom: 10px;">
						<label class="control-label" for="f_name"><?php echo lang('ui_linked_profile'); ?>:</label>
						<div class="controls">
						  <a class="btn btn-primary" style="margin-right: 15px;" target="_blank" href="http://facebook.com/<?php echo $fb_id; ?>"><?php echo $fb_id; ?></a>
						  <a style="margin: 3px 0 3px;" class="fb_button fb_button_medium" id="unlink_button" href="<?php echo site_url("logout/app/unlink"); ?>"><span class="fb_button_text"><?php echo lang('ui_unlink_acc'); ?></span></a>
						</div>
						</div>
					</form>
						<p style="margin-left: 12px;" class="opt"><?php echo lang('ui_acc_unlink_fb'); ?>
					</div>
					<?php } ?>  
					</div>
				</div>
				
				<div class="tab-pane" id="security">
					<div>
						<div class="ext">
							<div class="control-group">
								<h4><?php echo lang('ui_sec_last_login_details'); ?></h4>
								<hr/>
								<div class="row-fluid">
									<div class="span4" style="min-height: 0px;">
										<b><?php echo lang('ui_sec_last_login_ip')." ".long2ip($user->last_ip); ?></b>
									</div>
									<div class="span5" style="min-height: 0px;">
										<b><?php echo lang('ui_sec_last_login_date')." ".date('Y.m.d H:i:s', strtotime($user->last_date)); ?></b>
									</div>
								</div>
								
							</div>
						</div>
						
						<div class="ext pass-change">
								<h4><?php echo lang('ui_sec_pass_change'); ?></h4>
								<hr/>
								<form action="" class="form-horizontal" style="margin-bottom: 5px;" method="post" name="pass_reset" id="change_pass">
									<div class="control-group">
										<label class="control-label" for="old_pass"><?php echo lang('ui_sec_old_pass'); ?>:</label>
										<div class="controls">
											<input type="password" name="old_pass" class="input-large" id="old-pass" required placeholder="<?php echo lang('ui_sec_old_pass'); ?>" size="50" value="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="new_pass"><?php echo lang('ui_sec_new_pass'); ?>:</label>
										<div class="controls">
											<input type="password" name="new_pass" class="input-large" id="pass-input" required placeholder="<?php echo lang('ui_sec_new_pass'); ?>" size="50" value="">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="new_pass_repeat"><?php echo lang('ui_sec_new_pass_repeat'); ?>:</label>
										<div class="controls">
											<input type="password" name="new_pass_repeat" class="input-large" id="pass-confirm" required placeholder="<?php echo lang('ui_sec_new_pass_repeat'); ?>" size="50" value="">
										</div>
									</div>
									<div class="controls">
										<button type="submit" class="btn btn-primary" id="change_password"><?php echo lang('ui_apply_changes'); ?></a>
									</div>
								</form>
						</div>
						
						<div class="ext">
							<div class="control-group">
								<h4><?php echo lang('ui_sec_close_acc'); ?>:</h4>
								<hr/>
								<div class="row-fluid">
									<form class="form-horizontal" style="margin-bottom: 5px;">
										<div class="control-group" style="margin-bottom: 0px;">
											<label class="control-label" for="f_name"><?php echo lang('ui_sec_if_issues'); ?>:</label>
											<div class="controls">
												<?php if (!$fb_id) { ?>
												<a class="btn btn-primary" style="margin-right: 15px;" target="_blank" id="delete_acc_button" 
													href="<?php //echo site_url("logout/app/delete"); ?>"><?php echo lang('ui_sec_close_acc'); ?></a>
												<?php } else { ?>
												<a style="margin: 3px 0 3px;" class="fb_button fb_button_medium" id="delete_acc_button" 
													href="<?php echo site_url("logout/app/delete"); ?>"><span class="fb_button_text"><?php echo lang('ui_sec_close_acc'); ?></span></a>
												<?php } ?>
											</div>
										</div>
									</form>
									<p style="margin: 5px 12px 0;" class="opt"><?php echo lang('ui_sec_close_info'); ?></p>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>	
	</div>