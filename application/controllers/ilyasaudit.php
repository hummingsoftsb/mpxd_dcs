<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class IlyasAudit extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url','general'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
	   $this->load->model('assessment','',TRUE);
	   $this->load->model('ilyasmodel','',TRUE);
   	   $this->load->library('swiftmailer');
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
			//access checking
			if($viewperm==0)
				redirect('/home','refresh');
			if(!$this->assessment->check_access_nonp($session_data['id'],$this->input->get('jid')) && $session_data['roleid'] != 1){
				//die("No access");
				redirect('/journaldataentry','refresh');
			}
			//end access checking
				
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
			
			$data['cpagename']='ilyasaudit';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(5);
			$data['message']=$message;
			
			//$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detailnonp($id);
			//$data['dataimages']=$this->assessment->show_journal_data_entry_picturenonp($id);
			$data['dataentryno']=$id;
			
			$data['details']=$this->assessment->show_journalnonp($id);
			$data['validator']=$this->assessment->show_journalnp_validator($id);
			//var_dump($id);
			//var_dump($data['validator']);
			if (sizeOf($data['details']) < 1) return;
			$data['details'] = $data['details'][0];
			$data['lookups'] = $this->ilyasmodel->get_lookup_data();
			//var_dump($data);
			/*
			$save = $this->input->post('data');
			if ($save) {
				var_dump($save);
			}
			*/
			
			$audit_data = $this->ilyasmodel->get_audit_data($id);
			/*echo json_encode($audit_data);
			echo json_encode($this->ilyasmodel->get_config($id));
			die();*/
			$data['hot_config'] = $audit_data['headers'];//$this->ilyasmodel->get_config($id);
			//echo json_encode($data['hot_config']);die();
			$data['hot_revisions'] = $this->ilyasmodel->get_audit_revisions($id);
			//var_dump($data['hot_revisions']);die();
			$data['hot_data'] = $audit_data['data'];
			//var_dump($data['hot_data']);die();
			$data['hot_lock'] = $this->ilyasmodel->get_validationlock($id);
			$data['hot_read_only_rows'] = $this->ilyasmodel->get_read_only_rows($id);
			$data['hot_comments'] = [];//$this->ilyasmodel->get_validation_comment($id);
			$data['new_comments'] = $this->ilyasmodel->get_validation_comment_row($id);
			$data['data_date'] = $this->ilyasmodel->get_data_date($id);
			//echo json_encode($data['hot_data']);
			//die();
			$this->load->view('header', $data1);
			$this->load->view('ilyasaudit', $data);
			$this->load->view('footer');
		}
   		else
   		{
     		//If no session, redirect to login page
			if ($this->input->get("jid") != "") redirect(make_login_redirect('index.php/'.uri_string()."?jid=".$this->input->get("jid")), 'refresh');
     		else redirect('/login', 'refresh');
   		}
	}
	
	
	function get_data()
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
			//access checking
			if($viewperm==0)
				die();
			if(!$this->assessment->check_access_nonp($session_data['id'],$this->input->get('jid')) && $session_data['roleid'] != 1){
				die();
				//die("No access");
				//redirect('/journaldataentry','refresh');
			}
			//end access checking
				
			$jid=$this->input->get('jid');
			$rev=$this->input->get('rev');
			
			if (($jid == '') || ($rev == '')) die();
			$result = array();
			$user = $this->ilyasmodel->get_user_id($jid, $rev)[0];
			$result['data'] = $this->ilyasmodel->get_audit_data($jid, $rev);
			$result['user'] = array(
				'user_id' => $user->user_id,
				'user_full_name' => $user->user_full_name
			);
			
			echo json_encode($result);
		}
   		else
   		{
     		
   		}
	}
	
}
?>