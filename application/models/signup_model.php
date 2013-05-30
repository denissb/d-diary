<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Signup_model extends CI_Model {

	const NOCAPATCHA = "NOCAPATCHAFORFB";

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
		
		$capactcha = $this->security->xss_clean($this->input->post('capatcha'));
		
		$terms = $this->security->xss_clean($this->input->post('agree-terms'));

        // Check if the password and email input confirmotion succeeds
        if (($password == $password_check) && ($email == $email_check)) {
            // Perform validation
            $result = $this->validate($username, $password, $email, $terms, $capactcha);
            if ($result == "valid") {
				$code = md5(uniqid(rand().$capatcha, true));
                if ($this->db->insert('unvalidated', array(
                            'username' => $username,
                            'password' => $this->prep_password($password),
                            'email' => $email,
                            'f_name' => $f_name,
                            'l_name' => $l_name,
							'reg_ip' => ip2long($this->input->ip_address()),
							'code' => $code))
						&& $this->send_reg_email($email, $code)
                ) {
                    return "valid";
					$this->session->unset_userdata('cap_word');
                } else {
                    return "Database error";
                }
                // Return error (does not match requirements)		
            } else {
                return $result;
            }
        } else {
			$this->lang->load('ui');
            return lang('ui_validate_match');
        }
    }
	
	// Confirming the registration
	public function confirm($code) {
		$query = $this->db->from('unvalidated')->where('code', $code)->get();
		if($query->num_rows() == 1) {
			// Run confirmation
			$result = $query->row();
			if ($this->db->insert('users', array(
                        'username' => $result->username,
                        'password' => $result->password,
                        'email' => $result->email,
                        'f_name' => $result->f_name,
                        'l_name' => $result->l_name,
						'reg_date' => date('Y-m-d H:i:s'),
						'has_pswd' => true,
						'last_ip' => ip2long($this->input->ip_address())
						)) //&&
							//$this->db->where('code', $code)->delete('unvalidated')
                ) {
					return array(
						"username" => $result->username,
						"password" => $result->password,
						"remember" => false);
				} else {
					return false;
				}	
		} else {
			return false;
		}
	}
	
	//Canceling the registration
	public function cancel($code) {
		if($this->db->select('id')->from('unvalidated')->where('code', $code)->count_all_results() == 1) {
			// Run cancelation
			if($this->db->where('code', $code)->delete('unvalidated'))
				return true;
		} else {
			return false;
		}
	}
	
	// Registers a user from facebook if the user is not already present
	public function fb_register($fb_id) {
		if($this->check_fb($fb_id))
		{
			$this->load->model('fb_model');
			$user = $this->fb_model->get_user();
			$password = $this->prep_password(rand());
			$email = $this->facebook->he($user['username'])."@facebook.com";
			$result = $this->validate($user['id'], $password, $email, "Yes", self::NOCAPATCHA);
		} else {
			return "Sorry - this facebook ID is already taken";
		}
		if($result == "valid")
		{
			if ($this->db->insert('users', array(
								'username' => $user['id'],
								'password' => $password,
								'email' => $email,
								'f_name' => $this->facebook->he($user['first_name']),
								'l_name' => $this->facebook->he($user['last_name']),
								'fb_id' => $user['id'],
								'reg_date' => date('Y-m-d H:i:s'),
								'has_pswd' => false,
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
			$this->session->set_userdata('language', $this->facebook->get_lang());
			return true;
		}
		return false;
	}
	
	public function check_fb($fb_id) {
		if($this->session->userdata('username') == $fb_id)
			return true;
        return $this->db->select('fb_id')->from('users')->where('fb_id', $fb_id)->or_where('username', $fb_id)
			->count_all_results() ? false : true;
    }

    // Returns valid on execution , error string otherwise
    public function validate($username, $password, $email, $terms, $capatcha) {
        // Check email
        $email_check = filter_var($email, FILTER_VALIDATE_EMAIL);
        // Check username
        if ($this->db->select('username')->from('users')->from('unvalidated')->where('users.username', $username)
			->or_where('users.email', $email)->or_where('unvalidated.username', $username)
			->or_where('unvalidated.email', $email)->count_all_results()) {
            $username_check = false;
        } elseif(strlen($username) < 4) {
            $username_check = false;
        } else {
            $username_check = true;
        }
        //Check password strength
        $strength=0;
		if (strlen($password) > 6) { $strength++; } else { $strength--; }
		if (preg_match("/[a-z]/", $password) || preg_match("/[A-Z]/", $password)) { $strength++; }
		if (preg_match("/[0-9]/", $password)) { $strength++; }
		if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $password)) { $strength++; }
        $pass_check = ($strength<=2 ? false : true);
        
		$this->lang->load('ui');
		
		//Check for acceptance of Terms and conditions
		if($terms != "Yes")
			return lang('ui_validate_terms');
		
		if($capatcha != $this->session->userdata('cap_word') && $capatcha != self::NOCAPATCHA)
			return lang('ui_validate_capatcha');
		
        // Generate return messages
        if($email_check == false) {
            return lang('ui_validate_email');
        }
        if($pass_check == false) {
            return lang('ui_validate_pass');
        }
        if($username_check == false) {
            return lang('ui_validate_taken');
        }
        return "valid";
    }
	
    private function prep_password($password) {
        return sha1($password . $this->config->item('encryption_key'));
    }
	
	public function send_reg_email($to, $code) {
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
		$this->email->subject(lang('email_reg_subject'));
		
		$reg_confirm="<br/>".anchor("signup/confirm/".$code ,null , "title=Activate")."<br/><br/>";
		$reg_cancel="<br/>".anchor("signup/cancel/".$code ,null , "title=Cancel")."<br/>";
		
		$this->email->message(lang('email_reg_message').$reg_confirm.lang('email_reg_cancel').$reg_cancel.lang('email_reg_message_2')."<span>"
		.date("Y-m-d G:i", time() + 24*60*60)."</span>.<br/>D-diary");
		
		if($this->email->send()) {
			return true;
		} else {
			log_message('error', $this->email->print_debugger());
			return false;
		}
	}	
	
	 function create_capatcha() {
		$this->load->helper('captcha');
		
		$vals = array(
			'img_path' => './bootstrap/img/capatcha/',
			'img_url' => base_url().'bootstrap/img/capatcha/',
			'font_path' => './bootstrap/fonts/ArchitectsDaughter.ttf', 
			'img_width' => 150,
			'img_height' => 32
			);

		$cap = create_captcha($vals);
		$this->session->set_userdata('cap_word', $cap['word']);
		
		return $cap['image'];
	}
}
?>