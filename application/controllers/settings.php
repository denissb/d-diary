<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends MY_Controller {
	
	public $need_redirect = false;
	
	function __construct() {
            parent::__construct();
			$this->check_isvalidated();
			$this->load->model('fb_model');
			$this->load->model('settings_model');
			$this->set_lang();
			$this->lang->load('ui');	
			$this->load->helper('form');
	}

	public function index($msg="") {
		// Determine the available links for the header
		if (!$this->session->userdata('with_fb')) { 
			$links[] = 	"<div id=\"fbLogin\"><span><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">".lang('ui_fb_add')."</span></a></span></div>";
			$links[] = "<a href=\"".base_url()."logout\">".lang('ui_logout')."</a></li>";
		}
		
		$links[] = 	"<a href=\"".base_url()."settings\" id=\"menu-active\">".lang('ui_settings')."</a>";        
		$links[] = "<a href=\"".base_url()."about\">".lang('ui_about')."</a>";
		
		if ($this->session->userdata('with_fb')) {
			$links[] = 	"<div id=\"fbLogout\"><span><a class=\"fb_button fb_button_medium\" href=\"".base_url()."logout\"><span class=\"fb_button_text\">".lang('ui_logout')."</span></a></span></div>";
			$data['enabled'] = $this->settings_model->get_settings($this->fb_model->user_id);
		}
		
		$attributes = array('class' => 'nav');
		$data['navlist'] = ul($links,$attributes);
		$data['settings'] = null;
		$data['user'] = $this->settings_model->get_user_settings($this->session->userdata('userid'));
		$data['fb_id'] = $this->fb_model->user_id;
		$data['msg'] = $msg;
		
		$this->load->view('header/header_cal', $data);
		$this->load->view('body/settings', $data);
		$this->load->view('footer/footer');
	}
	
	// Processing the settings change
	public function process_widgets() {
		if(count($_POST)) {
			$scope="";
			try {
			$permis = $this->fb_model->get_permissions();
			} catch ( FacebookApiException $e ) {
				$this->index("<div class='alert alert-error'>".lang('ui_error_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
				return;
			}
			$perms = $this->fb_model->get_permissions();
			$settings = $this->settings_model->get_settings($this->fb_model->user_id);
			// Checking if the user disabled something in the settings
			$unset=$this->settings_model->uncheck_settings($settings, $_POST);
			
			// Build up a string of missing permissions and adding permissions to the settings array to be updated
			foreach($_POST as $item => $value) {
				if(in_array($value, $this->settings_model->settings) && !$perms[$value]) {
					$scope.= $value .",";
				}
				if(is_array($settings)) {
					if(!array_key_exists($value, $settings)) {
						$settings[$value] = 1;
						$unset = true;
					}
				} else {
					$settings[$value] = 1;
					$unset = true;
				}
			}
			
			if($unset) {
				$this->settings_model->update_settings($settings, $this->fb_model->user_id);
			}
			
			// Request new settings from facebook
			if($scope != "") {
				$scope = substr($scope, 0, -1);
				$uri = base_url()."settings/added";
				$url = $this->fb_model->oauth_dialog($scope, $uri);	
				redirect($url);
			} else {
				$this->index("<div class='alert alert-success'>".lang('ui_success_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
			}
		} else {
			$this->settings_model->update_settings(false, $this->fb_model->user_id);
			$this->index("<div class='alert alert-success'>".lang('ui_success_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
		}
	}
	
	public function process_user() {
		if(count($_POST)) {
			if(strcmp($_POST['language'], $this->session->userdata('language')) != 0) {
				$this->need_redirect = true;
			}
			if($this->settings_model->update_user_settings($_POST, $this->session->userdata('userid'))) {
				$this->index("<div class='alert alert-success'>".lang('ui_success_settings')."
					<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");	
			}
			if($this->need_redirect)
				redirect('settings/success');
				
		} else {
			$this->index("<div class='alert alert-error'>".lang('ui_error_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
		}
	}
	
	//Ajax method
	public function change_pass() {
		if(count($_POST) && $this->session->userdata('userid')) {
			$old_pass = $this->security->xss_clean($this->input->post('old_pass'));
			$new_pass = $this->security->xss_clean($this->input->post('new_pass'));
			$new_pass_repeat = $this->security->xss_clean($this->input->post('new_pass_repeat'));
			$this->output->set_output($this->settings_model->change_password($old_pass, $new_pass, $new_pass_repeat, $this->session->userdata('userid')));
		} else {
			$this->output->set_output(lang('ui_error_settings'));
		}
	}
	
	public function added() {
		// Save new ADDED settings
		try {
			$permissions = $this->fb_model->get_permissions();
		} catch ( FacebookApiException $e ) {
			$this->index("<div class='alert alert-error'>".lang('ui_error_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
			return;	
		}
		
		$settings = $this->settings_model->parse_permissions($permissions);
		$this->settings_model->update_settings($settings, $this->fb_model->user_id);
		
		if($this->input->get('error')) {
			$this->index("<div class='alert alert-error'>".lang('ui_settings_deny')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");
		} else {
			$this->index("<div class='alert alert-success'>".lang('ui_success_settings')."
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");	
		}	
	}
	
	private function check_isvalidated(){
		if($this->facebook->getUser() && !$this->session->userdata('with_fb')) {
				$this->load->model("signup_model");
				if($this->signup_model->add_fb($this->session->userdata('userid'), $this->facebook->getUser())) {
					redirect('simplecal');
				} else {
					redirect('simplecal/error/fb_add');
				}
			}
        if(!$this->session->userdata('validated')){
            redirect('login');
		}
    }
	
	public function success() {
		$this->index("<div class='alert alert-success'>".lang('ui_success_settings')."
			<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");	
	}
}