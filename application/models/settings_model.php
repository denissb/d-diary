<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_model extends CI_Model {

	// Array containing the list of available settings (yes, yes.. repeating fb permission names but can be different)
	public $settings = array('friends_events', 'friends_birthday');

	function __construct(){
        parent::__construct();
    }
	
	// Updates the settings of the current facebook user
	public function update_settings($settings, $user_id) {
		if($settings == false) {
			$this->db->set('settings', "")->where('fb_id', $user_id)->update('users');
			$this->session->unset_userdata('settings');
			return;
		}
		$settings = json_encode($settings);
		$this->db->set('settings', $settings)->where('fb_id', $user_id)->update('users');
		$this->session->set_userdata('settings', $settings);
	}

	// Returns the users setings in a php array
	public function get_settings($user_id) {
		$query = $this->db->select('settings')->from('users')->where('fb_id',$user_id)->get();
		$settings = $query->result();
		$settings = $settings[0]->settings;
		if($settings != "") {
			$settings = json_decode($settings, true);
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
		$a1 = json_decode( $this->session->userdata('settings'), true );
		$a2 = json_decode( $permissions, true );
		$permissions = array_merge_recursive( $a1, $a2 );
		$result = array();
		foreach($permissions as $key => $value) {
			if(in_array($key, $this->settings)) {
				$result[$key] = $value;
			}
		}
		return json_encode($result);
	}

}