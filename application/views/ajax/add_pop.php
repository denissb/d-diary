<div class="arrow"></div><div class="popover-inner">
    <button type="button" class="close close-popover">&times;</button>
    <div class="popover-add">
        <form autocomplete="off" id="time-form">
            <label for="time" class="lb_time"><?php echo lang('cal_time'); ?>:</label>
            <input type="text" class="input-mini time_p" name="time" id="time" placeholder="0:00" value="" />
        </form>
    </div>	
    <div class="popover-content">
        <form id="event-form">
            <label for="event" class="lb_event"><?php echo lang('cal_event'); ?>:&nbsp;</label>
            <input type="text" class="input-medium event_p" name="event" id="event" value="" />
            <div class="details">
                <label for="description" class="lb_event"><?php echo lang('cal_descr'); ?>:&nbsp;</label>
                <div class="area" contenteditable="true"></div>
            </div>
            <button class="btn btn-primary" id="add-event"><?php echo lang('cal_add'); ?></button>
            <button class="btn show-details" title="more details.."><i class="icon-chevron-down"></i></button>
        </form>
    </div>
</div></div>