<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simplecal extends CI_Controller {

	public function index()
	{
		$this->show();
	}

	public function show($year='', $month='') {
		if(!$year) { $year = date('Y'); }
		if(!$month) { $month = date('m'); }
	
		$this->load->model('Simplecalendar');
		// $this->Simplecalendar->add_event('2012-07-09', '14:30', 'Zobu higienists', '20Ls');
		
		$data['calendar'] = $this->Simplecalendar->generate($year, $month);
		
		// Load the views
		$this->load->view('header');
		$this->load->view('simplecal', $data);
		$this->load->view('footer');
	}
	
}