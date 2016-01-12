<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User extends CI_Model
{
//Check the user is valid or not for login
function login($username, $password)
{
	$this -> db -> select('user_id, user_name, sec_role_id, email_id, pwd_txt,lock_by_pwd,no_pwd_attempt,change_pwd_opt');
	$this -> db -> from('sec_user');
	if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $this -> db -> where('email_id', $username);
    }
    else {
        $this -> db -> where('user_name', $username);
    }
	//$this -> db -> where('email_id', $username);
	$this -> db -> where('pwd_txt', $password);
	$this -> db -> limit(1);
	$query = $this -> db -> get();
	if($query -> num_rows() == 1)
	{
		return $query->result();
	}
	else
	{
		$username=str_replace("'","''",$username);
		$this->db->query("update sec_user set no_pwd_attempt=no_pwd_attempt+1 where email_id='".$username."'");
		return false;
	}
}

//login bypass
function login_bypass($username)
{
	$this -> db -> select('user_id, user_name, sec_role_id, email_id, pwd_txt,lock_by_pwd,no_pwd_attempt,change_pwd_opt');
	$this -> db -> from('sec_user');
	if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $this -> db -> where('email_id', $username);
    }
    else {
        $this -> db -> where('user_name', $username);
    }
	//$this -> db -> where('email_id', $username);
	$this -> db -> limit(1);
	$query = $this -> db -> get();
	if($query -> num_rows() == 1)
	{
		return $query->result();
	}
	else
	{
		$username=str_replace("'","''",$username);
		$this->db->query("update sec_user set no_pwd_attempt=no_pwd_attempt+1 where email_id='".$username."'");
		return false;
	}
}

//Check the user is valid or not for forgot password
function chkuser($username, $email)
		{
	   		$this -> db -> select('user_id,user_name,user_full_name,email_id');
			$this -> db -> from('sec_user');
			if($email!="") {
			$this -> db -> where('email_id', $email);
			}
			if($username!="") {
			$this -> db -> where('user_name', $username);
			}
			$this -> db -> limit(1);
			$query = $this -> db -> get();
			if($query -> num_rows() == 1)
			{
				return $query->result();
			}
		   	else
		   	{
		     	return false;
		   	}
	}

	//Add User id for the user who requested forgot password
	function add_reset($data)
		{
			$this->db->insert('reset_password', $data);
		}

	//Reset Password for the user who requested forgot password
	function pass_reset($data,$id)
		{
			$this->db->set('pwd_txt', $data);
			$this->db->set('no_pwd_attempt', '0');
			$this->db->where('user_id', $id);
			$this->db->update('sec_user');

			//delete hashcode entry from reset password table
			$this->db->where('user_id', $id);
   			$this->db->delete('reset_password');
		}

		//Delete Password Reset hashcode entry
			function pass_reset_del($id)
				{
					$this->db->set('pwd_txt', $data);
					$this->db->set('no_pwd_attempt', '0');
					$this->db->where('user_id', $id);
					$this->db->update('sec_user');
		}

	//Function to verify hash code for reset password
	function chkcode($vcode)
	{
   		$this -> db -> select('user_id');
		$this -> db -> from('reset_password');
		$this -> db -> where('hash_key', $vcode);
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query -> num_rows() == 1)
		{
			return $query->result();
		}
	   	else
	   	{
	     	return false;
	   	}
	}

	//Function to display permission data for each user
	function show_permission_data($roleid)
	{
	$query ="select a.sec_group_desc,b.sec_obj_id,b.sec_obj_desc,b.url_path,b.icon_path,c.view_opt from sec_group a,sec_object b,sec_role_permission c where a.sec_group_id=b.sec_group_id and c.sec_role_id=".$roleid." and c.view_opt=1 and b.sec_obj_id=c.sec_obj_id order by a.seq_no asc, b.seq_no asc";
	$query = $this->db->query($query);
	$query_result = $query->result();
	return $query_result;
	}

	//Function to get the url path to redirect the user
	function get_role_url_path($roleid)
	{
        $query = "SELECT sec_role_url_path FROM sec_role WHERE sec_role_id = $roleid";
        $row =  $this->db->query($query)->row();
        if(!empty($row->sec_role_url_path)){
            return $row->sec_role_url_path;
        } else {
            return false;
        }
	}
	
	
	
}
?>