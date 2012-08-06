<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->check_isvalidated();
		if(!$this->input->is_ajax_request()) { exit('This is not an ajax request! :)'); }
	}
	
	public function showpopup() {
		$this->load->view('add_pop');
	}
	
	//Add the event with AJAX
	public function addevent($year='', $month='') {
		if(empty($year)) { $year = date('Y'); }
		if(empty($month)) { $month = date('m'); }
		
		if(!is_numeric($year) || !is_numeric($month)) {
			exit('Invalid parameters!');
			}	
		
		if($day = $this->input->post('day')) {
			// load the model
			$this->load->model('Simplecalendar');
			//Add the event to DB
			$result = $this->Simplecalendar->add_event(
				"$year-$month-$day",
				$this->security->xss_clean($this->input->post('time')),
				$this->security->xss_clean($this->input->post('event')),
				$this->security->xss_clean($this->input->post('description'))
			);
			
			// Possible outcomes (returns json cuz i needed 2 parameters)
			switch($result) {
				case is_array($result):
					echo json_encode($result);
					break;
				case 'reserved':
					echo json_encode(array('result' => 'This time is already reserved'));
					break;
				default:
					echo json_encode(array('result' => 'Error'));
			}
		}
	}
	
	//Edit the event with AJAX
	public function editevent($year='', $month='') {
		if(empty($year)) { $year = date('Y'); }
		if(empty($month)) { $month = date('m'); }
		
		if(!is_numeric($year) || !is_numeric($month)) {
			exit('Invalid parameters!');
			}	
		
		if($id = $this->input->post('id') && 
			$day = $this->input->post('day')) {
			// load the model
			$this->load->model('Simplecalendar');
			//Add the event to DB
			$result = $this->Simplecalendar->edit_event(
				$this->security->xss_clean($this->input->post('id')),
				"$year-$month-$day",
				$this->security->xss_clean($this->input->post('time')),
				$this->security->xss_clean($this->input->post('data')),
				$this->security->xss_clean($this->input->post('description'))
			);
			
			// Possible outcomes
			switch($result) {
				case "edited":
					echo 'Changes saved!';
					break;
				case "reserved":
					echo 'reserved';
					break;
				default:
					echo 'error';
			}
		}
	}
	
	// Load in the events for the day
	public function events($year='',$month='') {
		if(empty($year)) { $year = date('Y'); }
		if(empty($month)) { $month = date('m'); }
		
		if(!is_numeric($year) || !is_numeric($month)) {
			exit('Invalid parameters!');
			}
		$day = $this->input->post('day');
		if( empty($day) || $day == 'null' ) {	
				$day = date('d');
				$month = date('m');
				$year = date('Y');
			}
			
		$data['month_name'] = date("F");
		$data['day'] = $day;
		$data['year'] = &$year;
		
		$this->load->model('Simplecalendar');
		if($day < 10) { $day = '0'.$day; }
		$data['events'] = $this->Simplecalendar->get_events("$year-$month-$day");
		$this->load->view('day_events', $data);
		
	}
	
	// Delete event with post by ajax
	public function delete() {
		if( $id = $this->input->post('id') ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->del_event($id);
			echo ($result ? 'deleted' : 'Error deleting event'); }
		else {
			echo 'Error deleting event';
		}
	}
	
	// Mark event as done
	public function done() {
		if( $id = $this->input->post('id') ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->done_event($id);
			echo ($result ? 'done' : 'Error marking event'); }
		else {
			echo 'Error marking event';
		}
	}
	
	// Check if user logged in!
	private function check_isvalidated(){
        if(! $this->session->userdata('validated')){
            redirect('login');
        }
    }
}	