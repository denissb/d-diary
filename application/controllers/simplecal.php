<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simplecal extends CI_Controller {
	
	public $settings = array();
	
	function __construct() {
        parent::__construct();
        $this->check_isvalidated();
		$this->settings = $this->session->userdata('settings');
	}

	public function index()
	{	
		$this->show();
		$me = $this->facebook->api('/me');
		$locale = $me;
		var_dump($locale);
	}

	public function show($year='', $month='') {
		// Determine the available links for the header
		if (!$this->session->userdata('with_fb')) { 
			$links[] = 	"<div id=\"fbLogin\"><span><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">Add facebook</span></a></span></div>";
			$links[] = "<a href=\"".base_url()."logout\">Logout</a></li>";
		}
		$links[] = 	"<a href=\"".base_url()."settings\">Settings</a>";        
		$links[] = "<a href=\"".base_url()."about\">About</a>";
		if ($this->session->userdata('with_fb')) {
			$links[] = 	"<div id=\"fbLogout\"><span><a class=\"fb_button fb_button_medium\" href=\"".base_url()."logout\"><span class=\"fb_button_text\">Logout</span></a></span></div>";
		}
		$attributes = array('class' => 'nav');
		$data['navlist'] = ul($links,$attributes);
	
		if(!$year) { $year = date('Y'); }
		if(!$month) { $month = date('m'); }
	
		$this->load->model('Simplecalendar');
		// $this->Simplecalendar->add_event('2012-07-09', '14:30', 'Zobu higienists', '20Ls');
		$data['settings'] = $this->settings;
		$data['calendar'] = $this->Simplecalendar->generate($year, $month);
		
		// Load the views
		$this->load->view('header/header_cal', $data);
		$this->load->view('body/simplecal', $data);
		$this->load->view('footer/footer');
	}
	
	// Check if user logged in!
	private function check_isvalidated(){
		if($this->facebook->getUser()) {
			if(!$this->session->userdata('with_fb')) {
				$this->load->model("signup_model");
				if($this->signup_model->add_fb($this->session->userdata('userid'), $this->facebook->getUser())) {
					redirect('simplecal');
				} else {
					redirect('simplecal/error/fb_add');
				}
			}
		}
        if(!$this->session->userdata('validated')){
            redirect('login');
		}
    }
}