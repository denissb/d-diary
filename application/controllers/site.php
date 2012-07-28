<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller {

	public function index()
	{
		//$this->load->view('welcome_message');
		$this->home();
	}
	
	public function home() 
	{
		$data['title'] = "Welcome!";
		$this->load->view("home", $data);
	}
	
	public function addStuff() {
		$this->load->model("math");
		echo $this->math->add(5,5);
	}
	
}

/* End of file site.php */
/* Location: ./application/controllers/site.php */