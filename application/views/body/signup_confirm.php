<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3 spacer">
        </div>
        <div class="span6">
            <div class="well login">
                <h2 class="shadow"><?php echo $success; ?></h2>
                <hr />
                <h4 style="text-align:center; padding-bottom: 8px;">
                    <?php echo $result ?>
                </h4>
             
				<?php if(isset($link)) { ?>
					<p>
					   <span id="fbLogin"><a class="fb_button fb_button_medium"><span class="fb_button_text"><?php echo lang('ui_login'); ?></span></a></span>   
					</p>
					<p>
						<?php echo anchor($link, lang('ui_continue_to_cal'), array('class' => 'btn btn-primary')); ?>
					</p>
					<p>
					<span class="quote"><?php echo lang('ui_reg_usefull'); ?></span>
					</p>
				<?php } ?>
            </div>
        </div><!--/span-->
    </div><!--/row-->