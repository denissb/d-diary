<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fb_model extends CI_Model {

	public $user_id;

    function __construct(){
        parent::__construct();
		$this->user_id = $this->facebook->getUser();
    }

	// Returns the basic information about the user	in a string array/s
	public function get_user() {
		try {
			// Fetch the viewer's basic information
			$basic = $this->facebook->api('/'.$this->user_id);
		} catch (FacebookApiException $e) {
			  return false;
		}
		return $basic;	
	} 
	
	public function oauth_dialog($scope="", $redirect_uri="") {
		$params = array (
			'scope' => $scope,
			'redirect_uri' => $redirect_uri
		);
		$url = $this->facebook->getLoginUrl($params);
		return $url;
	}
	
	public function get_app_token($scope="client_credentials") {
		$app_token_url = "https://graph.facebook.com/oauth/access_token?"
        ."client_id=".$this->config->item('appId')
        ."&client_secret=". $this->config->item('secret')
        ."&grant_type=".$scope;
        $response = file_get_contents($app_token_url);
		$params = null;
		parse_str($response, $params);
		return $params['access_token'];
	}
	
	public function get_permissions() {
		$full_array = $this->facebook->api('/'.$this->user_id.'/permissions');
		$array = $this->facebook->idx($full_array, 'data');
		return $array[0];
	}
	
}
?>