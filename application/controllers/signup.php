<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Signup extends MY_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model('signup_model');
		$this->set_lang();
		$this->lang->load('ui');	
    }

    public function index($msg = NULL) {		
        // Load the view and pass in auth data (if such is present)
		$navi['navlist'] = $this->load_navi();
        $data['msg'] = $msg;
		
		$data['capatcha'] = $this->signup_model->create_capatcha();
		
        $this->load->view('header/header', $navi);
        $this->load->view('body/signup', $data);
        $this->load->view('footer/footer_public');
    }
	
	public function validate() {
		$username = $this->security->xss_clean($this->input->post('username'));
		$password = $this->security->xss_clean($this->input->post('password'));
		$email = $this->security->xss_clean($this->input->post('email'));
		$terms = $this->security->xss_clean($this->input->post('terms'));
		$capatcha = $this->security->xss_clean($this->input->post('capatcha'));
		$result = $this->signup_model->validate($username, $password, $email, $terms, $capatcha);
		$this->output->set_output($result);
	}

    public function process() {
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
		$data['navlist'] = $this->load_navi();
        // Load the view and pass in auth data (if such is present)
        $data['msg'] = $msg;
        $data['user'] = $this->session->userdata('username');
        $this->load->view('header/header',$data);
        $this->load->view('body/signup_done', $data);
        $this->load->view('footer/footer_public');
    }
	
	public function capatcha() {
		if(!$this->input->is_ajax_request())
			echo "This is not a valid AJAX request!";
		$this->output->set_output($this->signup_model->create_capatcha());
	}
	
	public function confirm($code) {
		$result = $this->signup_model->confirm($code);
		
		if($result) {
			// Succesfull confirmation
			$data['success'] = lang('ui_reg_confirm_success');
			$data['result'] = lang('ui_reg_confirm_result_success');
			
			$this->load->model('login_model');
			
			if($this->login_model->login($result['username'], $result['password'], $result['remember'], true)) {
				$data['link'] = base_url('simplecal');
			}
			
		} else {
			//Confirmation unsuccesfull
			$data['success'] = lang('ui_reg_no_acc');
			$data['result'] = lang('ui_reg_please_reg');
		}
		
		$navi['navlist'] = $this->load_navi();
		$this->load->view('header/header', $navi);
		$this->load->view('body/signup_confirm', $data);
		$this->load->view('footer/footer_public');
	}
	
	public function cancel($code) {	
		$result = $this->signup_model->cancel($code);
		
		if($result) {
			// Succesfull delition
			$data['success'] = lang('ui_reg_cancel_success');
		} else {
			$data['success'] = lang('ui_reg_cancel_fail');
		}
		
		$data['result'] = lang('ui_reg_please_reg');
		
		$navi['navlist'] = $this->load_navi();
		$this->load->view('header/header', $navi);
		$this->load->view('body/signup_confirm', $data);
		$this->load->view('footer/footer_public');
	}
	
	public function error($type) {
		switch($type) {
			case "fb_add":
				$data['success'] = lang('ui_reg_fb_in_use');
				$data['result'] = lang('ui_reg_fb_in_use_res');
			break;
			default:
				$data['success'] = "An error accured!";
				$data['result'] = "Something went wrong, nobody's perfect :)";
		}
		$navi['navlist'] = $this->load_navi();
		$this->load->view('header/header', $navi);
		$this->load->view('body/signup_confirm', $data);
		$this->load->view('footer/footer_public');
	}
	
	private function load_navi() {
		// Determine the available links for the header
		if(!$this->session->userdata('validated')) {
			$links[] = 	"<div style=\"padding-top: 8px;\"><span id=\"fbLogin\"><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">".lang('ui_login')."</span></a></span></div>";
			$links[] =	"<a href=\"".base_url()."login\">".lang('ui_login_no_fb')."</a></li>";
			$links[] = 	"<a href=\"".base_url()."signup\" id=\"menu-active\">".lang('ui_signup')."</a>";
		} 
		$links[] = "<a href=\"".base_url()."about\">".lang('ui_about')."</a>";
		if ($this->session->userdata('validated') && !$this->session->userdata('with_fb')) {
			$links[] = 	"<a href=\"".base_url()."settings\" id=\"menu-active\">".lang('ui_settings')."</a>";   
			$links[] = "<a href=\"".base_url()."logout\">".lang('ui_logout')."</a></li>";
		}
		if ($this->session->userdata('with_fb')) {
			$links[] = 	"<a href=\"".base_url()."settings\" id=\"menu-active\">".lang('ui_settings')."</a>";
			$links[] = 	"<div id=\"fbLogout\"><span><a class=\"fb_button fb_button_medium\" href=\"".base_url()."logout\"><span class=\"fb_button_text\">".lang('ui_logout')."</span></a></span></div>";
		}
		$attributes = array('class' => 'nav');
		return ul($links,$attributes);
	}

}