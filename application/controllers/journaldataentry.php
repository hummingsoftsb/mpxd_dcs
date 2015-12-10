<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Journaldataentry extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('assessment','',TRUE);
   	   $this->load->model('securitys','',TRUE);
	   $this->load->model('alertreminder','',TRUE);
	   $this->load->model('design','',TRUE);
   	   $this->load->model('securitys','',TRUE);
	   $this->load->model('ilyasmodel','',TRUE);
	}

	function index($offset=0)
	{
		// Load Form
		$this->load->helper(array('form','url'));

   		// Load Pagination
		$this->load->library('pagination');
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
		    $data['username'] = $session_data['username'];
		    $userid=$session_data['id'];

		    $roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"3");
 			foreach ($roleperms as $roleperm):
 				$viewperm=$roleperm->view_opt;
 				$addperm=$roleperm->add_opt;
 				$editperm=$roleperm->edit_opt;
 				$delperm=$roleperm->del_opt;
					$cweek=Date('W');
 			endforeach;
 			if($viewperm==0)
 				redirect('/home','refresh');

 			if($this->uri->uri_string()=="journaldataentry" && $_SERVER['QUERY_STRING']=="")
 			{
 				$this->session->unset_userdata('selectrecord');
 				$this->session->unset_userdata('searchrecord');
 			}
 			if($this->session->userdata('message'))
 			{
 				$messagehrecord=$this->session->userdata('message');
 				$message=$messagehrecord['message'];
 				$type=$messagehrecord['type'];
 				$this->session->unset_userdata('message');
 			}
 			else
 			{
 				$message='';
 				$type = '';
 			}
			
 			if($this->session->userdata('searchrecord'))
 			{
 				$searchrecord=$this->session->userdata('searchrecord');
				$search=$searchrecord['searchrecord'];
 			}
 			else
 			{
 				$search='';
 			}

 			// Config setup for Pagination
 			$config['base_url'] = base_url().'index.php/journaldataentry/index';
			//$config['base_url'] = base_url().'index.php/journaldataentrynonp_ilyas/index';
 			$config['total_rows'] = $this->assessment->totalpjde($search,$userid);
 			if($this->session->userdata('selectrecord'))
 			{
 				$selectrecord=$this->session->userdata('selectrecord');
 				$config['per_page'] = $selectrecord['selectrecord'];
 			}
 			else
 			{
 				$config['per_page'] = 10000;
 			}
 			$config['uri_segment'] = 3;
 			$config["num_links"] = 2;

 			// Initialize
 			$this->pagination->initialize($config);
 			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


 			//Load all progressive record data
 			$data['records'] = $this->assessment->show_pjde($search,$offset,$config['per_page'],$userid,$roleid);
 			$data['totalrows'] = $config['total_rows'];
 			$data['mpage'] = $config['per_page'];
 			$data['page']= $page+1;
 			$data['selectrecord']=$config['per_page'];
 			$data['searchrecord']=$search;
 			$data['cpagename']='journaldataentry';
			$data['labels']=$this->securitys->get_label(3);
			//$data['labels']=$this->securitys->JOIN(get_label(3),get_label(21));
			$data['labelgroup']=$this->securitys->get_label_group(3);
			$data['labelobject']=$this->securitys->get_label_object(3);
 			$data['addperm']=$addperm;
 			$data['editperm']=$editperm;
 			$data['delperm']=$delperm;
 			$data['message']=$message;
 			
 			$data['message_type']=$type;

			//Load data entry owner for each journal
			$data['pjdefreq'] = array ();
			$cweek=Date('W');
			foreach ( $data['records'] as $k => $freq )
			{
				$pjde="";
				$isthisweek = false;
				$data['freq1'] = $this->assessment->show_pjde_id($freq->journal_no);
				foreach ( $data['freq1'] as $pjdefre )
				{
					$count=$this->assessment->get_journal_data_entry_detail($pjdefre->data_entry_no);
					if($pjdefre->data_entry_status_id==1 && $pjdefre->frequency_period<=$cweek)
					{
						$isthisweek = true;
						$pjde.="<a href='javascript:void(0)' data-id='".$pjdefre->data_entry_no."' data-count='".$count."' class='modalentry'>".$pjdefre->frequency_detail_name."</a> ,";
					}
					else if ($pjdefre->data_entry_status_id==3 || ($pjdefre->data_entry_status_id==1 && $pjdefre->frequency_period>$cweek) )
					{
						$pjde.="<a href='javascript:void(0)'  style='color: grey;'>".$pjdefre->frequency_detail_name."</a> ,";
					}
					else if ($pjdefre->data_entry_status_id==0 && $pjdefre->frequency_period<=$cweek) {
						$pjde.="<a href='javascript:void(0)'  style='color: grey;'>".$pjdefre->frequency_detail_name."</a> ,";
					}
	        	}
				if(!$isthisweek) {
					unset($data['records'][$k]);
                }
				else
				$data['pjdefreq'][$freq->journal_no]=$pjde;
			}
           // print_r($data['pjdefreq']);
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);
			
			$data['search'] = "";
			if ($this->input->get('search')) {
				$data['search'] = $this->input->get('search');
			}
			//Non-p function
			$nonp_journals = $this->journal_nonp();
			
			$npjs = array();
			foreach($nonp_journals['records'] as $npj){
				$np_journal = new stdClass;
				$np_journal->project_name = $npj->project_name;
				$np_journal->journal_name = $npj->journal_name;
				$np_journal->journal_no = $npj->journal_no;
				$npjs[] = $np_journal;
			}
			$combined_records = array();
			foreach($data['records'] as $r){
				$combined_records[] = $r;
			}
			foreach($npjs as $r){
				$combined_records[] = $r;
			}
			$data['records'] = $combined_records; 
			
			$combined_pjdefreq = array();
			foreach($data['pjdefreq'] as $k => $r){
				$combined_pjdefreq[$k] = $r;
			}
			//Non-P journals
			foreach($nonp_journals['pjdefreq'] as $k => $r){
				$combined_pjdefreq[$k] = '<a href="'.base_url().'index.php/ilyas?jid='.$k.'">Update</a>';
			}
			
			$data['pjdefreq'] = $combined_pjdefreq;
			
			$this->load->view('header', $data1);
		    $this->load->view('assess_journalentries', $data);
		    $this->load->view('footer');
		}
		else
		{
		  	//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	private function journal_nonp($offset=0)
	{
		// Load Form
		$this->load->helper(array('form','url'));
   		
   		// Load Pagination
		$this->load->library('pagination');
		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
		    $data['username'] = $session_data['username'];
		    $userid=$session_data['id'];
		    
		    $roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"21");
 			foreach ($roleperms as $roleperm):
 				$viewperm=$roleperm->view_opt;
 				$addperm=$roleperm->add_opt;
 				$editperm=$roleperm->edit_opt;
 				$delperm=$roleperm->del_opt;
 			endforeach;
 			/*if($viewperm==0)
 				redirect('/home','refresh');*/

 			if($this->uri->uri_string()=="journaldataentrynonp_ilyas" && $_SERVER['QUERY_STRING']=="")
 			{
 				$this->session->unset_userdata('selectrecord');
 				$this->session->unset_userdata('searchrecord');
 			}
 			if($this->session->userdata('message'))
 			{
 				$messagehrecord=$this->session->userdata('message');
 				$message=$messagehrecord['message'];
 				$type=$messagehrecord['type'];
 				$this->session->unset_userdata('message');
 			}
 			else
 			{
 				$message='';
 				$type = '';
 			}
 			if($this->session->userdata('searchrecord'))
 			{
 				$searchrecord=$this->session->userdata('searchrecord');
 				$search=$searchrecord['searchrecord'];
 			}
 			else
 			{
 				$search='';
 			}
			//var_dump($search);
 			// Config setup for Pagination
 			$config['base_url'] = base_url().'index.php/journaldataentrynonp_ilyas/index';
 			$config['total_rows'] = sizeOf($this->ilyasmodel->get_journals_nonp($search,0,null,$userid,false,$roleid));
 			if($this->session->userdata('selectrecord'))
 			{
 				$selectrecord=$this->session->userdata('selectrecord');
 				$config['per_page'] = $selectrecord['selectrecord'];
 			}
 			else
 			{
 				$config['per_page'] = 999999;
 			}
 			$config['uri_segment'] = 3;
 			$config["num_links"] = 1;

 			// Initialize
 			$this->pagination->initialize($config);
 			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


 			//Load all record data
 			$data['records'] = $this->ilyasmodel->get_journals_nonp($search,$offset,$config['per_page'],$userid,false,$roleid);
			$data['totalrows'] = $config['total_rows'];
 			$data['mpage'] = $config['per_page'];
 			$data['page']= $page+1;
 			$data['selectrecord']=$config['per_page'];
 			$data['searchrecord']=$search;
 			$data['cpagename']='journaldataentrynonp_ilyas';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(21);
 			$data['addperm']=$addperm;
 			$data['editperm']=$editperm;
 			$data['delperm']=$delperm;
 			$data['message']=$message;
 			
 			$data['message_type']=$type;

			//Load data entry owner for each journal
			//$data['pjdefreq'] = $this->design->show_frequency_detail_no(date("Y-m-d"));
			//Load data entry owner for each journal
			$data['pjdefreq'] = array ();
			foreach ( $data['records'] as $freq )
			{
				$pjde="";
				$data['freq1'] = $this->assessment->show_pjdenonp_id($freq->journal_no);
				foreach ( $data['freq1'] as $pjdefre )
				{
					$count=$this->assessment->get_journal_data_entry_detailnonp($pjdefre->data_entry_no);
					if($pjdefre->data_entry_status_id>1)
					{
						$pjde.="<a href='javascript:void(0)' style='color: grey;'>".$pjdefre->frequency_detail_name."</a> ,";
					} 
					else 
					{
						$pjde.="<a href='javascript:void(0)' data-id='".$pjdefre->data_entry_no."' data-count='".$count."' class='modalentry'>".$pjdefre->frequency_detail_name."</a> ,";
					}
	        	}
				$data['pjdefreq'][$freq->journal_no]=$pjde;
			}

			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);


			/*$this->load->view('header', $data1);
		    $this->load->view('assess_journalentrynonp_ilyas', $data);
		    $this->load->view('footer');*/
			
			return array('records' => $data['records'],'pjdefreq' => $data['pjdefreq']);
		}
		else
		{
		  	//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function update()
	{
		$jid=$this->input->post('jid');
		$userid=$this->input->post('selowner');

		//query the database
		$result = $this->design->update_chdeo($jid,$userid);
		$sess_array = array('message' => " Data entry owner changed", "type" => 1);
		$this->session->set_userdata('message', $sess_array);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function dataentry()
	{
		$id=$this->input->post('id');
		$session_data = $this->session->userdata('logged_in');
		$loginid = $session_data['id'];
		$this->assessment->add_journal_data_entry_detail($id,$loginid);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function selectrecord()
	{
		$sess_array = array(
	         'selectrecord' => $this->input->post('recordselect')
	       );
	    $this->session->set_userdata('selectrecord', $sess_array);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function select()
	{
		$this->index();
	}

	function searchrecord()
	{
		$sess_array = array(
	         'searchrecord' => $this->input->post('search')
	       );
	    $this->session->set_userdata('searchrecord', $sess_array);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function search()
	{
		$this->index();
	}
}
?>