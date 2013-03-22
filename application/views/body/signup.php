<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4 spacer">
        </div>
        <div class="span4">
            <form class="well form form-vertical login" id="registration" 
                  action="<?php echo base_url(); ?>signup/process" method="post" name="process">
                <h2 class="shadow"><?php echo lang('ui_registration'); ?></h2>
                <hr />
                <?php if(!is_null($msg)) echo $msg; ?>
                <table class="register">
                    <tr>
                        <td><label><?php echo lang('ui_login_name'); ?>:</label></td>
                        <td><input type="text" name="username" class="input-large" id="login-input" placeholder="<?php echo lang('ui_login_name'); ?>" size="50"></td>
                    </tr>
                    <tr>
                        <td><label class="opt"><?php echo lang('ui_first_name'); ?>*:</label></td>
                        <td><input type="text" name="f_name" class="input-large" placeholder="<?php echo lang('ui_first_name'); ?>" size="50"></td>
                    </tr>
                    <tr>
                        <td><label class="opt"><?php echo lang('ui_last_name'); ?>*:</label></td>
                        <td><input type="text" name="l_name" class="input-large" placeholder="<?php echo lang('ui_last_name'); ?>" size="50"></td>
                    </tr>
                    <tr>
                        <td><label><?php echo lang('ui_password'); ?>:</label></td>
                        <td><input type="password" name="password" class="input-large" id="pass-input" placeholder="<?php echo lang('ui_password'); ?>" size="32">
                        </td>
                    </tr>
                    <tr>
                        <td><label><?php echo lang('ui_password_confirm'); ?>:</label></td>
                        <td><input type="password" name="password-confirm" class="input-large" id="pass-confirm" placeholder="<?php echo lang('ui_password_confirm'); ?>" size="32"></td>
                    </tr>
                    <tr>
                        <td><label><?php echo lang('ui_email'); ?>:</label></td>
                        <td><input type="text" name="email" class="input-large" id="email-input" placeholder="<?php echo lang('ui_email'); ?>" size="64"></td>
                    </tr>
                    <tr>
                        <td><label><?php echo lang('ui_email_confirm'); ?>:</label></td>
                        <td><input type="text" name="email-confirm" class="input-large" id="email-confirm" placeholder="<?php echo lang('ui_email_confirm'); ?>" size="64"></td>
                    </tr>
					<tr>
						<td colspan="2" class="terms_td" style="text-align:center; padding: 3px 0 5px;">
							<?php echo $capatcha; ?>
							<a href="javascript:void(0)" id="refresh_capatcha" class="btn btn-mini"><i class="icon-refresh" title="<?php echo lang('ui_reload'); ?>"></i></a>
						<td>
					</td>
					<tr>
                        <td><label><?php echo lang('ui_capatcha'); ?>:</label></td>
                        <td><input type="text" name="capatcha" class="input-large" id="capatcha-confirm" placeholder="<?php echo lang('ui_capatcha'); ?>" size="64"></td>
                    </tr>
					<tr>
						<td colspan="2" class="terms_td" style="text-align:center; padding: 3px 0 5px;">
							<label><?php echo lang('ui_terms_1'); ?> <a href="<?php echo site_url('policy'); ?> " target="_blank" title="terms"><?php echo lang('ui_terms_2'); ?></a>&nbsp;
								<input type="checkbox" id="agree-terms" name="agree-terms" value="Yes" />
							</label>
						</td>
					</tr>
                    <tr>
						<td><p style="text-align:left; padding-top:7px;" class="opt">* - <?php echo lang('ui_optional'); ?></td>
                        <td id="reg-controls">
							<button type="button" id="signup" class="btn btn-primary" value="signup"><?php echo lang('ui_signup_done'); ?></button>&nbsp;
							<button type="button" class="btn" value="signup" onclick="window.location='<?php echo base_url(); ?>'"><?php echo lang('ui_cancel'); ?></button>
						</td>
                    </tr>
                </table>
            </form>
        </div><!--/span-->
    </div><!--/row-->