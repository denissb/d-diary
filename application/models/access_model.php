<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
	
	public function get_access_history($user_id) {
		$query = $this->db->limit(30)->where('user_id', $user_id)->order_by("time", "desc")->get('access');
		if($query->num_rows() > 0) {
			$result = $query->result();
			foreach($result as $item) {
				$item->ip = long2ip($item->ip);
			}
			return json_encode($result);
		}
		
		return json_encode(array('message' => 'ERROR', code => 'db_access_history_fail'));
	}
}	