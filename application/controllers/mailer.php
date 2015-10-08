<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailer extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('mailermodel');
	}

	public function index()
	{
		header("content-type: application/json");
		$ids = $this->mailermodel->get_all_userid_queues();
		if (sizeOf($ids) < 1) {
			echo "No queue";
			return;
		}
		
		set_time_limit(0);
		
		$this->mailermodel->run_mailer_instance();
		$sent_count = 0;
		foreach($ids as $id):
			$status = $this->send($id->user_id);
			$this->mailermodel->update_mailer_instance('Email sent');
			if ($status) $sent_count = $sent_count + sizeOf(array_keys(array_filter($status, function($a){ return $a == true; })));
		endforeach;
		$this->mailermodel->finish_mailer_instance();
		echo "Successfully sent $sent_count emails.";
	}
	
	
	private function send($userid) {
		$this->load->library('swiftmailer');
		$statuses = array();
		$rows = $this->mailermodel->get_all_for_userid($userid);
		
		if (sizeOf($rows) < 1) return;
		$all = array();
		foreach($rows as $r):
			if (!isset($all[$r->type])) $all[$r->type] = array();
			array_push($all[$r->type], $r);
		endforeach;
		
		$user_fullname = $rows[0]->user_full_name;
		$user_email = $rows[0]->email_id;
		$user_id = $rows[0]->user_id;
		// Published journals
		if (isset($all['published'])) {
			$statuses['published'] = $this->send_published($user_id,$user_email,$user_fullname,$all['published']);
		}
		
		if (isset($all['rejected'])) {
			$statuses['rejected'] = $this->send_rejected($user_id,$user_email,$user_fullname,$all['rejected']);
		}
		
		return $statuses;
	}
	
	private function send_published($user_id,$user_email,$user_fullname,$items) {
		$status = false;
		$sendables = array();
		$sent = array();
		foreach($items as $item):
			$data = json_decode($item->data, true);
			// JSON data corrupted, skip this.
			if (is_null($data)) continue;
			if (($data['type']) == "progressive") { $q = $this->mailermodel->get_progressive_details_datavalidateno($data['jid']); }
			else if (($data['type']) == "nonprogressive") $q = $this->mailermodel->get_nonprogressive_details($data['jid']);
			if (sizeOf($q) < 1) continue;
			$journalname = $q[0]->journal_name;
			array_push($sendables, array(
				'type' => $data['type'],
				'jid' => ($data['type'] == "progressive") ? $q[0]->data_validate_no : $data['jid'],
				'journalname' => $journalname
			));
			array_push($sent, $item->id);
		endforeach;
		if (sizeOf($sendables) > 0) {
		
			$sent_result = $this->swiftmailer->send_collective_published($user_email,$user_fullname,$sendables);
			if ($sent_result['status'] > 0) {
				// Delete all sent
				$this->mailermodel->delete_queue($sent);
				$status = true;
			}
			// Store logs
			$log_data = array(
				'email' => 'published',
				'sendables' => $sendables
			);
			$this->mailermodel->add_log($sent_result['status'], $user_id, $user_fullname, $user_email, json_encode($log_data));
		}
		return $status;
	}
	
	private function send_rejected($user_id,$user_email,$user_fullname,$items) {
		$status = false;
		$sendables = array();
		$sent = array();
		foreach($items as $item):
			$data = json_decode($item->data, true);
			// JSON data corrupted, skip this.
			if (is_null($data)) continue;
			if (($data['type']) == "progressive") $q = $this->mailermodel->get_progressive_details_dataentry($data['jid']); // This uses data entry no instead of journal no
			else if (($data['type']) == "nonprogressive") $q = $this->mailermodel->get_nonprogressive_details($data['jid']);
			
			if (sizeOf($q) < 1) continue;
			$journalname = $q[0]->journal_name;
			array_push($sendables, array(
				'type' => $data['type'],
				'jid' => $data['jid'],
				'journalname' => $journalname
			));
			array_push($sent, $item->id);
		endforeach;
		if (sizeOf($sendables) > 0) {
			$sent_result = $this->swiftmailer->send_collective_rejected($user_email,$user_fullname,$sendables);
			if ($sent_result['status'] > 0) {
				// Delete all sent
				$this->mailermodel->delete_queue($sent);
				$status = true;
			}
			// Store logs
			$log_data = array(
				'email' => 'rejected',
				'sendables' => $sendables
			);
			$this->mailermodel->add_log($sent_result['status'], $user_id, $user_fullname, $user_email, json_encode($log_data));
		}
		return $status;
	}

}
?>