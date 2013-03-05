<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class About extends MY_Controller {

    function __construct() {
        parent::__construct();
        // Redirect validated user to calendar
        $this->load->helper('cookie');
		$this->load->model('login_model');
		$this->set_lang();
		if (($this->session->userdata('validated') || $this->login_model->catch_remembered())
			&& $this->uri->segment(1) != "about" ) {
				redirect('simplecal');
			} 
		if($this->facebook->check_login() && $this->uri->segment(1) != "about") {
			if($this->login_model->fb_login($this->facebook->getUser())) {
				redirect('simplecal');
			}
		}
		$this->lang->load('ui');	
    }
    public function index($msg = NULL) {
		// Determine the available links for the header
		if ($this->session->userdata('validated') && !$this->session->userdata('with_fb')) { 
			$links[] = "<a href=\"".base_url()."logout\">".lang('ui_logout')."</a></li>";
		} 
		if(!$this->session->userdata('validated')) {
			$links[] = 	"<div id=\"fbLogin\"><span><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">".lang('ui_login')."</span></a></span></div>";
			$links[] =	"<a href=\"".base_url()."login\">".lang('ui_login_no_fb')."</a></li>";
			$links[] = 	"<a href=\"".base_url()."signup\">".lang('ui_signup')."</a>";
		} else {
			$links[] = 	"<a href=\"".base_url()."settings\">".lang('ui_settings')."</a>";     
		}
		$links[] = "<a href=\"".base_url()."about\" id=\"menu-active\">".lang('ui_about')."</a>";
		if ($this->session->userdata('with_fb')) {
			$links[] = 	"<div id=\"fbLogout\"><span><a class=\"fb_button fb_button_medium\" href=\"".base_url()."logout\"><span class=\"fb_button_text\">".lang('ui_logout')."</span></a></span></div>";
		}

		$attributes = array('class' => 'nav');
		
        // Load the view and pass in auth data (if such is present)
		$data['navlist'] = ul($links,$attributes);
        $this->load->view('header/header', $data);
        $this->load->view('body/about', $data);
        $this->load->view('footer/footer_public');
    }
}