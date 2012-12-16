<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Signup extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->set_fb_lang()
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
			$links[] = 	"<a href=\"".base_url()."signup\" id=\"menu-active\">".lang('ui_signup')."</a>";
		}                   
		$links[] = "<a href=\"".base_url()."about\">".lang('ui_about')."</a>";
		$attributes = array('class' => 'nav');
		
        // Load the view and pass in auth data (if such is present)
        $data['msg'] = $msg;
		$data['navlist'] = ul($links,$attributes);
        $this->load->view('header/header', $data);
        $this->load->view('body/signup', $data);
        $this->load->view('footer/footer_public');
    }
	
	public function validate() {
		$this->load->model('signup_model');
		$username = $this->security->xss_clean($this->input->post('username'));
		$password = $this->security->xss_clean($this->input->post('password'));
		$email = $this->security->xss_clean($this->input->post('email'));
		$result = $this->signup_model->validate($username, $password, $email);
		echo $result;
	}

    public function process() {
        $this->load->model('signup_model');
        $this->load->model('login_model');
        // Validate the user can login
        $result = $this->signup_model->register();
        // Now we verify the result
        if ($result!=="valid") {
            // If user did not validate, then show them login page again
            $msg = "<div class='alert alert-error'>".$result.
			"<a class='close' data-dismiss='alert' href='#''>&times;</a></div>";
            $this->index($msg);
        } else {
            // If user did validate,
            // Send them to members area
            $this->login_model->validate();
            $this->session->set_flashdata('new', true);
            redirect('signup/done');
        }
    }
    
    public function done($msg = NULL) {
        if(!$this->session->flashdata('new')) {
            redirect();
        }
		// Determine the available links for the header
		if ($this->session->userdata('validated') && !$this->session->userdata('with_fb')) { 
			$links[] = "<a href=\"".base_url()."logout\">".lang('ui_logout')."</a></li>";
		} 
		if(!$this->session->userdata('validated')) {
			$links[] = 	"<div style=\"padding-top: 8px;\"><span id=\"fbLogin\"><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">".lang('ui_login')."</span></a></span></div>";
			$links[] =	"<a href=\"".base_url()."login\">".lang('ui_login_no_fb')."</a></li>";
			$links[] = 	"<a href=\"".base_url()."signup\">".lang('ui_signup')."</a>";
		}                   
		$links[] = "<a href=\"".base_url()."about\">".lang('ui_about')."</a>";
		$attributes = array('class' => 'nav');
		$data['navlist'] = ul($links,$attributes);
        // Load the view and pass in auth data (if such is present)
        $data['msg'] = $msg;
        $data['user'] = $this->session->userdata('username');
        $this->load->view('header',$data);
        $this->load->view('signup_done', $data);
        $this->load->view('footer_public');
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