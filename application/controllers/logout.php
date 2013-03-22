<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	function __construct() {
        parent::__construct();
		$this->load->model('logout_model');
	}	
	
	public function index()
	{	
		redirect($this->logout_model->logout());
	}
	
	public function app($type="")
	{	
		// Retreve the email adress of the user
		if($type == "unlink") {
			$query = $this->db->distinct()->select('email')->from('users')
				->where('id', $this->session->userdata('userid'))->get();
			$row = $query->result();
			$email = $row[0]->email;
			if($this->logout_model->send_new_pass($email, $this->facebook->getUser()))
				{
					redirect($this->logout_model->logout_fb());
				} else {
					redirect('settings');
				}
		}
		redirect($this->logout_model->logout_fb());
	}
}