<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct(){
        parent::__construct();
		// Load the model
        $this->load->model('login_model');
    }

	public function index($msg = NULL)
	{
		// Check if user was remembered
		if($this->login_model->catch_remembered()) {
			redirect('simplecal');
		}
		// Load the view and pass in auth data (if such is present)
		$data['msg'] = $msg;
		$this->load->view('header');
		$this->load->view('login_view', $data);
		$this->load->view('footer');
	}
	
	public function process(){
        // Validate the user can login
        $result = $this->login_model->validate();
        // Now we verify the result
        if(!$result){
            // If user did not validate, then show them login page again
			$msg ="<div class='alert alert-error'>Invalid username/password!
				<a class='close' data-dismiss='alert' href='#''>&times;</a></div>";
            $this->index($msg);
        }else{
            // If user did validate,
            // Send them to members area
            redirect('simplecal');
        }
    }
	
}