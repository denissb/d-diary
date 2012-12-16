<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->set_fb_lang();
		if(!$this->input->is_ajax_request() || !$this->session->userdata('validated')) 
			{ exit('Invalid request!'); }
		$this->lang->load('calendar');	
	}
	
	public function showpopup() {
		$this->lang->load('ddiary');
		$this->load->view('ajax/add_pop');
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
					$this->output->set_output(json_encode($result));
					break;
				case 'reserved':
					$this->output->set_output(json_encode(array('result' => lang('cal_time_reserved'))));
					break;
				default:
					$this->output->set_output(json_encode(array('result' => lang('cal_error'))));
			}
		}
	}
	
	public function changedate() {
		if($this->input->post('newdate') && $this->input->post('id')) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->change_date($this->security->xss_clean($this->input->post('id')),
				$this->security->xss_clean($this->input->post('newdate')));
			$this->output->set_output($result ? 'changed' : 'Error changing event');
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
					$this->output->set_output('Changes saved!');
					break;
				case "reserved":
					$this->output->set_output('reserved');
					break;
				default:
					$this->output->set_output('error');
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
		if( !isset($day) || $day == 'null' ) {	
				$day = date('j');
				$month = date('m');
				$year = date('Y');
			} 	
		$data['month_name'] = lang('cal_'.strtolower(date("F",mktime(0,0,0,$month,1,$year)))); 
		$data['day'] = $day;
		$data['month'] = &$month;
		$data['year'] = &$year;
		$this->lang->load('ddiary');
		$this->load->model('Simplecalendar');
		if($day < 10) { $day= "0".$day; }
		$data['events'] = $this->Simplecalendar->get_events("$year-$month-$day");
		$this->load->view('ajax/day_events', $data);
	}
	
	// Delete event with post by ajax
	public function delete() {
		if( $id = $this->input->post('id') ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->del_event($id);
			$this->output->set_output($result ? 'deleted' : 'Error deleting event'); }
		else {
			$this->output->set_output('Error deleting event');
		}
	}
	
	// Mark event as done
	public function done() {
		if( $id = $this->input->post('id') ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->done_event($id);
			$this->output->set_output($result ? 'done' : 'Error marking event'); }
		else {
			$this->output->set_output('Error marking event');
		}
	}
	
	// Check if user logged in!
	private function check_isvalidated(){
        if(!$this->session->userdata('validated')){
            redirect('login');
        }
    }
	
	private function set_fb_lang() {
		$lang = $this->session->userdata('language');
		if($lang) {
			$this->config->set_item('language',$this->session->userdata('language'));
			switch ($lang){
				case "latvian":
				   $lang = "lv";
				   break;
				case "russian":
				   $lang = "ru";
				   break;   
				default:
				   $lang = "en";
				   break;
			}
			$this->config->set_item('lang_short', $lang);
		}	
	}
}	