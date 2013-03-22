<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	protected function set_lang() {
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