$this->conf['template'] = '
		{table_open}<table class="table table-striped table-bordered" border="0">{/table_open}

	   {heading_row_start}<tr>{/heading_row_start}

	   {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
	   {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
	   {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

	   {heading_row_end}</tr>{/heading_row_end}

	   {week_row_start}<tr class="cal-week-row">{/week_row_start}
	   {week_day_cell}<td>{week_day}</td>{/week_day_cell}
	   {week_row_end}</tr>{/week_row_end}

	   {cal_row_start}<tr class="cal-row">{/cal_row_start}
	   {cal_cell_start}<td>{/cal_cell_start}

	   {cal_cell_content}<div class="cal-cell"><a href="{content}"><div class="day">{day}</div></div></a>{/cal_cell_content}
	   {cal_cell_content_today}<div class="cal-cell"><a href="{content}"><div class="day bold">{day}</div></a></div>{/cal_cell_content_today}
	   {cal_cell_no_content}<div class="cal-cell"><div class="day no-events">{day}</div><span class="events">3</span></div>{/cal_cell_no_content}
	   {cal_cell_no_content_today}<div class="cal-cell"><div class="day bold">{day}</div></div>{/cal_cell_no_content_today}

	   {cal_cell_blank}&nbsp;{/cal_cell_blank}

	   {cal_cell_end}</td>{/cal_cell_end}
	   {cal_row_end}</tr>{/cal_row_end}

	   {table_close}</table>{/table_close}
		';
		
	}