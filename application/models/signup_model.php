<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Signup_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Returns valid if sucessful, error message string otherwise
    public function register() {
        // grab user input for the register form
        $username = $this->security->xss_clean($this->input->post('username'));

        $password = $this->security->xss_clean($this->input->post('password'));
        $password_check = $this->security->xss_clean($this->input->post('password-confirm'));

        $email = $this->security->xss_clean($this->input->post('email'));
        $email_check = $this->security->xss_clean($this->input->post('email-confirm'));

        $l_name = $this->security->xss_clean($this->input->post('l_name'));
        $f_name = $this->security->xss_clean($this->input->post('f_name'));

        // Check if the password and email input confirmotion succeeds
        if (($password == $password_check) && ($email == $email_check)) {
            // Perform validation
            $result = $this->validate($username, $password, $email);
            if ($result == "valid") {
                if ($this->db->insert('users', array(
                            'username' => $username,
                            'password' => $this->prep_password($password),
                            'email' => $email,
                            'f_name' => $f_name,
                            'l_name' => $l_name,
							'last_ip' => ip2long($this->input->ip_address())))
                ) {
                    return "valid";
                } else {
                    return "Database error";
                }
                // Return error (does not match requirements)		
            } else {
                return $result;
            }
        } else {
            return "The password or email combinations do not match!";
        }
    }
	
	// Registers a user from facebook if the user is not already present
	public function fb_register($fb_id) {	
		$this->load->model('fb_model');
		$user = $this->fb_model->get_user();
		$password = $this->prep_password(rand());
		$email = $this->facebook->he($user['username'])."@facebook.com";
		$result = $this->validate($user['id'], $password, $email);
		if($result == "valid" && $this->check_fb($fb_id))
		{
			if ($this->db->insert('users', array(
								'username' => $user['id'],
								'password' => $password,
								'email' => $email,
								'f_name' => $this->facebook->he($user['first_name']),
								'l_name' => $this->facebook->he($user['last_name']),
								'fb_id' => $user['id'],
								'last_ip' => ip2long($this->input->ip_address())))
					) {
						// Set the data here to avoid dependancy injection..
						$data = array(
						'userid' => $this->db->insert_id() ,
						'fname' => $this->facebook->he($user['first_name']),
						'lname' => $this->facebook->he($user['last_name']),
						'username' => $user['id'],
						'with_fb' => true,
						'validated' => true,
						);
						$this->session->set_userdata($data);
						$this->facebook->setExtendedAccessToken();
						return "valid";
					} else {
						return "Database error";
					}
		} else {
			return "Sorry - we have a database error";
		}
	}
	
	public function add_fb($u_id, $fb_id) {
		if($this->check_fb($fb_id))
		{
			$this->db->set('fb_id', $fb_id);
			$this->db->where('id', $u_id);
			try
			{
			$this->db->update('users'); 
			} catch (Exception $e) {
				return false;
			}
			$this->facebook->setExtendedAccessToken();
			$this->session->set_userdata('with_fb', true);
			return true;
		}
		return false;
	}
	
	public function check_fb($fb_id) {
        return $this->db->select('fb_id')->from('users')->where('fb_id', $fb_id)->count_all_results() ? false : true;
    }

    // Returns valid on execution , error string otherwise
    public function validate($username, $password, $email) {
        // Check email
        $email_check = filter_var($email, FILTER_VALIDATE_EMAIL);
        // Check username
        if ($this->db->select('username')->from('users')->where('username', $username)
			->or_where('email', $email)->count_all_results()) {
            $username_check = false;
        } elseif(strlen($username) < 4) {
            $username_check = false;
        } else {
            $username_check = true;
        }
        //Check password strength
        $strength=0;
		if (strlen($password) > 6) { $strength++; }
		if (preg_match("/[a-z]/", $password) || preg_match("/[A-Z]/", $password)) { $strength++; }
		if (preg_match("/[0-9]/", $password)) { $strength++; }
		if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $password)) { $strength++; }
        $pass_check = ($strength<=2 ? false : true);
        
        // Generate return messages
        if($email_check == false) {
            return "The email adress you provided is not valid";
        }
        if($pass_check == false) {
            return "Your password needs to be more complex";
        }
        if($username_check == false) {
            return "Provided username or email is already taken";
        }
        return "valid";
    }
	

    private function prep_password($password) {
        return sha1($password . $this->config->item('encryption_key'));
    }
}
?>