<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Simplecalendar extends CI_Model {

    const TABLE_NAME = 'events';
    private $conf;
    private $userid;
    private $table_num;
    
    public $table_name;

    function __construct() {
        parent::__construct();
        // Calendar config
        $this->conf = array(
            'start_day' => 'monday',
            'show_next_prev' => TRUE,
            'next_prev_url' => base_url('simplecal/show')
        );
        //Calendar template
        $this->conf['template'] = $this->load->file('application/views/templates/simplecalendar.php', true);
        // Get the user id
        $this->userid = $this->session->userdata('userid');
        $table_num = $this->session->userdata('table_num');
        if($table_num) {
            $this->table_name = self::TABLE_NAME."_".$table_num;
        } else {
            die();
        }    
    }

    // Get the event counts for the days that have events
    public function get_data($year, $month) {
        $query = $this->db->select('date, COUNT(*) AS ev_count')->from($this->table_name)
                        ->where('user_id', $this->userid)->like('date', "$year-$month", 'after')->group_by('date')->get();
        $cal_data = array();
        foreach ($query->result() as $row) {
            $day = substr($row->date, 8, 2);
            $day[0] == 0 ? $day = $day[1] : null;
            $cal_data[$day] = $row->ev_count;
        }
        return $cal_data;
    }

    // Generate out calendar
    public function generate($year, $month) {
        $cal_data = $this->get_data($year, $month);
        $this->load->library('calendar', $this->conf);
        return $this->calendar->generate($year, $month, $cal_data);
    }

    // Adding events
    public function add_event($date, $time, $title, $description = '') {
        if ($time == '') {
            $time = '00:00:00';
        }
        if ($this->validate($date, $time, $title)) {
            // Check if time is taken
            if ($this->db->select('date')->from($this->table_name)->where('user_id', $this->userid)->where('date', $date)
                            ->where('time', $time)->count_all_results()) {
                return "reserved";
                // Try to insert data
            } else {
                if ($this->db->insert($this->table_name, array(
                            'date' => $date,
                            'time' => $time,
                            'title' => $title,
                            'description' => trim($description),
                            'user_id' => $this->userid))
                ) {
                    return array('result' => 'Added', 'id' => $this->db->insert_id());
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
    public function edit_event($id, $date, $time, $title, $description = '') {
        if ($time == '') {
            $time = '00:00:00';
        }
        if ($this->validate($date, $time, $title)) {
            // Check if time is taken
            if ($this->db->select('date')->from($this->table_name)->where('user_id', $this->userid)->where('date', $date)
                            ->where('time', $time)->where('id !=', $id)->count_all_results()) {
                return "reserved";
            } else {
                if ($this->db->where('id', $id)->where('user_id', $this->userid)
                                ->update($this->table_name, array(
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
        $p_date = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
        if (preg_match($p_date, $date)) {
            $query = $this->db->select('id, date, time, title, description, done')->from($this->table_name)
                            ->where('date', $date)->where('user_id', $this->userid)->group_by('time')->get();
            return $query->result();
        } else {
            return false;
        }
    }

    // Delete the event
    public function del_event($id) {
        if (is_int(intval($id))) {
            $query = $this->db->where('user_id', $this->userid)->where('id', $id)->delete($this->table_name);
            return ($this->db->affected_rows() > 0 ? true : false);
        } else {
            return false;
        }
    }

	public function change_date($id, $new_date) {
		 if (is_int(intval($id)) && isset($new_date)) {
			$data = array('date' => $new_date);
            $query = $this->db->where('user_id', $this->userid)->where('id', $id)->update($this->table_name, $data);
            return ($this->db->affected_rows() > 0 ? true : false);
        } else {
            return false;
        }
	}

    // Mark event as done
    public function done_event($id) {
        if (is_int(intval($id))) {
            $data = array('done' => 1);
            $query = $this->db->where('user_id', $this->userid)->where('id', $id)->update($this->table_name, $data);
            return ($this->db->affected_rows() > 0 ? true : false);
        } else {
            return false;
        }
    }
	
	// Mark event as not done
    public function not_done_event($id) {
        if (is_int(intval($id))) {
            $data = array('done' => 0);
            $query = $this->db->where('user_id', $this->userid)->where('id', $id)->update($this->table_name, $data);
            return ($this->db->affected_rows() > 0 ? true : false);
        } else {
            return false;
        }
    }

    // Validate event form
    private function validate($date, $time, $title) {
        $p_time = '#([0-9]{1}|[0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}#';
        $p_date = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
        if (preg_match($p_time, $time) && !empty($title)) {
            return true;
        } else {
            return false;
        }
    }

}