<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class IntegrationModel extends CI_Model
{
	
	var $email_id = 'integrationtestuser@hummingsoft.com.my';
	var $username = 'integration_test_user';
	var $project_name = 'Integration Test Project';
	var $journal_data_name = 'Integration Test Journal Data';
	var $journal_image_name = 'Integration Test Journal Image';

	
	function get_user() {
		$email_id = $this->email_id;
		$username = $this->username;
		$query = "SELECT * FROM sec_user WHERE email_id='$email_id' AND user_name='$username'";
		return $this->db->query($query)->result();
	}
	
	function get_project($user_id) {
		$project_name = $this->project_name;
		$query = "SELECT * FROM project_template WHERE project_name = '$project_name' AND user_id = '$user_id'";
		return $this->db->query($query)->result();
	}
	
	function get_journal_data($user_id, $project_no) {
		$journal_data_name = $this->journal_data_name;
		$query = "SELECT * FROM journal_master WHERE journal_name = '$journal_data_name' AND project_no = '$project_no' AND user_id = '$user_id'";
		return $this->db->query($query)->result();
	}
	
	function get_journal_image($user_id, $project_no) {
		$journal_image_name = $this->journal_image_name;
		$query = "SELECT * FROM journal_master WHERE journal_name = '$journal_image_name' AND project_no = '$project_no' AND user_id = '$user_id'";
		return $this->db->query($query)->result();
	}
	
	function get_data_entry_no($journal_no) {	
		$query = "SELECT * FROM journal_data_entry_master WHERE journal_no = '$journal_no'";
		return $this->db->query($query)->result();
	}
	
	function setup_user() {
		$username = $this->username;
		$email_id = $this->email_id;
		$user = $this->get_user();
		if (sizeOf($user) > 0) return $user;
		$insert_query = "INSERT INTO sec_user (user_name, user_full_name, user_type, sec_role_id, email_id, dept_name, pwd_txt, change_pwd_opt, lock_by_pwd, no_pwd_attempt, user_status) VALUES('$username',	'Integration test user',	1,	1,	'$email_id',	NULL,	'fe01ce2a7fbac8fafaed7c982a04e229',	0,	0,	0,	0);";
		$this->db->query($insert_query);
		$result = $this->get_user();//$this->db->query($query)->result();
		//}
		return $result;
	}
	
	function setup_project($user_id) {
		$project_name = $this->project_name;
		$project = $this->get_project($user_id);
		if (sizeOf($project) > 0) return $project;
		$insert_query = "INSERT INTO project_template (project_name, project_definition, user_id, start_date, end_date) VALUES('$project_name',	NULL,	'$user_id',	'2015-01-01',	'2099-01-01');";
		$this->db->query($insert_query);
		return $this->get_project($user_id);
	}
	
	function unsetup_project($user_id) {
		$project_name = $this->project_name;
		$delete_query = "DELETE FROM project_template WHERE project_name = '$project_name' AND user_id='$user_id'";
		$this->db->query($delete_query);
	}
	
	function setup_journal_image($user_id, $project_no) {
		$journal_image_name = $this->journal_image_name;
		$journal = $this->get_journal_image($user_id, $project_no);
		if (sizeOf($journal) > 0) return $journal;

		$insert_query = "INSERT INTO journal_master (project_no, journal_name, journal_definition, user_id, start_date, end_date, frequency_no, journal_property, dependency, is_image, album_name) VALUES('$project_no',	'$journal_image_name',	NULL,	'$user_id',	'2015-01-01',	NULL,	1,	'',	'',	1,	'Integration Test Album');";
		$this->db->query($insert_query);
		$journal = $this->get_journal_image($user_id, $project_no)[0];
		$journal_no = $journal->journal_no;
		
		$q9 = "INSERT INTO journal_data_entry_master (journal_no, frequency_detail_no, data_entry_status_id, created_user_id, created_date, publish_user_id, publish_date) VALUES ('$journal_no',	93,	1,	'$user_id',	'2015-10-20',	NULL,	NULL) RETURNING data_entry_no";
		$data_entry_no = $this->db->query($q9)->result()[0]->data_entry_no;
				
		$q4 = "INSERT INTO journal_data_entry_detail (data_entry_no, data_attb_id, actual_value, start_value, end_value, frequency_max_value, prev_actual_value, frequency_max_opt, display_seq_no, data_source, created_user_id, created_date) VALUES('$data_entry_no',	0,	'',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	1,	'2015-10-12')";
		$this->db->query($q4);
		
		$q10 = "INSERT INTO journal_data_validate_master (data_entry_no, validate_user_id, validate_level_no, validate_status, accept_date) VALUES ('$data_entry_no',	1,	1,	1,	NULL)";
		$this->db->query($q10);
		
		$q11 = "INSERT INTO journal_data_user (journal_no, data_user_id, default_owner_opt) VALUES('$journal_no',	'$user_id',	1);";
		$this->db->query($q11);
		
		$q12 = "INSERT INTO journal_validator (journal_no, validate_user_id, validate_level_no) VALUES ('$journal_no',	'$user_id',	1);";
		$this->db->query($q12);
		
		return $journal;
	}
	
	function unsetup_journal_image($user_id, $project_no) {
		$journal = $this->get_journal_image($user_id, $project_no);
		if (sizeOf($journal) < 1) return false;
		$journal = $journal[0];
		$journal_no = $journal->journal_no;
		$journal_image_name = $this->journal_image_name;
		$data_entry = $this->get_data_entry_no($journal_no);
		$data_entry_no = sizeOf($data_entry) < 1 ? false : $data_entry[0]->data_entry_no;
		
		if ($data_entry_no) {
			$delete_query = "DELETE FROM journal_data_validate_detail WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
			$delete_query = "DELETE FROM journal_data_validate_master WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
			$delete_query = "DELETE FROM journal_data_entry_master WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
		}
		
		
		$delete_query = "DELETE FROM journal_validator WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		$delete_query = "DELETE FROM journal_data_user WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		$delete_query = "DELETE FROM journal_data_entry_master WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		//$delete_query = "DELETE FROM journal_detail WHERE journal_no = '$journal_no'";
		//$this->db->query($delete_query);
		
		//$delete_query = "DELETE FROM data_attribute WHERE data_attb_label = 'DataAttbIntegrationTestType4' OR data_attb_label = 'DataAttbIntegrationTestType3' OR data_attb_label = 'DataAttbIntegrationTestType2' OR data_attb_label = 'DataAttbIntegrationTestType1'";
		//$this->db->query($delete_query);
		
		
		$delete_query = "DELETE FROM journal_master WHERE journal_name = '$journal_image_name' AND project_no = '$project_no' AND user_id = '$user_id'";
		$this->db->query($delete_query);
		
	}
	
	
	function setup_journal_data($user_id, $project_no) {
		$journal_data_name = $this->journal_data_name;
		$journal = $this->get_journal_data($user_id, $project_no);
		if (sizeOf($journal) > 0) return $journal;
		$insert_query = "INSERT INTO journal_master (project_no, journal_name, journal_definition, user_id, start_date, end_date, frequency_no, journal_property, dependency, is_image, album_name) VALUES('$project_no',	'$journal_data_name',	NULL,	'$user_id',	'2015-01-01',	NULL,	1,	'',	'',	0,	'');";
		$this->db->query($insert_query);
		$journal = $this->get_journal_data($user_id, $project_no)[0];
		$journal_no = $journal->journal_no;
		
		// WATCH FOR DATA_SET_ID! MIGHT BREAK IF IT DOES NOT EXIST
		$q4 = "INSERT INTO data_attribute (data_attb_label, data_attb_type_id) VALUES('DataAttbIntegrationTestType4', 4) RETURNING data_attb_id";
		$q3 = "INSERT INTO data_attribute (data_attb_label, data_attb_type_id, data_attb_data_type_id) VALUES('DataAttbIntegrationTestType3', 3, 1) RETURNING data_attb_id";
		$q2 = "INSERT INTO data_attribute (data_attb_label, data_attb_type_id, data_set_id) VALUES('DataAttbIntegrationTestType2', 2, 2) RETURNING data_attb_id";
		$q1 = "INSERT INTO data_attribute (data_attb_label, data_attb_type_id, data_attb_data_type_id, data_attb_digits) VALUES('DataAttbIntegrationTestType1', 1, 1, 0) RETURNING data_attb_id";

		
		$attb4 = $this->db->query($q4)->result()[0]->data_attb_id;
		$attb3 = $this->db->query($q3)->result()[0]->data_attb_id;
		$attb2 = $this->db->query($q2)->result()[0]->data_attb_id;
		$attb1 = $this->db->query($q1)->result()[0]->data_attb_id;
		
		
		$q8 = "INSERT INTO journal_detail (journal_no, data_attb_id, start_value, end_value, frequency_max_value, display_seq_no) VALUES ('$journal_no',	'$attb4',	'0.0000',	'100',	'100',	1);";
		$q7 = "INSERT INTO journal_detail (journal_no, data_attb_id, start_value, end_value, frequency_max_value, display_seq_no) VALUES ('$journal_no',	'$attb3',	'0.0000',	'100',	'100',	2);";
		$q6 = "INSERT INTO journal_detail (journal_no, data_attb_id, start_value, end_value, frequency_max_value, display_seq_no) VALUES ('$journal_no',	'$attb2',	'0.0000',	'100',	'100',	3);";
		$q5 = "INSERT INTO journal_detail (journal_no, data_attb_id, start_value, end_value, frequency_max_value, display_seq_no) VALUES ('$journal_no',	'$attb1',	'0.0000',	'100',	'100',	4);";
		
		$this->db->query($q8);
		$this->db->query($q7);
		$this->db->query($q6);
		$this->db->query($q5);
				
		// Week 93 might get broken
		$q9 = "INSERT INTO journal_data_entry_master (journal_no, frequency_detail_no, data_entry_status_id, created_user_id, created_date, publish_user_id, publish_date) VALUES ('$journal_no',	93,	1,	'$user_id',	'2015-10-20',	NULL,	NULL) RETURNING data_entry_no";
		$data_entry_no = $this->db->query($q9)->result()[0]->data_entry_no;
		
		$q10 = "INSERT INTO journal_data_validate_master (data_entry_no, validate_user_id, validate_level_no, validate_status, accept_date) VALUES ('$data_entry_no',	1,	1,	1,	NULL)";
		$this->db->query($q10);
		
		$q11 = "INSERT INTO journal_data_user (journal_no, data_user_id, default_owner_opt) VALUES('$journal_no',	'$user_id',	1);";
		$this->db->query($q11);
		
		$q12 = "INSERT INTO journal_validator (journal_no, validate_user_id, validate_level_no) VALUES ('$journal_no',	'$user_id',	1);";
		$this->db->query($q12);
		
		return $this->get_journal_data($user_id, $project_no);
	}
	
	function unsetup_journal_data($user_id, $project_no) {
		$journal = $this->get_journal_data($user_id, $project_no);
		if (sizeOf($journal) < 1) return false;
		$journal = $journal[0];
		$journal_no = $journal->journal_no;
		$journal_data_name = $this->journal_data_name;
		$data_entry = $this->get_data_entry_no($journal_no);
		$data_entry_no = sizeOf($data_entry) < 1 ? false : $data_entry[0]->data_entry_no;
		
		if ($data_entry_no) {
			$delete_query = "DELETE FROM journal_data_validate_detail WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
			$delete_query = "DELETE FROM journal_data_validate_master WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
			$delete_query = "DELETE FROM journal_data_entry_master WHERE data_entry_no = '$data_entry_no'";
			$this->db->query($delete_query);
		}
		
		
		$delete_query = "DELETE FROM journal_validator WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		$delete_query = "DELETE FROM journal_data_user WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		$delete_query = "DELETE FROM journal_data_entry_master WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		$delete_query = "DELETE FROM journal_detail WHERE journal_no = '$journal_no'";
		$this->db->query($delete_query);
		
		
		$delete_query = "DELETE FROM data_attribute WHERE data_attb_label = 'DataAttbIntegrationTestType4' OR data_attb_label = 'DataAttbIntegrationTestType3' OR data_attb_label = 'DataAttbIntegrationTestType2' OR data_attb_label = 'DataAttbIntegrationTestType1'";
		$this->db->query($delete_query);
		
		
		$delete_query = "DELETE FROM journal_master WHERE journal_name = '$journal_data_name' AND project_no = '$project_no' AND user_id = '$user_id'";
		$this->db->query($delete_query);
		
	}
	
	
	
	
	
}