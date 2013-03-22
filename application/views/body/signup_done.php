<div class="container-fluid">

    <div class="row-fluid">
        <div class="span3 spacer">
        </div>
        <div class="span6">
            <div class="well login">
                <h2 class="shadow"><?php echo lang('ui_reg_successful'); ?></h2>
                <?php if (!is_null($msg)) echo $msg; ?>
                <hr />
                <h4 style="text-align:center;">
                    <?php echo lang('ui_reg_gratitude'); ?>
                </h4>
                <br />
                <p>
                   <?php echo lang('ui_reg_use_link'); ?>
                </p>
				<p>
					<span class="quote"><?php echo lang('ui_reg_usefull'); ?></span>
				</p>
            </div>
        </div><!--/span-->
    </div><!--/row-->