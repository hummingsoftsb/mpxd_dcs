<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Design extends CI_Model
{
	//Function to Fetch All Frequency
	function show_frequency()
	{
		$query = $this->db->get('frequency_master');
		$query_result = $query->result();
		return $query_result;
	}

	//Function to Fetch All Frequency
	function show_frequency_detail_no($date)
	{
		$sql="select frequency_detail_no from frequency_detail where '$date' between start_date and end_date";
		$query = $this->db->query($sql);
		$query_result = $query->result();
		foreach ($query_result as $row):
			$frequencyno=$row->frequency_detail_no;
		endforeach;
		return $frequencyno;
	}

	// Function to fetch total number of records
	function totalprojtmp($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT pt.*,su.user_full_name FROM project_template pt,sec_user su";
		$sql .=" where pt.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(project_definition) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// Function To Fetch All Project Template Record
	function show_projtmps()
	{
		$this->db->order_by('project_name');
		$query = $this->db->get('project_template');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch All Project Template Record
	function show_projtmp($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT pt.*,su.user_full_name FROM project_template pt,sec_user su";
		$sql .=" where pt.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(project_definition) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$sql .=" Order By project_name asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($sql);
        return $q->result();
	}

	function show_proj_field($field,$value)
	{
		$this->db->where('project_no',$value);
		$this->db->select($field);
		$query = $this->db->get('project_template');
		$query_result = $query->result();
		foreach ($query_result as $row):
			$fieldvalue=$row->$field;
		endforeach;
		return $fieldvalue;
	}

	// Add Check Query For Selected Project Template
	function add_check_projtmp($data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select project_name from project_template where project_name='$data'");
		return $query->num_rows();
	}

	//Function to add new record
	function add_projtmp($data)
	{
		// Inserting in Table unit_measure
		$this->db->insert('project_template', $data);
		return true;
	}

	// Update Check Query For Selected Project Template
	function update_check_projtmp($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select project_name from project_template where project_no!=$id and project_name='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Project Template
	function update_projtmp($id,$data)
	{
		$this->db->where('project_no', $id);
		$this->db->update('project_template', $data);
	}

	// Delete Check Query For Selected Project Template
	function delete_check_projtmp($id)
	{

		$query=$this->db->query("select project_no from journal_master where project_no=$id");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_projtmp($id)
	{
		$this->db->where('project_no', $id);
		$this->db->delete('project_template');
	}

	// Function to fetch total number of records
	function totaljournal($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT jm.*,pt.project_name,su.user_full_name FROM journal_master jm,project_template pt,sec_user su";
		$sql .=" where jm.project_no=pt.project_no and jm.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(journal_name) like '%".$data."%' ";
			$sql .=" or lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// Function To Fetch All Project Template Record
	function show_journals()
	{
        $q = $this->db->get('project_template');
        return $q->result();
	}

	// Function To Fetch All Project Template Record
	function show_journal($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT jm.*,pt.project_name, jm.album_name,su.user_full_name,dependency  FROM journal_master jm,project_template pt,sec_user su";
		$sql .=" where jm.project_no=pt.project_no and jm.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(journal_name) like '%".$data."%' ";
			$sql .=" or lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$sql .=" Order By project_name asc, journal_name asc OFFSET ".$offset." LIMIT ".$perPage;
        $q = $this->db->query($sql);
        return $q->result();
	}

	//Function to Fetch Validator for Selected Journal
	function show_journal_validator($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$this->db->order_by("validate_level_no", "asc");
		$q = $this->db->get('journal_validator');
        return $q->result();
	}

	//Function to Fetch Data User for Selected Journal
	function show_journal_data_user($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$this->db->order_by("default_owner_opt","desc");
		$q = $this->db->get('journal_data_user');
        return $q->result();
	}

	//Function to Fetch Data Attribute for Selected Journal
	function show_journal_data_attb($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$this->db->order_by("display_seq_no");
		$q = $this->db->get('journal_detail');
        return $q->result();
	}

	// Add Check Query For Selected Project Template
	function add_check_journal($data,$projectno)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select journal_name from journal_master where journal_name='$data'");
		return $query->num_rows();
	}

	//Function to add new record
	function add_journal($data,$projectno,$name)
	{
		// Inserting in Table Journal Master
		$this->db->insert('journal_master', $data);
		$query=$this->db->query("select journal_no from journal_master where journal_name='$name'");
		$rows=$query->result();
		foreach ($rows as $row):
			$journalno=$row->journal_no;
		endforeach;
		return $journalno;
	}

	//Function to add new record
	function add_journal_validator($data)
	{
		// Inserting in Table Journal Validator
		$this->db->insert('journal_validator', $data);
	}

	//Function to add new record
	function add_journal_data_entry($data)
	{
		// Inserting in Table Journal Data User
		$this->db->insert('journal_data_user', $data);
	}

	//Function to add new record
	function add_journal_detail($data)
	{
		// Inserting in Table Journal Detail
		$this->db->insert('journal_detail', $data);
	}

	//Function to add new record
	function add_journal_data_entry_master($data)
	{
		// Inserting in Table Journal Data Entry Master
		$this->db->insert('journal_data_entry_master', $data);
	}

	// Update Check Query For Selected Project Template
	function update_check_journal($data,$projectno,$journalno)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select journal_name from journal_master where journal_name='$data' and journal_no!=$journalno");
		return $query->num_rows();
	}

	// Update Query For Selected Project Template
	function update_journal($id,$data)
	{
		$this->db->where('journal_no', $id);
		$this->db->update('journal_master', $data);
	}

	// Delete Check Query For Selected Project Template
	function delete_check_journal($id)
	{
		$query=$this->db->query("select data_entry_no from journal_data_entry_detail where data_entry_no in (select data_entry_no from journal_data_entry_master where journal_no=$id)");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_journal($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_entry_master');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_user');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_validator');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_detail');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_master');
	}

	// Check journal end date greater than project end date
	function datecheck1($date1,$id)
	{
		$query=$this->db->query("select * from project_template where project_no=".$id." and end_date < '".$date1."'");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_journal_validator($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_validator');
	}

	// Delete the selected record
	function delete_journal_data_entry($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_user');
	}

	// Delete the selected record
	function delete_journal_detail($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_detail');
	}

	// Function to fetch total number of records
	function totaljournalnonp($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT jm.*,pt.project_name,su.user_full_name FROM journal_master_nonprogressive jm,project_template pt,sec_user su";
		$sql .=" where jm.project_no=pt.project_no and jm.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(journal_name) like '%".$data."%' ";
			$sql .=" or lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	// Function To Fetch All Project Template Record
	function show_journalnonp($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT jm.*,pt.project_name,su.user_full_name FROM journal_master_nonprogressive jm,project_template pt,sec_user su";
		$sql .=" where jm.project_no=pt.project_no and jm.user_id=su.user_id ";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(journal_name) like '%".$data."%' ";
			$sql .=" or lower(project_name) like '%".$data."%' ";
			$sql .=" or lower(user_full_name) like '%".$data."%' ";
			$sql .=" ) ";
		}
		$sql .=" Order By project_name asc, journal_name asc OFFSET ".$offset." LIMIT ".$perPage;
		$q = $this->db->query($sql);
		return $q->result();
	}
	//Function to Fetch Validator for Selected Journal
	function show_journal_validatornonp($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$this->db->order_by("validate_level_no", "asc");
		$q = $this->db->get('journal_validator_nonprogressive');
		return $q->result();
	}

	//Function to Fetch Data User for Selected Journal
	function show_journal_data_usernonp($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$this->db->order_by("default_owner_opt","desc");
		$q = $this->db->get('journal_data_user_nonprogressive');
		return $q->result();
	}

	//Function to Fetch Data Attribute for Selected Journal
	function show_journal_data_attbnonp($journalno)
	{
		$this->db->where('journal_no',$journalno);
		$q = $this->db->get('journal_detail_nonprogressive');
		return $q->result();
	}

	// Add Check Query For Selected Project Template
	function add_check_journalnonp($data,$projectno)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select journal_name from journal_master_nonprogressive where journal_name='$data' and project_no=$projectno");
		return $query->num_rows();
	}

	//Function to add new record
	function add_journalnonp($data,$projectno,$name)
	{
		// Inserting in Table Journal Master
		$this->db->insert('journal_master_nonprogressive', $data);
		$query=$this->db->query("select journal_no from journal_master_nonprogressive where journal_name='$name'");
		$rows=$query->result();
		foreach ($rows as $row):
			$journalno=$row->journal_no;
		endforeach;
		return $journalno;
	}

	//Function to add new record
	function add_journal_validatornonp($data)
	{
		// Inserting in Table Journal Validator
		$this->db->insert('journal_validator_nonprogressive', $data);
	}

	//Function to add new record
	function add_journal_data_entrynonp($data)
	{
		// Inserting in Table Journal Data User
		$this->db->insert('journal_data_user_nonprogressive', $data);
	}

	//Function to add new record
	function add_journal_detailnonp($data)
	{
		// Inserting in Table Journal Detail
		$this->db->insert('journal_detail_nonprogressive', $data);
	}

	//Function to add new record
	function add_journal_data_entry_masternonp($data)
	{
		// Inserting in Table Journal Data Entry Master
		$this->db->insert('journal_data_entry_master_nonprogressive', $data);
	}

	// Update Check Query For Selected Project Template
	function update_check_journalnonp($data,$projectno,$journalno)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select journal_name from journal_master_nonprogressive where journal_name='$data' and journal_no!=$journalno");
		return $query->num_rows();
	}

	// Update Query For Selected Project Template
	function update_journalnonp($id,$data)
	{
		$this->db->where('journal_no', $id);
		$this->db->update('journal_master_nonprogressive', $data);
	}

	// Delete Check Query For Selected Project Template
	function delete_check_journalnonp($id)
	{
		$query=$this->db->query("select data_entry_no from journal_data_entry_detail_nonprogressive where data_entry_no in (select data_entry_no from journal_data_entry_master_nonprogressive where journal_no=$id)");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_journalnonp($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_entry_master_nonprogressive');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_user_nonprogressive');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_validator_nonprogressive');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_detail_nonprogressive');

		$this->db->where('journal_no', $id);
		$this->db->delete('journal_master_nonprogressive');
	}

	// Delete the selected record
	function delete_journal_validatornonp($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_validator_nonprogressive');
	}

	// Delete the selected record
	function delete_journal_data_entrynonp($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_data_user_nonprogressive');
	}

	// Delete the selected record
	function delete_journal_detailnonp($id)
	{
		$this->db->where('journal_no', $id);
		$this->db->delete('journal_detail_nonprogressive');
	}

	//Publish the selected record
	function publish_journal_data_entrynonp($id,$userid)
	{
		$this->db->query("update journal_data_entry_master_nonprogressive set data_entry_status_id=4,publish_user_id=$userid,publish_date='".date("Y-m-d")."' where data_entry_no in (select data_entry_no from journal_data_entry_master_nonprogressive where journal_no=$id)");
	}


	// Function to fetch total number of records
	function totalchdeo($data)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="(select a.project_name,b.journal_name,b.journal_no,d.user_full_name,'jp' as type from project_template a, journal_master b , journal_data_user c, sec_user d where a.project_no=b.project_no and b.journal_no=c.journal_no and c.data_user_id=d.user_id and default_owner_opt=1";
				if($data!="")
				{
					$query .=" and( lower(a.project_name) like '%".$data."%' ";
					$query .=" or lower(b.journal_name) like '%".$data."%' ";
					$query .=" or lower(d.user_full_name) like '%".$data."%') ";
				}
				$query .=") UNION (select a.project_name,b.journal_name,b.journal_no,d.user_full_name,'jnonp' as type from project_template a, journal_master_nonprogressive b , journal_data_user_nonprogressive c, sec_user d where a.project_no=b.project_no and b.journal_no=c.journal_no and c.data_user_id=d.user_id and default_owner_opt=1";
				if($data!="")
						{
							$query .=" and( lower(a.project_name) like '%".$data."%' ";
							$query .=" or lower(b.journal_name) like '%".$data."%' ";
							$query .=" or lower(d.user_full_name) like '%".$data."%') ";
				}
		$query .=")";
		$query = $this->db->query($query);
		return $query->num_rows();
	}

	// Function To Fetch All data entry owner Record
	function show_chdeo($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="(select a.project_name,b.journal_name,b.journal_no,d.user_full_name,'jp' as type from project_template a, journal_master b , journal_data_user c, sec_user d where a.project_no=b.project_no and b.journal_no=c.journal_no and c.data_user_id=d.user_id and default_owner_opt=1";
		if($data!="")
		{
			$query .=" and( lower(a.project_name) like '%".$data."%' ";
			$query .=" or lower(b.journal_name) like '%".$data."%' ";
			$query .=" or lower(d.user_full_name) like '%".$data."%') ";
		}
		$query .=") UNION (select a.project_name,b.journal_name,b.journal_no,d.user_full_name,'jnonp' as type from project_template a, journal_master_nonprogressive b , journal_data_user_nonprogressive c, sec_user d where a.project_no=b.project_no and b.journal_no=c.journal_no and c.data_user_id=d.user_id and default_owner_opt=1";
		if($data!="")
				{
					$query .=" and( lower(a.project_name) like '%".$data."%' ";
					$query .=" or lower(b.journal_name) like '%".$data."%' ";
					$query .=" or lower(d.user_full_name) like '%".$data."%') ";
		}
		$query .=") Order By project_name asc,journal_name asc OFFSET ".$offset."LIMIT ".$perPage;
		$q = $this->db->query($query);
		return $q->result();
	}

	// Function To Fetch Selected data entry owner journal Record
	function show_chdeo_id($data,$type)
	{
		if($type=="jp") {
		$query="select c.journal_data_user_no,d.user_full_name from journal_data_user c, sec_user d where c.data_user_id=d.user_id and c.journal_no=".$data." and c.default_owner_opt!=1";
		} else {
		$query="select c.journal_data_user_no,d.user_full_name from journal_data_user_nonprogressive c, sec_user d where c.data_user_id=d.user_id and c.journal_no=".$data." and c.default_owner_opt!=1";
		}
		$q = $this->db->query($query);
		return $q->result();
	}

	// Function to update data entry owner
	function update_chdeo($id,$data,$type)
	{
			if($type=="jp") {
			$this->db->set('default_owner_opt', '0');
			$this->db->where('journal_no', $id);
			$this->db->update('journal_data_user');
			$this->db->set('default_owner_opt', '1');
			$this->db->where('journal_data_user_no', $data);
			$this->db->update('journal_data_user');
			} else {
			$this->db->set('default_owner_opt', '0');
			$this->db->where('journal_no', $id);
			$this->db->update('journal_data_user_nonprogressive');
			$this->db->set('default_owner_opt', '1');
			$this->db->where('journal_data_user_no', $data);
			$this->db->update('journal_data_user_nonprogressive');
			}
	}
	
	// Function to update journal_data_validate_master's validator
	
	function update_journal_data_validate_master($data_entry_no,$validate_user){
		
	}
	

}
?>