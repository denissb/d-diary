<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	public function index()
	{
		$this->session->sess_destroy();
		$this->load->helper('cookie');
		if($data = get_cookie('Remembered_auth', TRUE)) {
			$data = unserialize(urldecode($data)); 
			delete_cookie('Remembered_auth');
			$this->db->delete('remembered', array('user' => $data['user']));
		}
		redirect('login');
	}
}