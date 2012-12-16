<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct(){
        parent::__construct();
		// Load the model
        $this->load->model('login_model');
		// Try to login if facebook auth detected
		$this->lang->load('ui');
    }

	public function index($msg = NULL)
	{
		// Check if user was remembered
		if($this->login_model->catch_remembered()) {
			redirect('simplecal');
		}
		$user = $this->facebook->getUser();
		if($user) {
			if($this->login_model->fb_login($user)) {
				redirect('simplecal');
			}
		}	
		
		$links[] = 	"<div id=\"fbLogin\"><span><a class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">Login</span></a></span></div>";
		$links[] =	"<a href=\"".base_url()."login\" id=\"menu-active\">".lang('ui_login_no_fb')."</a></li>";
		$links[] = 	"<a href=\"".base_url()."signup\">".lang('ui_signup')."</a>";
		
		$links[] = "<a href=\"".base_url()."about\">".lang('ui_about')."</a>";
		$attributes = array('class' => 'nav');
		
		// Load the view and pass in auth data (if such is present)
		$data['msg'] = $msg;
		$data['navlist'] = ul($links,$attributes);
		$this->load->view('header/header', $data);
		$this->load->view('body/login_view', $data);
		$this->load->view('footer/footer_public');
	}
	
	public function process(){
        // Validate the user
        $result = $this->login_model->validate();
        // Now we verify the result
        if(!$result){
            // If user did not validate, then show them login page again
			$msg ="<div class='alert alert-error'>Invalid username/password!
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>";
            $this->index($msg);
        }else{
            // Send them to members area
            redirect('simplecal');
        }
    }	
}