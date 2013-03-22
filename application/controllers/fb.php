<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fb extends CI_Controller {

    function __construct() {
       parent::__construct();
       parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
    }
	
    function index(){
        // Try to get the user's id on Facebook
        $userId = $this->facebook->getUser();
 
        // If user is not yet authenticated, the id will be zero
        if($userId == 0){
            // Generate a login url
            $data['url'] = $this->facebook->getLoginUrl(array('scope'=>'email'));
            $this->load->view('main_index', $data);
        } else {
            // Get user's data and print it
            $user = $this->facebook->api('/me');
            print_r($user);
        }
    }
}