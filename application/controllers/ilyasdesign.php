<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Ilyasdesign extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
	    $this->load->model('assessment','',TRUE);
	    $this->load->model('ilyasmodel','',TRUE);
		$this->load->model('admin','',TRUE);
	}
	
	function index()
	{	
   		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];
			$roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"3");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');
				
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
			
			$id=$this->input->get('jid');
			
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);
			
			$data['cpagename']='ilyasdesign';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(20);
			$data['message']=$message;
			
			//$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detailnonp($id);
			///$data['dataimages']=$this->assessment->show_journal_data_entry_picturenonp($id);
			$data['dataentryno']=$id;
			$data['uoms']=$this->admin->show_uoms();
			$data['lookups']=$this->ilyasmodel->get_lookup_data();
			
			//$data['details']=$this->assessment->show_journal_data_entrynonp($id);
			$data['details']=$this->assessment->show_journalnonp($id);
			//var_dump($data['details']);
			if (sizeOf($data['details']) < 1) return;
			$data['details'] = $data['details'][0];
			/*
			$save = $this->input->post('data');
			if ($save) {
				var_dump($save);
			}
			*/
			
			$data['hot_config'] = $this->ilyasmodel->get_config($id, true);
			$data['hot_data'] = $this->ilyasmodel->get_data($id);
			$data['hot_lock'] = $this->ilyasmodel->get_validationlock($id);
			$data['journal_list'] = $this->assessment->get_journal_list();
			$data['nonp_journal_list'] = $this->ilyasmodel->get_all_journals();
			
			$this->load->view('header', $data1);
			$this->load->view('ilyas_design', $data);
			$this->load->view('footer');
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}
	
	function save_data() {
		if($this->session->userdata('logged_in'))
   		{
			$session_data = $this->session->userdata('logged_in');
			header('Content-Type: application/json');
			$id = $this->input->get('jid');
			//$publish = $this->input->get('publish');
			//$ispublish = (isset($publish) && ($publish == "true"));
			if ($id) {
				$jdetails=$this->assessment->show_journalnonp($id);
				if (sizeOf($jdetails) > 0) {
					/* The journal exists */
					//var_dump($id);
					$data = json_decode($this->input->post("data"));
					//var_dump($data);
					if ((sizeOf($data) < 1) || (!is_array($data)) || (!is_array($data[0]))) {
						// Empty all rows!
						$q = $this->ilyasmodel->empty_rows($id);
					} else {
						$q = $this->ilyasmodel->save_data($id, $data, $session_data['id'], false, false);
					
					}
					echo json_decode($q);
					//print_r(json_decode($this->input->get("data")));
				}
				//print_r(json_decode($this->input->get("data")));
			}
			//$lol = $this->ilyasmodel->save_data();
			//var_dump($lol);
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}
	
	
	function save_config() {
		if($this->session->userdata('logged_in'))
   		{
			header('Content-Type: application/json');
			$id=$this->input->get('jid');
			
			if ($id) {
				$jdetails=$this->assessment->show_journalnonp($id);
				//var_dump($jdetails);
				if (sizeOf($jdetails) > 0) {
					/* The journal exists */
					
					$config = json_decode($this->input->post("config"),true);
					
					//$config = json_decode('[{"header":"Test1","type":"text","uom":"1","width":200,"order":0},{"header":"Test2","type":"text","uom":"1","width":200,"order":1}]',true);
					//print_r($config);
					$q = $this->ilyasmodel->save_config($id,$config);
					echo json_encode($q);
					//print_r(json_decode($this->input->get("data")));
				}
				//print_r(json_decode($this->input->get("data")));
			}
			//$lol = $this->ilyasmodel->save_data();
			//var_dump($lol);
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}
	/*
	
	function save_data() {
		if($this->session->userdata('logged_in'))
   		{
			header('Content-Type: application/json');
			$id=$this->input->get('jid');
			
			if ($id) {
				$jdetails=$this->assessment->show_journal_data_entrynonp($id);
				if (sizeOf($jdetails) > 0) {
					/* The journal exists *
					$q = $this->ilyasmodel->save_data($id,json_decode($this->input->post("data")));
					echo json_decode($q);
					//print_r(json_decode($this->input->get("data")));
				}
				//print_r(json_decode($this->input->get("data")));
			}
			//$lol = $this->ilyasmodel->save_data();
			//var_dump($lol);
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}
	
	/*function get_config($jid) {
		//header('Content-Type: application/json');
		//print_r($this->ilyasmodel->get_config(19));
		$result = [];
		$q = $this->ilyasmodel->get_config($jid);
		foreach ($q as $i):
			$i = (array) $i;
			array_push($result, [
				'header' => $i['col_header'],
				'width' => $i['col_width'],
				'type' => $i['type'],
				'uom' => $i['uom_id']]);
		endforeach;
		return $result;
	}
	
	function get_data() {
	header('Content-Type: application/json');
		$id=$this->input->get('jid');
		return $this->ilyasmodel->get_data($id);
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
	}*/
}
?>