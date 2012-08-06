	<div class="container-fluid">
			<h2><?php echo ucfirst($this->session->userdata('fname')); ?>'s calendar</h2>

	<div class="row-fluid">
      
        <div class="span7">
          <?php echo $calendar; ?>
         
        </div><!--/span-->
		
		<div class="span5">
          <div class="well" id="day-events">
          </div><!--/.well -->
        </div><!--/span-->

      </div><!--/row-->