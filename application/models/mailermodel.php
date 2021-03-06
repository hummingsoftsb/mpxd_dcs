<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class MailerModel extends CI_Model
{
	function get_all_for_userid($userid) {
		$userid = str_replace("'","",$userid);
		$query = "SELECT a.id, a.type, a.data, b.user_id, b.user_full_name, b.email_id FROM email_queue a, sec_user b WHERE a.user_id=b.user_id AND a.user_id='$userid'";
		$result = $this->db->query($query)->result();
		return $result;
	}
	
	function get_progressive_details($jid) {
		$jid = str_replace("'","",$jid);
		$query = "SELECT journal_name FROM journal_master WHERE journal_no = '$jid'";
		return $this->db->query($query)->result();
	}
	
	function get_progressive_details_dataentry($dataentryno) {
		$dataentryno = str_replace("'","",$dataentryno);
		$query = "SELECT journal_name FROM journal_data_entry_master a, journal_master b WHERE a.journal_no = b.journal_no AND a.data_entry_no = '$dataentryno'";
		return $this->db->query($query)->result();
	}
	
	function get_progressive_details_datavalidateno($dataentryno) {
		$dataentryno = str_replace("'","",$dataentryno);
		$query = "SELECT journal_name, data_validate_no FROM journal_data_entry_master a, journal_master b, journal_data_validate_master c WHERE a.journal_no = b.journal_no AND a.data_entry_no = c.data_entry_no AND c.data_entry_no = '$dataentryno'";
        return $this->db->query($query)->result();
	}
	
	function get_nonprogressive_details($jid) {
		$jid = str_replace("'","",$jid);
		$query = "SELECT journal_name FROM journal_master_nonprogressive WHERE journal_no = '$jid'";
		return $this->db->query($query)->result();
	}

    /*function to get journal_name based on data_entry_no for pending journals progressive. done by jane*/
    function get_progressive_pending_journals($dataentryno) {
        $dataentryno = str_replace("'","",$dataentryno);
        $query = "SELECT journal_name FROM journal_data_entry_master a, journal_master b WHERE a.journal_no = b.journal_no AND a.data_entry_no = '$dataentryno'";
        return $this->db->query($query)->result();
    }

    /*function to get journal_name based on data_entry_no for pending journals non-progressive. done by jane*/
    function get_nonprogressive_pending_journals($jid) {
        $jid = str_replace("'","",$jid);
        $query = "SELECT journal_name FROM journal_master_nonprogressive WHERE journal_no = '$jid'";
        return $this->db->query($query)->result();
    }
	
	// Not sanitized, please do not use directly
	private function insert_queue($user_id, $queue_type, $journal_type, $jid) {
		$data = json_encode(array("type"=>$journal_type, "jid"=>$jid));
		$query = "INSERT INTO email_queue(user_id,type,data,timestamp) VALUES ('$user_id','$queue_type','$data',CURRENT_TIMESTAMP)";
		return $this->db->query($query);
	}
	
	function insert_queue_published($user_id, $journal_type, $jid) {
		return $this->insert_queue($user_id, 'published', $journal_type, $jid);
	}
	
	function insert_queue_rejected_nonprogressive($user_id, $jid) {
		return $this->insert_queue($user_id, 'rejected', 'nonprogressive', $jid);
	}
	
	function insert_queue_rejected_progressive($user_id, $jid) {
		return $this->insert_queue($user_id, 'rejected', 'progressive', $jid);
	}

    /*function to insert pending data entry to the table email_queue. done by jane*/
	function insert_queue_pending($user_id, $journal_status, $journal_type, $journal_no) {
		return $this->insert_queue($user_id, $journal_status, $journal_type, $journal_no);
	}

	function delete_queue($sent) {
		$this->db->where_in('id',$sent);
		return $this->db->delete('email_queue');
	}
	
	function add_log($status, $user_id, $user_fullname, $user_email, $log_data) {
		$query = "INSERT INTO email_log(status,sent_timestamp,user_id,user_fullname,user_email,log_data) VALUES ('$status',CURRENT_TIMESTAMP,'$user_id','$user_fullname','$user_email','$log_data')";
		return $this->db->query($query);
	}
	
	// Get all queues, unique by user ids, sorted by timestamp from the earliest to the latest
	function get_all_userid_queues() {
		$query = "SELECT DISTINCT ON(user_id) * FROM (SELECT * FROM email_queue ORDER BY timestamp desc) a";
		return $this->db->query($query)->result();
	}
	
	function run_mailer_instance() {
		$query = "UPDATE email_mailer_instance SET last_log = 'Started running', running = 1, last_activity_timestamp = CURRENT_TIMESTAMP, finish_timestamp = null, started_timestamp = CURRENT_TIMESTAMP WHERE id=1";
		$this->db->query($query);
	}
	
	function update_mailer_instance($last_log) {
		$last_log = str_replace("'","",$last_log);
		$query = "UPDATE email_mailer_instance SET last_log = '$last_log', running = 1, last_activity_timestamp = CURRENT_TIMESTAMP WHERE id=1";
		$this->db->query($query);
	}
	
	function finish_mailer_instance() {
		$query = "UPDATE email_mailer_instance SET last_log = 'Finished', running = 0, last_activity_timestamp = CURRENT_TIMESTAMP, finish_timestamp = CURRENT_TIMESTAMP WHERE id=1";
		$this->db->query($query);
	}

    // Get all queues, unique by user ids, sorted by timestamp from the earliest to the latest. done by jane
    function get_all_reminder_userid_queues() {
        $query = "SELECT DISTINCT reminder_user_id, reminder_date FROM user_reminder ORDER BY reminder_date desc";
        return $this->db->query($query)->result();
    }

    /*function to get all reminders. done by jane*/
    function get_all_reminders_for_userid($userid) {
        $userid = str_replace("'","",$userid);
        $query = "SELECT a.data_entry_no, a.nonp_journal_id, a.reminder_message, b.user_id, b.user_full_name, b.email_id, b.sec_role_id, c.reminder_status_desc FROM user_reminder a, sec_user b, reminder_status c WHERE a.reminder_user_id=b.user_id AND a.reminder_status_id=c.reminder_status_id AND a.reminder_user_id='$userid'";
        $result = $this->db->query($query)->result();
        return $result;
    }

    /*function to get journal_name based on data_entry_no for incomplete data entry progressive journals. done by jane*/
    function get_progressive_incomplete_journals($dataentryno) {
        $dataentryno = str_replace("'","",$dataentryno);
        $query = "SELECT journal_name FROM journal_data_entry_master a, journal_master b WHERE a.journal_no = b.journal_no AND a.data_entry_no = '$dataentryno'";
        return $this->db->query($query)->result();
    }

    /*function to get journal_name based on data_entry_no for incomplete data entry non-progressive. done by jane*/
    function get_nonprogressive_incomplete_journals($jid) {
        $jid = str_replace("'","",$jid);
        $query = "SELECT journal_name FROM journal_master_nonprogressive WHERE journal_no = '$jid'";
        return $this->db->query($query)->result();
    }

    /*function to get journal_name, data_validate_no based on data_entry_no for waiting validation progressive. done by jane*/
    function get_progressive_waiting_journals($dataentryno) {
        $dataentryno = str_replace("'","",$dataentryno);
        $query = "SELECT journal_name, data_validate_no FROM journal_data_entry_master a, journal_master b, journal_data_validate_master c WHERE a.journal_no = b.journal_no AND a.data_entry_no = c.data_entry_no AND c.data_entry_no = '$dataentryno'";
        return $this->db->query($query)->result();
    }

    /*function to get journal_name based on journal_no for waiting validation non-progressive. done by jane*/
    function get_nonprogressive_waiting_journals($jid) {
        $jid = str_replace("'","",$jid);
        $query = "SELECT journal_name FROM journal_master_nonprogressive WHERE journal_no = '$jid'";
        return $this->db->query($query)->result();
    }
}
?>