<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Google_Tokens extends CI_Model
{

	function get_all() {
		$this->db->select("key,value")->from("google_tokens");
		$q = $this->db->get();
		$arr = [];
		if (!$q) return [];
		foreach($q->result() as $r):
			$arr[$r->key] = $r->value;
		endforeach;
		
		return $arr;
	}
	
	function set_all() {
		//$this->
	}
	
	function set_access_token($token, $created, $expires_in) {
		//var_dump($token);
		//var_dump($created);
		//var_dump($expires_in);
		$this->db->trans_start();
		$this->db->update("google_tokens", array("value" => $token), array("key" => "access_token"));
		$this->db->update("google_tokens", array("value" => $created), array("key" => "created"));
		$this->db->update("google_tokens", array("value" => $expires_in), array("key" => "expires_in"));
		//var_dump($this->db->last_query());
		$q = $this->db->trans_complete();
		return $q;
	}
	
	function get_refresh_token() {
		/*$this->db->select("value")->from("google_tokens")->where("key", "refresh_token");
		$this->db->get();
		//var_dump($this->db->last_query());
		return $this->db->result();*/
	}
	
	function set_refresh_token($d) {
		/*$value = array("value", $d);
		return $this->db->update("value", $value, array("key" => "refresh_token"));
		var_dump($this->db->last_query());*/
	}
	
	function set_temp_freq_id($data) {
		return $this->db->update("google_tokens", array("value" => $data), array("key" => "temp_freq_id"));
	}
	
	function get_temp_freq_id() {
		$this->db->select("value")->from("google_tokens")->where("key", "temp_freq_id");
		$q = $this->db->get();
		$result = $q->result();
		$this->set_temp_freq_id('');
		//var_dump($result);
		return $result[0]->value;
	}
}
?>