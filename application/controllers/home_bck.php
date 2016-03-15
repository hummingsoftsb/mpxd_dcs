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
	}
	
	function index()
	{	
   		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];

			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			/*$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);*/
            $data1['alertcount']=count($data1['alerts']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);

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
	}

	function hidereminder()
	{
		$id=$this->input->post('id');
		$this->alertreminder->hide_reminder($id);
	}
}
?>