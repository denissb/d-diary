<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct(){
        parent::__construct();
    }

	public function index()
	{
		$this->load->view('header');
		$this->load->view('login_view');
		$this->load->view('footer');
	}
	
	public function process(){
        // Load the model
        $this->load->model('login_model');
        // Validate the user can login
        $result = $this->login_model->validate();
        // Now we verify the result
        if(! $result){
            // If user did not validate, then show them login page again
            $this->index();
        }else{
            // If user did validate,
            // Send them to members area
            redirect('home');
        }
    }
	
}