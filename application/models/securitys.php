<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Securitys extends CI_Model
{
	// Function To Fetch All Group Record
	function show_group()
	{
		$query = $this->db->get('sec_group');
		$query_result = $query->result();
		return $query_result;
	}

	// Function to fetch total number of Object
	function totalobject($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT sec_obj_id FROM  sec_object,sec_group where sec_object.sec_group_id=sec_group.sec_group_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(sec_obj_desc) like '%".$data."%' ";
			if($data=="screen")
				$query .=" or sec_obj_type=1 ";
			if($data=="report")
				$query .=" or sec_obj_type=2 ";
			$query .=" or lower(sec_group_desc) like '%".$data."%' ";
			$query .=" )";
		}
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	//Function to Fetch All Object Record
	function show_objects()
	{
		$query = $this->db->get('sec_object');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All Object Record
	function show_object($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT sec_obj_id,sec_group_desc as sec_group,sec_obj_desc,sec_obj_type,sec_object.sec_group_id FROM  sec_object,sec_group where sec_object.sec_group_id=sec_group.sec_group_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(sec_obj_desc) like '%".$data."%' ";
			if($data=="screen")
				$query .=" or sec_obj_type=1 ";
			if($data=="report")
				$query .=" or sec_obj_type=2 ";
			$query .=" or lower(sec_group_desc) like '%".$data."%' ";
			$query .=" )";
		}
		$query .=" order by sec_group.sec_group_desc asc,sec_object.seq_no asc ";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}

	// Update Check Query For Selected object
	function update_check_object($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select sec_obj_id from sec_object where sec_obj_id!=$id and sec_obj_desc='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected object
	function update_object($id,$data)
	{
		$this->db->where('sec_obj_id', $id);
		$this->db->update('sec_object', $data);
	}

	// Function to fetch total number of Label
	function totallabel($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT sec_label_id FROM  sec_label,sec_object,sec_group where sec_label.sec_obj_id=sec_object.sec_obj_id and  sec_label.sec_group_id=sec_group.sec_group_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(sec_obj_desc) like '%".$data."%' ";
			$query .=" or lower(sec_label_desc) like '%".$data."%' ";
			if($data=="screen")
				$query .=" or sec_obj_type=1 ";
			if($data=="report")
				$query .=" or sec_obj_type=2 ";
			$query .=" or lower(sec_group_desc) like '%".$data."%' ";
			$query .=" )";
		}
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	//Function to Fetch All Label Record
	function show_labels()
	{
		$query = $this->db->get('sec_label');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All Label Record
	function show_label($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT sec_label_id,sec_label_desc,sec_group_desc as sec_group,sec_object.sec_obj_id,sec_group.sec_group_id as sec_group_id,sec_obj_desc FROM  sec_label,sec_object,sec_group where sec_label.sec_obj_id=sec_object.sec_obj_id and  sec_label.sec_group_id=sec_group.sec_group_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(sec_obj_desc) like '%".$data."%' ";
			$query .=" or lower(sec_label_desc) like '%".$data."%' ";
			if($data=="screen")
				$query .=" or sec_obj_type=1 ";
			if($data=="report")
				$query .=" or sec_obj_type=2 ";
			$query .=" or lower(sec_group_desc) like '%".$data."%' ";
			$query .=" )";
		}
		$query .=" order by sec_group.sec_group_desc asc,sec_object.seq_no asc,sec_label.seq_no asc ";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}

	// Update Check Query For Selected Label
	function update_check_label($id,$data,$objid)
	{
		$data=str_replace("'","''",$data);
		$objid=str_replace("'","''",$objid);
		$query=$this->db->query("select sec_label_id from sec_label where sec_label_id!=$id and sec_obj_id='$objid' and sec_label_desc='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Label
	function update_label($id,$data)
	{
		$this->db->where('sec_label_id', $id);
		$this->db->update('sec_label', $data);
	}

	//Select Query For Selected Label
	function get_label($objid)
	{
		$this->db->where('sec_obj_id', $objid);
		$this->db->order_by("seq_no", "asc"); 
		$query = $this->db->get('sec_label');
		$query_result = $query->result();
		return $query_result;
	}

	//Select Query For Selected Label Group
	function get_label_group($objid)
	{
		$this->db->where('sec_obj_id', $objid);
		$query = $this->db->get('sec_object');
		$rows = $query->result();
		$label='';
		foreach ($rows as $row):
			$label=$row->sec_group_id;
		endforeach;
		$this->db->where('sec_group_id', $label);
		$query = $this->db->get('sec_group');
		$rows = $query->result();
		$group='';
		foreach ($rows as $row):
				$group=$row->sec_group_desc;
			endforeach;
		return $group;
	}

	//Select Query For Selected Label Object
	function get_label_object($objid)
	{
		$this->db->where('sec_obj_id', $objid);
		$query = $this->db->get('sec_object');
		$rows = $query->result();
		$label='';
		foreach ($rows as $row):
				$label=$row->sec_obj_desc;
			endforeach;
		return $label;
	}

	//Select Query For Selected Label Field Name
	function get_label_object_name($objid)
	{
		$this->db->where('sec_label_id', $objid);
		$query = $this->db->get('sec_label');
		$rows = $query->result();
		$label='';
		foreach ($rows as $row):
				$label=$row->sec_label_desc;
			endforeach;
		return $label;
	}

	// Function to fetch total number of Role
	function totalrole($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT sec_role_id FROM  sec_role ";
		if($data!="")
		{
			$query .=" where lower(sec_role_name) like '%".$data."%' ";
			$query .=" or lower(sec_role_desc) like '%".$data."%' ";
		}
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	//Function to Fetch All Role Record
	function show_roles()
	{
		$query = $this->db->get('sec_role');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All Role Record
	function show_role($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT * FROM  sec_role ";
		if($data!="")
		{
			$query .=" where lower(sec_role_name) like '%".$data."%' ";
			$query .=" or lower(sec_role_desc) like '%".$data."%' ";
		}
		$query .=" order by sec_role_name asc ";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}

	// Add Check Query For Selected Role
	function add_check_role($data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select sec_role_id from sec_role where sec_role_name='$data'");
		return $query->num_rows();
	}

	// Add Query For Selected Role
	function add_role($name,$desc,$path)
	{
		$name=str_replace("'","''",$name);
		$desc=str_replace("'","''",$desc);
        $path=str_replace("'","''",$path);
		$this->db->query("insert into sec_role (sec_role_name,sec_role_desc,sec_role_url_path) values('$name','$desc','$path') ");
		$query=$this->db->query("select sec_role_id from sec_role where sec_role_name='".$name."'");
		$rows=$query->result();
		foreach ($rows as $row):
			$roleid=$row->sec_role_id;
		endforeach;
		return $roleid;
	}

	// Update Check Query For Selected Role
	function update_check_role($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select sec_role_id from sec_role where sec_role_id!=$id and sec_role_name='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Role
	function update_role($id,$data)
	{
		$data=str_replace("'","''",$data);
		$this->db->where('sec_role_id', $id);
		$this->db->update('sec_role', $data);
	}

	// Delete Check Query For Selected Role
	function delete_check_role($id)
	{
		$query=$this->db->query("select user_id from sec_user where sec_role_id=$id");
		return $query->num_rows();
	}

	// Delete Query For Selected Role
	function delete_role($id)
	{
		$this->db->where('sec_role_id', $id);
		$this->db->delete('sec_role_permission');
		$this->db->where('sec_role_id', $id);
		$this->db->delete('sec_role');
	}

	//Function to display record for permission
	function show_permission()
	{
		$query ="select a.sec_group_desc,b.sec_obj_id,b.sec_obj_desc from sec_group a,sec_object b where a.sec_group_id=b.sec_group_id order by a.seq_no asc, b.seq_no asc";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}

	//Function to display permission data for each user
	function show_permission_data($roleid)
	{
			$query ="select * from sec_role_permission where sec_role_id=".$roleid;
			$query = $this->db->query($query);
			$query_result = $query->result();
			return $query_result;
	}

	//Function to display permission data for each user
	function show_permission_object_data($roleid,$objid)
	{
			$query ="select * from sec_role_permission where sec_role_id=".$roleid." and sec_obj_id=".$objid;
			$query = $this->db->query($query);
			$query_result = $query->result();
			return $query_result;
	}

	// Update Query For Selected Role Permission
	function update_role_perm($id,$objid,$data,$data1)
	{
		$query=$this->db->query("select sec_role_id from sec_role_permission where sec_role_id='".$id."' and sec_obj_id='".$objid."'");
		$norow=$query->num_rows();
		if($norow==1)
		{
			$this->db->where('sec_role_id', $id);
			$this->db->where('sec_obj_id', $objid);
			$this->db->update('sec_role_permission', $data);
		}
		else
		{
			$this->db->insert('sec_role_permission', $data1);
		}
	}

	// Function to fetch total number of User
	function totaluser($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT user_id FROM  sec_user,sec_role where sec_user.sec_role_id=sec_role.sec_role_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(user_name) like '%".$data."%' ";
			$query .=" or lower(user_full_name) like '%".$data."%' ";
			$query .=" or lower(sec_role_name) like '%".$data."%' ";
			$query .=" or lower(email_id) like '%".$data."%' ";
			$query .=" )";
		}
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	//Function to Fetch All User Record
	function show_users()
	{
		$this->db->order_by('user_full_name');
		$query = $this->db->get('sec_user');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All User Record
	function show_user($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT * FROM  sec_user,sec_role where sec_user.sec_role_id=sec_role.sec_role_id ";
		if($data!="")
		{
			$query .=" and (";
			$query .=" lower(user_name) like '%".$data."%' ";
			$query .=" or lower(user_full_name) like '%".$data."%' ";
			$query .=" or lower(sec_role_name) like '%".$data."%' ";
			$query .=" or lower(email_id) like '%".$data."%' ";
			$query .=" )";
		}
//		$query .=" order by user_full_name asc OFFSET ".$offset." LIMIT ".$perPage;
		$query .=" order by user_full_name asc";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All User Record
	function chkoldpass_user($username,$password)
	{
		$username=str_replace("'","''",$username);
		$password=str_replace("'","''",$password);
		$query ="SELECT * FROM  sec_user where email_id='".$username."' and pwd_txt='".$password."'";
		$query = $this->db->query($query);
		$query_result = $query->result();
		return $query_result;
	}
	
	// Function to fetch user based on user_id
	function get_user_by_id($id){
		$query = "SELECT * FROM sec_user WHERE user_id=$id";
		$result = $this->db->query($query);
		return $result->row();
	}

	// Add Check Query For Selected User�
	function add_check_user($data,$data1)
	{
		$data=str_replace("'","''",$data);
		$data1=str_replace("'","''",$data1);
		$query=$this->db->query("select user_id from sec_user where user_name='$data' or email_id='$data1'");
		return $query->num_rows();
	}

	// Add Query For Selected User
	function add_user($data)
	{
		$this->db->insert('sec_user', $data);
	}

	// Update Check Query For Selected User
	function update_check_user($id,$data,$data1)
	{
		$data=str_replace("'","''",$data);
		$data1=str_replace("'","''",$data1);
		$query=$this->db->query("select user_id from sec_user where user_id!=$id and (user_name='$data' or email_id='$data1')");
		return $query->num_rows();
	}

	// Update Query For Selected User
	function update_user($id,$data)
	{
		$this->db->where('user_id', $id);
		$this->db->update('sec_user', $data);
	}

	// Update Query For Selected User
	function Changepwd_user($id,$data)
	{
		$this->db->where('email_id', $id);
		$this->db->update('sec_user',$data);
	}

	// Delete Check Query For Selected User
	function delete_check_user($id)
	{//journal_master journal_data_validate_master journal_data_user journal_data_entry_picture journal_data_entry_master journal_data_entry_detail journal_data_entry_audit_log
		$query=$this->db->query("select user_id from project_template where user_id=$id");
		return $query->num_rows();
	}

	//check whether the user_id is existed in any table. done by jane.
	function delete_check_user_journal($id)
	{
        $where = array('user_id' => $id);
        $result = $this->db->select('user_id')->from('journal_master_nonprogressive')
                ->where($where)
                ->get()
                ->num_rows() > 0;
        if ($result == false) {
            $where = array('data_user_id' => $id);
            $result = $this->db->select('data_user_id')->from('journal_data_user_nonprogressive')
                    ->where($where)
                    ->get()
                    ->num_rows() > 0;
            if ($result == false) {
                $where = array('validate_user_id' => $id);
                $result = $this->db->select('validate_user_id')->from('journal_validator_nonprogressive')
                        ->where($where)
                        ->get()
                        ->num_rows() > 0;
                if ($result == false) {
                    $where = array('user_id' => $id);
                    $result = $this->db->select('user_id')->from('journal_master')
                            ->where($where)
                            ->get()
                            ->num_rows() > 0;
                    if ($result == false) {
                        $where = array('data_user_id' => $id);
                        $result = $this->db->select('data_user_id')->from('journal_data_user')
                                ->where($where)
                                ->get()
                                ->num_rows() > 0;
                        if ($result == false) {
                            $where = array('validate_user_id' => $id);
                            $result = $this->db->select('validate_user_id')->from('journal_validator')
                                    ->where($where)
                                    ->get()
                                    ->num_rows() > 0;
                            return $result;
                        }
                    }
                }
            }
        }
        return $result;
	}

	// Delete Query For Selected User
	function delete_user($id)
	{
		$this->db->where('user_id', $id);
		$this->db->delete('sec_user');
	}
}


?>