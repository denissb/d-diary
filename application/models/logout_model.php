<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
	
	public function logout() {
		//Default logout path
		$result="login";
	
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
			$logout_url = array( 'next' => base_url("about/"));
			$url = $this->facebook->getLogoutUrl($logout_url);
			$result = $url;
		}
		
		return $result;
	}
	
	public function logout_fb() {
		//Default value
		$result="login";
	
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
			$this->db->update('users', array('fb_id' => '', 'settings' => ''), array('fb_id' => $user_id));

			$result = "about";
		}
		return $result;	
	}
	
	public function send_new_pass($to, $fb_id) {
		//$this->set_fb_lang();
		//First test if this is required at all
		if($this->db->select('has_pswd')->from('users')->where('fb_id', $fb_id)->where('has_pswd', 1)->count_all_results()) 
		{	
			return true;	
		}
		
		$this->load->helper('string');
		
		$pass= random_string('alnum', 10);
		$gen_pass = $this->prep_password($pass);
		$data=array('password' => $gen_pass, 'has_pswd' => 1);
		
		$this->db->update('users', $data, array('fb_id' => $fb_id, 'has_pswd' => false));
		
		if($this->db->affected_rows() == 1) {
			$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => SMTP_HOST,
				'smtp_port' => 465,
				'smtp_user' => SMTP_USER,
				'smtp_pass' => SMTP_PASS,
				'mailtype' => 'html',
				'charset' => 'utf-8'
			);
			$this->load->library('email', $config);  
			$this->lang->load('email');  
			
			$this->email->set_newline("\r\n"); /* for some reason it is needed */
			
			$this->email->from(SMTP_USER, SMTP_ALIAS);
			$this->email->to($to);
			$this->email->subject(lang('email_new_pass_subject'));
			
			$msg = lang('email_new_pass_message');
			
			$msg.= "<span><stong>".lang('email_new_pass_user').$fb_id."</strong></span><br/>";
			$msg.= "<span><strong>".lang('email_new_pass_pass').$pass."</strong></span><br/>";
			
			$msg.= lang('email_new_pass_message_2');
			
			$this->email->message($msg);
			
			if($this->email->send()) {
				return true;
			} else {
				$data=array('has_pswd' => 0);
				$this->db->update('users', $data, array('fb_id' => $fb_id, 'has_pswd' => true));
				log_message('error', $this->email->print_debugger());
				return false;
			}
		} 
		return false;
	}
	
	private function prep_password($password)
	{
		return sha1($password.$this->config->item('encryption_key'));
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