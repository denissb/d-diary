<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Login model class 
 * Modified for use in a Simplecalendar app =)
 */
class Login_model extends CI_Model{
    function __construct(){
        parent::__construct();
    }
 
    public function validate(){
        // grab user input
        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));
		$remember = $this->security->xss_clean($this->input->post('rememberme'));
 
        // Prep the query
        $this->db->where('username', $username);
        $this->db->where('password', $this->prep_password($password));
 
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
                    'validated' => true
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
				'expire' => '172800' 
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
								'validated' => true
								);
						$this->session->set_userdata($data);
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
	
	private function prep_password($password)
	{
		return sha1($password.$this->config->item('encryption_key'));
	}
}
?>