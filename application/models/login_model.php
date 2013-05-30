<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model{
    function __construct(){
        parent::__construct();
    }
 
    public function validate(){
        // grab user input
        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));
		$remember = $this->security->xss_clean($this->input->post('rememberme'));
        return $this->login($username, $password, $remember);
    }
    
    // Logs in the specified user
    public function login($username, $password, $remember, $hashed = false) {
        // Prep the query
        $this->db->where('username', $username);
		
		if(!$hashed) 
			$this->db->where('password', $this->prep_password($password));
		else
			$this->db->where('password', $password);
			
        // Run the query
        $query = $this->db->get('users');
        // Let's check if there are any results
        if($query->num_rows() == 1)
        {
            // If there is a user, then create session data
            $row = $query->row();
			
			// if user is linked with facebook and is logged in to it
			if($row->fb_id != "") {
				$this->load->model('fb_model');
				if($this->facebook->getUser()) {
					return $this->fb_login($row->fb_id);
				}
			}
			
            $data = array(
                    'userid' => $row->id,
                    'fname' => $row->f_name,
                    'lname' => $row->l_name,
                    'username' => $row->username,
                    'validated' => true,
					'with_fb' => false,
					'language' => $row->language
                    );
            $this->session->set_userdata($data);
			// Remember user to keep them signed in
			if($remember == 'ON') {
				$this->remember($username);
			}
            return true;
        } else {
        // If the previous process did not validate
        // then return false.
        return false;
		}
    }
	
	 // Logs in the specified user
    public function fb_login($fb_id) {
        // Prep the query
		if(!$fb_id) {
			return false;
		}
        $this->db->where('fb_id', $fb_id);
        // Run the query
        $query = $this->db->get('users');
        // Let's check if there are any results
        if($query->num_rows() == 1)
        {
            // If there is a user, then create session data
            $row = $query->row();
            $data = array(
                    'userid' => $row->id,
                    'fname' => $row->f_name,
                    'lname' => $row->l_name,
                    'username' => $row->username,
					'with_fb' => true,
                    'validated' => true,
					'settings' => $row->settings,
					'language' => $row->language == "" ? $this->facebook->get_lang() : $row->language
                    );
            $this->session->set_userdata($data);
			$this->facebook->setExtendedAccessToken();
			$this->log_ip_and_date($row->id);
			// Remember user to keep them signed in
            return true;
        } 
		if($query->num_rows() == 0) 
		{
			// If the previous process did not validate
			// then register a new user
			$this->load->model('signup_model');
			if($this->signup_model->fb_register($fb_id) == "valid")
				{
					return true;
				}
		}
		return false;
    }
	
	// Generate a persistant login cookie
	private function remember($user) {
		$key = sha1(rand().$user);
		$data = array (
			'user' => $user,
			'key' => $key
		);
		//if the values were inserted give the user a cookie
		if($this->db->insert('remembered', $data)) {
		$this->input->set_cookie(
				array(
				'name' => 'Remembered_auth',
				'value'  => urlencode(serialize($data)),
				'expire' => '864000' 
				)
			);
		}
	}
	
	// Log in remembered user
	public function catch_remembered() {
		// Load the CI cookie helper
		$this->load->helper('cookie');
		// Fetch the cookie data
		if($data = get_cookie('Remembered_auth', TRUE)) {
			//Delete cookie
			delete_cookie('Remembered_auth');
			// Fetch the php array
			$data = unserialize(urldecode($data)); 
			// Prep the query
			$this->db->where('user', $data['user']);
			$this->db->where('key', $data['key']);
			// Run the query
			$query = $this->db->get('remembered');
			// Let's check if there are any results
			if($query->num_rows()) 
				{
					// Remove previously assigned cookies
					$this->db->delete('remembered', array('user' => $data['user'], 'key' => $data['key']));

					// Run the query and check
					if($query = $this->db->get_where('users', array('username' => $data['user']), 1))
					{
						// If there is a user, then create session data
						$row = $query->row();
						$data = array(
								'userid' => $row->id,
								'fname' => $row->f_name,
								'lname' => $row->l_name,
								'username' => $row->username,
								'validated' => true,
								'with_fb' => $row->fb_id ? true : false
								);
						$this->session->set_userdata($data);
						$this->log_ip_and_date($row->id);
						// Set a new cookie
						$this->remember($row->username);
						return true;
					} else {
						return false;
					}
				} else {
					return false;
			}
		}
		return false;
	}
	
	public function prep_password($password)
	{
		return sha1($password.$this->config->item('encryption_key'));
	}
	
	// Saves the users ip in a database encoded as a long integer
	public function log_ip_and_date($user_id) {
		$ip = ip2long($this->input->ip_address());
		
		$date = date('Y-m-d H:i:s', time());
		$data = array(
			'last_ip' => $ip,
			'last_date' => $date
		);
		$this->db->set($data)->where('id', $user_id)->update('users');
		
		//Updating the entries table
		$log_data['user_id'] = $user_id;
		$log_data['time'] = $data['last_date'];
		$log_data['ip'] = $data['last_ip'];
		$log_data['client'] = $this->input->user_agent();
		
		$this->db->insert('access', $log_data);
	}	
}
?>