<div class="container-fluid">

    <div class="row-fluid">
        <div class="span4 spacer">
        </div>
        <div class="span4">
            <form class="well form form-vertical login" action="<?php echo base_url(); ?>login/process" method="post" name="process">
                <h2 class="shadow"><?php echo lang('ui_login_msg'); ?></h2>
                <hr />
                <?php if (!is_null($msg)) echo $msg; ?>
                <table style="margin-left: 3%;">
                    <tr>
                        <td><label><?php echo lang('ui_login_name'); ?>:</label></td>
                        <td><input type="text" name="username" class="input-large" placeholder="<?php echo lang('ui_login_name'); ?>"></td>
                    </tr>
                    <tr>
                        <td><label><?php echo lang('ui_password'); ?>:</label></td>
                        <td><input type="password" name="password" class="input-large" placeholder="<?php echo lang('ui_password'); ?>"></td>
                    </tr>
                </table>
                <button type="submit" class="btn btn-primary" value="login"><?php echo lang('ui_login'); ?></button>
                <label class="remember" style="position:relative; top: 1px;"><?php echo lang('ui_remember_me'); ?>
                    <input type="checkbox" name="rememberme" value="ON" />
                </label>	
            </form>
        </div><!--/span-->
    </div><!--/row-->
