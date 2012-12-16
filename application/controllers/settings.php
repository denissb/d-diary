<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller {
	
	function __construct() {
            parent::__construct();
			$this->check_isvalidated();
			$this->load->model('fb_model');
			$this->load->model('settings_model');
			$this->set_fb_lang();
			$this->lang->load('ui');
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
		$data['msg'] = $msg;
		$data['navlist'] = ul($links,$attributes);
		$data['settings'] = null;
		$this->load->view('header/header_cal', $data);
		$this->load->view('body/settings', $data);
		$this->load->view('footer/footer');
	}
	
	// Processing the settings change
	public function process() {
		if(count($_POST)) {
			$scope="";
			$perms = $this->fb_model->get_permissions();
			$settings = $this->settings_model->get_settings($this->fb_model->user_id);
			// Checking if the user disabled something in the settings
			$unset=$this->settings_model->uncheck_settings($settings, $_POST);
			
			// Build up a string of missing permissions and add permissions to the settings array to be updated
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
				redirect('settings');	
			}
		} else {
			$this->settings_model->update_settings(false, $this->fb_model->user_id);
			redirect('settings');	
		}
	}
	
	public function added() {
		// Save new ADDED settings
		$permissions = $this->fb_model->get_permissions();
		$settings = $this->settings_model->parse_permissions($permissions);
		$this->settings_model->update_settings($settings, $this->fb_model->user_id);
		$this->index("<div class='alert alert-success'>Settings applied
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>");	
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