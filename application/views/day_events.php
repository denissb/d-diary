<div class="date-unit">
	<h2 id="active-date"><?php echo $day ?>.</h2>
	<span><?php echo $month_name; ?></span>
	<span class='year'><?php echo $year; ?></span>
	<hr />
	<div class='event-item' id="add-section">
		<div class="popover-add">
		<form autocomplete="off" id="time-form">
				<label for="time" class="lb_time">Time:</label>
				<input type="text" class="input-mini" name="time" id="time" placeholder="0:00" value=""></input>
		</form>
		</div>
		<form id="event-form">
				<label for="event" class="lb_event">Event:&nbsp;</label>
				<input type="text" class="input resizable" name="event" id="event" value=""></input><br />
					<label for="description" class="lb_descr">Description:&nbsp;</label>
					<div class="area" contenteditable="true"></div>
				<button class="btn btn-primary" id="main-add">Add event</button>
				<button class="btn" id="hide-add">Cancel</button>
		</form>
	</div>
	<button class="btn btn-primary" id="show-add">Add event</button>
</div>
	<div id="event-container">
	<?php if($events) {
		foreach ($events as $row): ?>
	<div class="event-item">
	
		<div class="controlls">
			<div class="btn-group">
			  <button class="btn btn-primary edit-event" id="<?php echo $row->id; ?>">Edit</button>
			  <button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">
				<span class="caret"></span>
			  </button>
			<ul class="dropdown-menu">
			<?php if($row->done == false) { ?>	
				<li><a href='#' class="done-event">Done</a></li>
				<li class='divider'></li>
			<?php } ?>		
				<li><a href='#' class="delete-event">Delete</a></li>
			</ul>
			</div>
		</div>
		
		<span class="time"><?php echo substr($row->time, 0, -3); ?></span>
		
		<h3 class="event-name <?php echo $row->done ? 'event-done' : 0; ?>"><?php echo $row->title; ?></h3>
		
		<?php if($row->description): ?>
		<div class="description closed">
			<div class="desc-field">
				<?php echo $row->description; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php endforeach; } else {
		echo "<p class='event-info'>No events planned yet</p>";
		}
	?>
	</div>