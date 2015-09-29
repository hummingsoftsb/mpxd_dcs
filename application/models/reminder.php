<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Reminder extends CI_Model
{
	function show_frequency_detail_no($date,$day)
	{
		$day=strtoupper($day);
		$sql="select data_entry_no,data_entry_status_id,journal_no,(select frequency_detail_name from frequency_detail where frequency_detail.frequency_detail_no=journal_data_entry_master.frequency_detail_no)as frequency_detail_name from journal_data_entry_master where data_entry_status_id in (0,1) and frequency_detail_no in (select frequency_detail_no from frequency_detail fd,frequency_master fm where fm.frequency_no=fd.frequency_no and cut_off_day='$day' and '$date' between start_date and end_date)";
		$query = $this->db->query($sql);
		$query_result = $query->result();
		foreach ($query_result as $row):
			$statusid=$row->data_entry_status_id;
			if($statusid==0)
			{

			}
			else
			{
				$sql="select journal_name,project_name,user_full_name,email_id from journal_master jm,project_template pt where pt.project_no=jm.project_no and jm.journal_no='".$row->journal_no."'";

			}
		endforeach;
		return $sql;
	}
	function show_current_progressive_pending($date,$day)
	{
		$day=strtoupper($day);
		$sql="select data_entry_no,data_entry_status_id,journal_no,(select journal_name from journal_master jm where jm.journal_no=jdem.journal_no) as journalname,(select user_full_name from sec_user su where su.user_id in (select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1)) as name ,(select email_id from sec_user su where su.user_id in (select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1)) as emailid,(select frequency_detail_name from frequency_detail fd where fd.frequency_detail_no=jdem.frequency_detail_no)as frequency_detail_name from journal_data_entry_master jdem where data_entry_status_id=1 and frequency_detail_no in (select frequency_detail_no from frequency_detail fd,frequency_master fm where fm.frequency_no=fd.frequency_no and cut_off_day='$day' and '$date' between start_date and end_date)";
		$query = $this->db->query($sql);
		return $query->result();
	}
	function show_old_progressive_pending($date,$day)
	{
		$day=strtoupper($day);
		$sql="select data_entry_no,data_entry_status_id,journal_no,frequency_detail_no,(select journal_name from journal_master jm where jm.journal_no=jdem.journal_no) as journalname,(select user_full_name from sec_user su where su.user_id in (select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1)) as name ,(select email_id from sec_user su where su.user_id in (select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1)) as emailid from journal_data_entry_master jdem where data_entry_status_id=0 and frequency_detail_no in (select frequency_detail_no from frequency_detail fd,frequency_master fm where fm.frequency_no=fd.frequency_no and cut_off_day='$day' and '$date' between start_date and end_date)";
		$query = $this->db->query($sql); die($sql);
		return $query->result();
	}
	function show_current_progressive_pending_val($date,$day)
	{
		$sql="select data_entry_no,data_entry_status_id,journal_no,(select journal_name from journal_master jm where jm.journal_no=jdem.journal_no) as journalname,(select user_full_name from sec_user su where su.user_id in (select validate_user_id from journal_data_validate_master jdvm where jdvm.data_entry_no=jdem.data_entry_no and validate_status=1)) as name ,(select email_id from sec_user su where su.user_id in (select validate_user_id from journal_data_validate_master jdvm where jdvm.data_entry_no=jdem.data_entry_no and validate_status=1)) as emailid,(select frequency_detail_name from frequency_detail fd where fd.frequency_detail_no=jdem.frequency_detail_no)as frequency_detail_name from journal_data_entry_master jdem where data_entry_status_id in (2,3) and frequency_detail_no in (select frequency_detail_no from frequency_detail fd,frequency_master fm where fm.frequency_no=fd.frequency_no and cut_off_day='$day' and '$date' between start_date and end_date)";
		$query = $this->db->query($sql);
		return $query->result();
	}
	function show_old_progressive_freqname($journalno,$dataentryno)
	{
		$sql="select (select frequency_detail_name from frequency_detail fd where fd.frequency_detail_no=jdem.frequency_detail_no)as frequency_detail_name from journal_data_entry_master jdem where journal_no=".$journalno." and data_entry_no<=".$dataentryno." and data_entry_status_id in (0,1)  order by frequency_detail_no asc";
		$query = $this->db->query($sql);
		$rows=$query->result();
		$freqname="";
		foreach ($rows as $row):
			if($freqname=="")
				$freqname=$row->frequency_detail_name;
			else
				$freqname .=",".$row->frequency_detail_name;
		endforeach;
		return $freqname;
	}
	
	function update_reminder(){
			$sql = "ALTER SEQUENCE user_reminder_reminder_no_seq RESTART WITH 1;TRUNCATE user_reminder; INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 2 as reminder_status_id, 'Waiting for Validation: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 3 OR jdem.data_entry_status_id = 2) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now() ); INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 1 as reminder_status_id, 'Incomplete Data Entry: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 1 OR jdem.data_entry_status_id = 0) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now());";
			$query = $this->db->query($sql);
	}
}
?>