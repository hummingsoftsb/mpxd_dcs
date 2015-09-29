<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class MobileApp extends CI_Model
{

	
	function __construct() {
		//$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		/*if ($this->cache->apc->is_supported())
		{
			echo "YES!";
		} else echo "NO!";*/
		
		//
		//var_dump($this->cache->get('foo'));
	}
	
	// Set session id for user
	function set_session($user_id, $session_id, $installation_id)
	{
		$now = date('Y-m-d H:i:s');
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$data = array(
			'session_id' => $session_id,
			'user_id' => $user_id,
			'last_logged_in' => $now,
			'user_agent' => $ua,
			'installation_id' => $installation_id,
			'session_valid' => 1
		);
		
		$log = array( 
			'session_id' => $session_id,
			'user_id' => $user_id,
			'last_logged_in' => $now,
			'user_agent' => $ua,
			'installation_id' => $installation_id
		);
		
		$this->db->insert('mobile_app_user_log',$log);
		//Check session exist or not (to disallow multiple login on mobile app)
		if($this->is_user_exists($user_id)) {
			//Delete previous session cache if exists
			//if ($this->cache->get($session_id)) $this->cache->delete($session_id);
			$this->db->where('user_id',$user_id);
			return $this->db->update('mobile_app_user',$data);
		} else {
			return $this->db->insert('mobile_app_user',$data);
		}
		return false;
	}
	
	function set_installation_id($session_id, $installation_id) {
		if ($this->is_session_exists($session_id)) {
			$this->db->set('installation_id', $installation_id);
			$this->db->where('session_id',$session_id);
			$q = $this->db->update('mobile_app_user');
			return $q;
		}
		return false;
	}
	
	function get_installation_id_by_user_id($user_id) {
		$r = $this->get_user_by_userid($user_id);
		if (!$r) return false;
		return $r[0]->installation_id;
	}
	
	function get_installation_id_by_session_id($session_id) {
		$r = $this->get_user_by_sessionid($session_id);
		if (!$r) return false;
		return $r[0]->installation_id;
	}
	
	
	
	function is_user_exists($user_id) {
		return sizeOf($this->get_user_by_userid($user_id)) > 0;
	}
	
	function get_user_by_userid($user_id) {
		$this->db->select('*');
		$this->db->from('mobile_app_user');
		$this->db->where('user_id', $user_id);
		$query = $this->db->get();
		return $query->result();
	}
	
	function is_session_exists($session_id) {
		return sizeOf($this->get_user_by_sessionid($session_id)) > 0;
	}
	
	function is_session_valid($session_id) {
		return ($this->get_user_by_sessionid($session_id)[0]->session_valid == 1);
	}
	
	function invalidate_session_by_user_id($user_id) {
		$this->db->where('user_id',$user_id);
		return $this->db->update('mobile_app_user',array('session_valid'=>0));
	}
	
	function invalidate_session_by_session_id($session_id) {
		$this->db->where('session_id',$session_id);
		return $this->db->update('mobile_app_user',array('session_valid'=>0));
	}
	
	function get_user_by_sessionid($session_id) {
		//$sesscache = $this->cache->get($session_id);
		//if ($sesscache) return $sesscache;
		$this->db->select('*');
		$this->db->from('mobile_app_user');
		$this->db->where('session_id', $session_id);
		$query = $this->db->get();
		return $query->result();
		//$this->cache->save($session_id,$query->result());
		//return $this->cache->get($session_id);
	}
}
?>