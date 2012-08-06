<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simplecalendar extends CI_Model {

	private $conf;
	private $userid;
	
	function __construct() 
	{
		parent::__construct();
		// Calendar config
		$this->conf = array(
			'start_day' => 'monday',
			'show_next_prev' => TRUE,
			'next_prev_url' => base_url() . 'show'
		);
		//Calendar template
		$this->conf['template'] = '
		{table_open}<table class="table calendar table-striped table-bordered" border="0">{/table_open}

	   {heading_row_start}<tr>{/heading_row_start}

	   {heading_previous_cell}<th><a href="{previous_url}" class="btn"><i class="icon-arrow-left"></a></th>{/heading_previous_cell}
	   {heading_title_cell}
	   <th colspan="{colspan}">
		<h3>{heading}</h3>
		<button class="btn toggle-cal" title="details.."><i class="icon-chevron-up"></i></button>
	   </th>
	   {/heading_title_cell}
	   {heading_next_cell}<th><a href="{next_url}" class="btn"><i class="icon-arrow-right"></i></a></th>{/heading_next_cell}

	   {heading_row_end}
	   </tr>
	   <tbody class="cal-body">
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
		';
	// Get the user id
		$this->userid = $this->session->userdata('userid');
	}
	
	// Get the event counts for the days that have events
	public function get_data($year, $month) {
		$query= $this->db->select('date, COUNT(*) AS ev_count')->from('events')
		->where('user_id', $this->userid)->like('date', "$year-$month", 'after')->group_by('date')->get();
		$cal_data=array();
		foreach ($query->result() as $row) {
			$day = substr($row->date, 8, 2);
			$day[0] == 0 ? $day = $day[1] : null;
			$cal_data[$day] = $row->ev_count ;
		}
		return $cal_data;
	}

	// Generate out calendar
	public function generate($year, $month)
	{
		$cal_data = $this->get_data($year, $month);
		$this->load->library('calendar', $this->conf);
		return $this->calendar->generate($year, $month, $cal_data);
	}
	
	// Adding events
	public function add_event($date, $time, $title, $description='') {
		if($time == '') { $time= '00:00:00'; }
		if($this->validate($date, $time, $title)) {
		// Check if time is taken
			if($this->db->select('date')->from('events')->where('date', $date)->where('user_id', $this->userid)
			->where('time', $time)->count_all_results()) 
			{
				return "reserved";
			// Try to insert data
			} else {
				if( $this->db->insert('events', array(
					'date' => $date,
					'time' => $time,
					'title' => $title,
					'description' => $description,
					'user_id' => $this->userid))
						) {
						return array('result'=> 'Added', 'id'=> $this->db->insert_id() );
						} else {
							return "no_insert";
							}	
				}
		// Return error(no data given)		
		} else {
			return "no_data";
		}
	}	
	
	// Editing events
	public function edit_event($id, $date, $time, $title, $description='') {
		if($time == '') { $time= '00:00:00'; }
		if($this->validate($date, $time, $title)) {
		// Check if time is taken
			if($this->db->select('date')->from('events')->where('date', $date)->where('user_id', $this->userid)
			->where('time', $time)->where('id !=', $id)->count_all_results()) 
			{
				return "reserved";
			} else {
				if ( $this->db->where('id', $id)
				->update('events', array(
					'date' => $date,
					'time' => $time,
					'title' => $title,
					'description' => $description))
						) {
						return "edited";
						} else {
							return "no_edit";
							}
				}
		} else {
			return "no_data";
		}	
	}
	
	//Get the events for selected date
	public function get_events($date) {
		$p_date='/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
		if(preg_match($p_date, $date)) {
			$query = $this->db->select('id, date, time, title, description, done')->from('events')
			->where('date', $date)->where('user_id', $this->userid)->group_by('time')->get();
			return $query->result();
		} else {
			return false;
		}
	}
	
	// Delete the event
	public function del_event($id) {
		if(is_int(intval($id))) {
			$query = $this->db->where('user_id', $this->userid)->where('id', $id)->delete('events');
			return ($this->db->affected_rows() > 0 ? true : false); 
		} else {
			return false;
		}
	}
	
	// Mark event as done
	public function done_event($id) {
		if(is_int(intval($id))) {
			$data = array('done' => 1);
			$query = $this->db->where('user_id', $this->userid)->where('id', $id)->update('events', $data);
			return ($this->db->affected_rows() > 0 ? true : false); 
		} else {
			return false;
		}
	}
	
	// Validate event form
	private function validate($date, $time, $title) {
		$p_time='#([0-9]{1}|[0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}#';
		$p_date='/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
		if( preg_match($p_time, $time) && !empty($title) ) {
			return true;
		} else {
			return false;
		}
	}
}	