<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Home extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
		$this->load->model('assessment','',TRUE);
	}
	
	function index()
	{	
   		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];

			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);

			if($this->session->userdata('message'))
			{
				$messagehrecord=$this->session->userdata('message');
				$message=$messagehrecord['message'];
				$this->session->unset_userdata('message');
			}
			else
			{
				$message='';
			}
			
			$data['labels']=$this->securitys->get_label(6);
			$data['labelgroup']=$this->securitys->get_label_group(6);
			$data['labelobject']=$this->securitys->get_label_object(6);
			$data['message']=$message;

			$this->load->view('header', $data1);
			$this->load->view('front_home', $data);
			$this->load->view('footer');
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}

	function hidealert()
	{
		$id=$this->input->post('id');
		$this->alertreminder->hide_alert($id);
		//$sess_array = array('message' => "Alert Deleted Successfully");
		//$this->session->set_userdata('message', $sess_array);
	}

	function hidereminder()
	{
		$id=$this->input->post('id');
		$this->alertreminder->hide_reminder($id);
		//$sess_array = array('message' => "Reminder Deleted Successfully");
		//$this->session->set_userdata('message', $sess_array);
	}
	
	function test()
	{
		$this->load->library('swiftmailer');
		$emailres=$this->assessment->publish_journal_data_entry_email("5261","28");
			foreach($emailres as $rows):
				$journalno=$rows->journal_no;
				$journalname=$rows->journalname;
				$dataentryname=$rows->dataentryname;
				$validatorname=$rows->validatorname;
				$validatoremail=$rows->validatoremail;
				$validatorid=$rows->validatorid;
			endforeach;
			
			$this->swiftmailer->data_entry_published_progressive($validatoremail, $validatorname, $dataentryname, $journalname, $journalno);
		//var_dump($this->session->userdata('logged_in'));
		echo "YES";
	}
}
?>