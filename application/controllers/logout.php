<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	function __construct() {
        parent::__construct();
	}	
	
	public function index()
	{	
		$this->session->sess_destroy();
		$this->load->helper('cookie');
		if($data = get_cookie('Remembered_auth', TRUE)) {
			$data = unserialize(urldecode($data)); 
			delete_cookie('Remembered_auth');
			$this->db->delete('remembered', array('user' => $data['user']));
		}
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");

		// Logout from facebook and revoke acces token
		if($user_id = $this->facebook->getUser()) {
			$logout_url = array( 'next' => base_url()."about/");
			$url = $this->facebook->getLogoutUrl($logout_url);
			redirect($url);
		}	
		redirect('login');
	}
	
	public function app()
	{
		$this->session->sess_destroy();
		$this->load->helper('cookie');
		if($data = get_cookie('Remembered_auth', TRUE)) {
			$data = unserialize(urldecode($data)); 
			delete_cookie('Remembered_auth');
			$this->db->delete('remembered', array('user' => $data['user']));
		}
		// Logout from facebook and revoke acces token
		if($user_id = $this->facebook->getUser()) {
			$access_token=$this->facebook->getAccessToken();
			$result = $this->facebook->api(array(
				'method' => 'auth.revokeAuthorization',
				'uid' =>$user_id,
				'access_token'=>$access_token
			));
			redirect('about');
			exit;
		}	
		redirect('login');
	}
}