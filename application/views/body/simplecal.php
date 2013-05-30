<div class="container-fluid">
    <h2 class="topic"><?php echo ucfirst($this->session->userdata('fname')).lang('ui_decl')." ".lang('ui_calendar'); ?></h2>

    <div class="row-fluid">

        <div class="span7">
            <?php echo $calendar; ?>
			
			<?php if(!is_array($settings)) { ?>
			<div class="row-fluid part_one">
			</div>
			<?php } ?>
        </div><!--/span-->

        <div class="span5">
            <div class="well" id="day-events">
            </div><!--/.well -->
				
			<?php if(!is_array($settings)) { ?>	
			<div class="row-fluid part_two">
			</div>
			<?php } ?>
        </div><!--/span-->

    </div><!--/row-->