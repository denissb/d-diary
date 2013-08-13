		{table_open}<table class="table calendar table-striped table-bordered" id="{year-month}" border="0">{/table_open}

		{heading_row_start}<tr>{/heading_row_start}

		{heading_previous_cell}<th><a href="{previous_url}" class="btn"><i class="icon-arrow-left"></a></th>{/heading_previous_cell}
		{heading_title_cell}
		<th colspan="{colspan}">
		<h3 class="sel_month">{heading}</h3>
		<button class="btn toggle-cal" title="hide calendar"><i class="icon-chevron-up"></i></button>
		<button class="btn show-small-cal" title="pick dates" data-date-format="yyyy-mm-dd" 
		data-date="2013-09-26"><i class="icon-calendar"></i></button>
		</th>
		{/heading_title_cell}
		{heading_next_cell}<th><a href="{next_url}" class="btn"><i class="icon-arrow-right"></i></a></th>{/heading_next_cell}

		{heading_row_end}
		</tr>
		<tbody id="cal-body">
		{/heading_row_end}

		{week_row_start}<tr class="cal-week-row">{/week_row_start}
		{week_day_cell}<td>{week_day}</td>{/week_day_cell}
		{week_row_end}</tr>{/week_row_end}

		{cal_row_start}<tr class="cal-row">{/cal_row_start}
		{cal_cell_start}<td>{/cal_cell_start}

		{cal_cell_content}<div class="cal-cell content">
		<div class="day {past}">{day}</div>
		<span class="events">{content}</span>
		</div>{/cal_cell_content}

		{cal_cell_content_today}<div class="cal-cell cal-active content">
		<div class="day bold">{day}</div>
		<span class="events">{content}</span>
		</div>{/cal_cell_content_today}

		{cal_cell_no_content}<div class="cal-cell"><div class="day no-events {past}">{day}</div></div>{/cal_cell_no_content}
		{cal_cell_no_content_today}<div class="cal-cell cal-active"><div class="day bold">{day}</div></div>{/cal_cell_no_content_today}

		{cal_cell_blank}&nbsp;{/cal_cell_blank}

		{cal_cell_end}</td>{/cal_cell_end}
		{cal_row_end}</tr>{/cal_row_end}

		{table_close}
		</tbody>
		</table>
		{/table_close}