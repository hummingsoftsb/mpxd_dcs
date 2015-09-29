<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Reminders extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('reminder','',TRUE);
		 $this->load->library('email');
	}

	function index($offset=0)
	{
		// Load Form
		$this->load->helper(array('form','url'));
		$currentpropends = $this->reminder->show_current_progressive_pending(date("Y-m-d"),date("l"));
		foreach($currentpropends as $currentpropend):
			$journalname=$currentpropend->journalname;
			$frequencyno=$currentpropend->frequency_detail_name;
			$emailid=$currentpropend->emailid;
			$name=$currentpropend->name;
			$this->email->from('test@hummingsoft.com.my', 'MPXD');
			$this->email->to('naim@hummingsoft.com.my');
			$message="Dear ".$name.",<br>".$journalname." data entry is pending for the week ".$frequencyno.".";
			$this->email->subject($journalname.' data entry pending');
			$this->email->message($message);
			//$this->email->send();
		endforeach;
		$oldpropends = $this->reminder->show_old_progressive_pending(date("Y-m-d"),date("l"));
		foreach($oldpropends as $oldpropend):
			$journalname=$oldpropend->journalname;
			$journalno=$oldpropend->journal_no;
			$dataentryno=$oldpropend->data_entry_no;
			$frequencyno=$this->reminder->show_old_progressive_freqname($journalno,$dataentryno);
			$emailid=$oldpropend->emailid;
			$name=$oldpropend->name;
			$this->email->from('test@hummingsoft.com.my', 'MPXD');
			$this->email->to('naim@hummingsoft.com.my');
			$message="Dear ".$name.",<br>".$journalname." data entry is pending for the weeks ".$frequencyno.".";
			$this->email->subject($journalname.' data entry pending');
			$this->email->message($message);
			//$this->email->send();
		endforeach;
		$currentpropvals=$this->reminder->show_current_progressive_pending_val(date("Y-m-d"),date("l"));
		foreach($currentpropvals as $currentpropval):
			$journalname=$currentpropval->journalname;
			$frequencyno=$currentpropval->frequency_detail_name;
			$emailid=$currentpropval->emailid;
			$name=$currentpropval->name;
			$this->email->from('test@hummingsoft.com.my', 'MPXD');
			$this->email->to('naim@hummingsoft.com.my');
			$message="Dear ".$name.",<br>".$journalname." validation is pending for the week ".$frequencyno.".";
			$this->email->subject($journalname.' validation pending');
			$this->email->message($message);
			//$this->email->send();
		endforeach;
		$data['data']="";

		$this->load->view('reminders', $data);
	}
	
	function update(){
		$this->reminder->update_reminder();
		echo "Done.";
	}
}
?>