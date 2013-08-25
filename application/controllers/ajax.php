<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->set_lang();
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
				case 'reserved':
					$this->set_json_response(array('result' => lang('cal_time_reserved')));
					break;
				case 'no_insert':
					$this->set_json_response(array('result' => lang('cal_error')));
					break;
				default:
					$this->set_json_response($result);
			}
		}
	}
	
	public function changedate() {
		if($this->input->get('newdate', true) && $this->input->get('id', true)) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->change_date($this->input->get('id', true),
				$this->input->get('newdate', true));
			$response = $result ? 'changed' : 'Error changing event';	
			$res_array = array('result' => $response);
			$this->set_json_response($res_array);
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
					$this->set_json_response(array('result' => 'Changes saved!'));
					break;
				case "reserved":
					$this->set_json_response(array('result' => 'reserved'));
					break;
				default:
					$this->set_json_response(array('result' => 'error'));
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
		if( $id = $this->input->get('id',true) ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->del_event($id);
			$response = $result ? 'deleted' : 'Error deleting event';
			$res_array = array('result' => $response);
			$this->set_json_response($res_array);
		} else {
			$this->set_json_response(array('result' => 'Error deleting event'));
		}
	}
	
	// Mark event as done
	public function done() {
		if( $id = $this->input->get('id',true) ) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->done_event($id);
			$response = $result ? 'done' : 'Error marking event';
			$res_array = array('result' => $response);
			$this->set_json_response($res_array);
		} else {
			$this->set_json_response(array('result' => 'Error marking event'));
		}
	}
	
	public function not_done() {
		if( $id = $this->input->get('id',true)) {
			$this->load->model('Simplecalendar');
			$result = $this->Simplecalendar->not_done_event($id);
			$response = $result ? 'done' : 'Error marking event';
			$res_array = array('result' => $response);
			$this->set_json_response($res_array);
		} else {
			$this->set_json_response(array('result' => 'Error marking event'));
		}
	}
	
	//Return a JSON array of access history
	public function access() {
		$this->load->model('Access_model');
		$result = $this->Access_model->get_access_history($this->session->userdata('userid'));
		$this->output->set_content_type('application/json')->set_output($result);
	}
	
	private function set_json_response($result) {
		if(is_array($result)) {
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		}
	}
	
	// Check if user logged in!
	private function check_isvalidated(){
        if(!$this->session->userdata('validated')){
            redirect('login');
        }
    }
}	