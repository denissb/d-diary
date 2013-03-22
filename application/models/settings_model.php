<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_model extends CI_Model {

	// Array containing the list of available settings (yes, yes.. repeating fb permission names for fb extensions but can be different)
	public $settings = array('friends_events', 'friends_birthday', 'publish_stream');
	public $user_settings = array('f_name', 'l_name', 'language', 'last_date', 'last_ip');
	
	function __construct(){
        parent::__construct();
    }
	
	// Updates the settings of the current facebook user
	public function update_settings($settings, $user_id) {
		if($settings == false || $settings === 'null') {
			$this->db->set('settings', "")->where('fb_id', $user_id)->update('users');
			$this->session->unset_userdata('settings');
			return;
		}
		
		if(is_array($settings))
			$settings = json_encode($settings);
		
		$this->db->set('settings', $settings)->where('fb_id', $user_id)->update('users');
		$this->session->set_userdata('settings', $settings);
	}
	
	public function update_user_settings($settings, $user_id) {
		$data = array();
		foreach($settings as $item => $value)
			{
				if(in_array($item, $this->user_settings) && $value!="") {
					$data[$item] = trim($this->security->xss_clean($value));
				}
			}
		if(!empty($data)) {
			$this->db->where('id', $user_id)->update('users', $data);
			// TD: remove set_fb_lang and some other methods to a new controler base class :| Unify naming convention between session and db varibale names
			$this->session->set_userdata(array(
                    'fname' => $data['f_name'],
                    'lname' => $data['l_name'],
					'language' => $data['language']
                    ));
			return $this->db->affected_rows() ? true : false;
		}	
	}

	// Returns the facebook setings in a php array
	public function get_settings($user_id) {
		$query = $this->db->select('settings')->from('users')->where('fb_id',$user_id)->get();
		$settings = $query->result();
		$result = json_decode(stripslashes($settings[0]->settings), true);
		if(is_array($result)) {
			return $result;
		} else if (!is_array($result)) {
				$result = json_decode($result, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					return null;
				}
				return $result;
			}
	}
	
	// Returns user setings in a php array
	public function get_user_settings($user_id) {
		$query = $this->db->select(implode("," ,$this->user_settings))->from('users')->where('id',$user_id)->get();
		$settings = $query->result();
		if(!empty($settings)) {
			$settings = $settings[0];
			return $settings;
		} 
		return null;
	}
	
	// Checks the settings 
	public function uncheck_settings(&$settings, $new_settings) {
		if($settings) {
			foreach($settings as $setting => $value) {
				if(!in_array($setting, $new_settings)) {
					unset($settings[$setting]);
					return true;
				}	
			}
			return false;
		} else {
			return true;
		}
	}
	
	// Parses an JSON object, removing permissions non existant in the application
	public function parse_permissions($permissions) {
		if(strlen($this->session->userdata('settings')) > 5) {
			$a1 = json_decode(stripslashes($this->session->userdata('settings')), true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$a1 = array();
			}
		} else {
			$a1 = array();
		}
		if(!is_array($permissions)) {
			$a2 = json_decode($permissions, true );
		} else {
			$a2 = $permissions;
		}
		$merged = array_merge( $a1, $a2 );
		
		$result = array();
		foreach($merged as $key => $value) {
			if(in_array($key, $this->settings) && array_key_exists($key, $a2)) {
				$result[$key] = $value;
			} else {
			
			}
		}	
		return json_encode($result);
	}
	
	public function change_password($old_pass, $new_pass, $new_pass_repeat, $user_id) {
		$this->load->model('login_model');
		//Check if old_pass is the users current password
		$query = $this->db->select('username')->from('users')->where('id', $user_id)
			->where('password', $this->login_model->prep_password($old_pass))->get();
		 if ($query->num_rows() != 1) {
			return json_encode(array(
				"msg" => "The current password was not valid",
				"success" => false
				));
			}			
		//Check that the new password was repeated without errors and is difficult enough
		$strength=0;
		if (strlen($new_pass) > 6) { $strength++; } else { $strength--; }
		if (preg_match("/[a-z]/", $new_pass) || preg_match("/[A-Z]/", $password)) { $strength++; }
		if (preg_match("/[0-9]/", $new_pass)) { $strength++; }
		if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $new_pass)) {$strength++;}
		if ($strength > 2) {
			if($new_pass != $new_pass_repeat) {
				return json_encode(array(
					"msg" => "Provided password was not repeated correctly!", 
					"success" => false
					));
			}
			//Update the password is no errors were found
			if($this->db->where('id', $user_id)->update('users', array(
				'password' => $this->login_model->prep_password($new_pass)
				))) {
				return json_encode(array(
					"msg" => "Password changed",
					"success" => true
					));
			}
		}
		//Otherwise return error message
		return lang('ui_validate_pass');
	}
}