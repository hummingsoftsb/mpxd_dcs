<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Projectstatus extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('timeline','',TRUE);
   	   $this->load->model('securitys','',TRUE);
	   $this->load->model('alertreminder','',TRUE);
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

		    $roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"1");
 			foreach ($roleperms as $roleperm):
 				$viewperm=$roleperm->view_opt;
 				$addperm=$roleperm->add_opt;
 				$editperm=$roleperm->edit_opt;
 				$delperm=$roleperm->del_opt;
 			endforeach;
 			if($viewperm==0)
 				redirect('/home','refresh');

 			if($this->uri->uri_string()=="projectstatus" && $_SERVER['QUERY_STRING']=="")
 			{
 				$this->session->unset_userdata('selectrecord');
 				$this->session->unset_userdata('searchrecord');
 			}
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
 			$config['base_url'] = base_url().'index.php/projectstatus/index';
 			$config['total_rows'] = $this->timeline->totalprojstat($search);
 			if($this->session->userdata('selectrecord'))
 			{
 				$selectrecord=$this->session->userdata('selectrecord');
 				$config['per_page'] = $selectrecord['selectrecord'];
 			}
 			else
 			{
 				$config['per_page'] = 10;
 			}
 			$config['uri_segment'] = 3;
 			$config["num_links"] = 1;

 			// Initialize
 			$this->pagination->initialize($config);
 			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


 			//Load all record data
 			$data['records'] = $this->timeline->show_projstat($search,$offset,$config['per_page']);
 			$data['totalrows'] = $config['total_rows'];
 			$data['mpage'] = $config['per_page'];
 			$data['page']= $page+1;
 			$data['selectrecord']=$config['per_page'];
 			$data['searchrecord']=$search;
 			$data['cpagename']='projectstatus';
 			$data['labels']=$this->securitys->get_label(1);
			$data['labelgroup']=$this->securitys->get_label_group(1);
			$data['labelobject']=$this->securitys->get_label_object(1);
 			$data['addperm']=$addperm;
 			$data['editperm']=$editperm;
 			$data['delperm']=$delperm;
 			$data['message']=$message;


			//calculate percentage for each project
			$data['projstat'] = array ();
			foreach ( $data['records'] as $pstat )
			{
				$datproj="";
				$data['projstat1'] = $this->timeline->show_projper($pstat->project_no);
				foreach ( $data['projstat1'] as $pstat1 )
				{
					$datproj.=$pstat1->cper.",".$pstat1->cavg.",777,";
				}
				$data['projstat'][$pstat->project_no]=$datproj;
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

			$this->load->view('header', $data1);
		    $this->load->view('timeline_projectstatus', $data);
		    $this->load->view('footer');
		}
		else
		{
		  	//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function selectrecord()
	{
		$sess_array = array(
	         'selectrecord' => $this->input->post('recordselect')
	       );
	    $this->session->set_userdata('selectrecord', $sess_array);

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
	}

	function search()
	{
		$this->index();
	}
}
?>