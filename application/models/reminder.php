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
	
//	function update_reminder(){
//			$sql = "ALTER SEQUENCE user_reminder_reminder_no_seq RESTART WITH 1;TRUNCATE user_reminder; INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 2 as reminder_status_id, 'Waiting for Validation: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 3 OR jdem.data_entry_status_id = 2) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now() ); INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 1 as reminder_status_id, 'Incomplete Data Entry: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 1 OR jdem.data_entry_status_id = 0) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now());";
//			$query = $this->db->query($sql);
//	}


// modified by jane
    function update_reminder(){
        $sql = "ALTER SEQUENCE user_reminder_reminder_no_seq RESTART WITH 1;TRUNCATE user_reminder; INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 2 as reminder_status_id, 'Waiting for Validation: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 3 OR jdem.data_entry_status_id = 2) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now() ); INSERT INTO user_reminder ( SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdu.data_user_id as reminder_user_id, jdem.data_entry_no as data_entry_no, 1 as reminder_status_id, 'Incomplete Data Entry: ' || jm.journal_name as reminder_message, 0 as reminder_hide, 1 as email_send_opt FROM journal_data_user jdu, journal_data_entry_master jdem, frequency_detail fd, journal_master jm WHERE jdu.journal_no = jdem.journal_no AND jm.journal_no = jdem.journal_no AND (jdem.data_entry_status_id = 1 OR jdem.data_entry_status_id = 0) AND jdem.frequency_detail_no = fd.frequency_detail_no AND fd.start_date < now());";
        $query = $this->db->query($sql);
        //for data entry non progressive
        $timestamp_sql = "SELECT max(timestamp), jmnp.reminder_frequency FROM ilyas_config ic, ilyas i, journal_master_nonprogressive jmnp
               WHERE ic.config_no = i.config_no and i.validate_status=2 and ic.journal_no = jmnp.journal_no
               GROUP BY ic.journal_no,  jmnp.reminder_frequency, jmnp.journal_no";
        $query_result = $this->db->query($timestamp_sql)->result();
        foreach($query_result as $row) {
            $last_revision_date = date_format(date_create($row->max), 'Y-m-d');
            $frequency = $row->reminder_frequency;
            $now = date('Y-m-d');
            $daylen = 60*60*24;
            $days_diff = (strtotime($now)-strtotime($last_revision_date))/$daylen;
            if(($frequency == 'Weekly' && $days_diff > 7) || ($frequency == 'Monthly' && $days_diff >30 ) ) {
                $query_data_entry = "INSERT INTO user_reminder (SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jdunp.data_user_id as reminder_user_id,
                           NULL as data_entry_no , 1 as reminder_status_id, 'Incomplete Data Entry: ' || jmnp.journal_name as reminder_message, 0 as reminder_hide,
                           1 as email_send_opt, jmnp.journal_no as nonp_journal_id FROM journal_data_user_nonprogressive jdunp, journal_master_nonprogressive jmnp,  ilyas_config as ic, ilyas as i
                           WHERE jdunp.journal_no = jmnp.journal_no  AND ic.journal_no = jmnp.journal_no AND i.validate_status = 2 AND ic.config_no = i.config_no
                           GROUP BY jdunp.data_user_id, jmnp.journal_no, nonp_journal_id)";
                $this->db->query($query_data_entry);
            }
        }
        // for validator non progressive
        $query_validator = "INSERT INTO user_reminder (SELECT nextval('user_reminder_reminder_no_seq') as reminder_no, now() as reminder_date, jvn.validate_user_id as reminder_user_id,
                               NULL as data_entry_no, 2 as reminder_status_id, 'Waiting for Validation: ' || jmnp.journal_name as reminder_message, 0 as reminder_hide,
                               1 as email_send_opt, ic.journal_no as nonp_journal_id  FROM journal_validator_nonprogressive jvn, journal_master_nonprogressive jmnp, ilyas_config as ic, ilyas as i
                               WHERE jvn.journal_no = jmnp.journal_no AND ic.journal_no = jmnp.journal_no AND i.validate_status = 1 AND ic.config_no = i.config_no GROUP BY jvn.validate_user_id,
                               ic.journal_no, jmnp.journal_no)";
        $this->db->query($query_validator);
    }

    // function to resend reminder by admin. done by jane
    function resend_reminder($reminder_no){
        $query = "SELECT * FROM user_reminder WHERE reminder_no=$reminder_no";
        $result = $this->db->query($query)->row();
        if(!empty($result->data_entry_no)) {
            $data_entry_no = $result->data_entry_no;
        } else {
            $data_entry_no = 'NULL';
        }
        if(!empty($result->nonp_journal_id)) {
            $journal_no = $result->nonp_journal_id;
        } else {
            $journal_no = 'NULL';
        }
        $insert_query = "INSERT INTO user_reminder(reminder_date, reminder_user_id, data_entry_no, reminder_status_id, reminder_message, reminder_hide, email_send_opt, nonp_journal_id) values(now(), $result->reminder_user_id, $data_entry_no, $result->reminder_status_id, '".$result->reminder_message."', $result->reminder_hide, $result->email_send_opt, $journal_no)";
        $this->db->query($insert_query);
    }
}
?>