<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Journalauditlog extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('assessment','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"5");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="journalauditlog" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/journalauditlog/index';
			$config['total_rows'] = $this->assessment->totallog($search);
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
			$data['records'] = $this->assessment->show_log($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='journalauditlog';
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;

			//Load data entry owner for each journal
			$data['audlog'] = array ();
			foreach ( $data['records'] as $aulog )
			{
			$dataulog="";
			$data['aulog1'] = $this->assessment->show_log_id($aulog->data_entry_no);

			foreach ( $data['aulog1'] as $auditlog )
			{
			$pname="";
			$cname="";
			$data['cname2'] = $this->assessment->show_uname($auditlog->cur_user_id);
			foreach ($data['cname2'] as $cname1 )
			{
			$cname=$cname1->user_full_name;
			}
			if($auditlog->prv_user_id!="") {
			$data['pname2'] = $this->assessment->show_uname($auditlog->prv_user_id);
			foreach ($data['pname2'] as $pname1 )
			{
			$pname=$pname1->user_full_name;
			}
			}
			$curdate=date("d-m-Y", strtotime($auditlog->cur_date));
			if($auditlog->prv_date!="") {
			$prvdate=date("d-m-Y", strtotime($auditlog->prv_date));
			} else {
			$prvdate="";
			}
			$dataulog.=$auditlog->data_attb_label.",".$auditlog->cur_value.",".$cname.",".$curdate.",".$auditlog->prv_value.",".$pname.",".$prvdate.",777,";
			}
			$data['audlog'][$aulog->data_entry_no]=$dataulog;
			}
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			/*$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);*/
            $data1['alertcount']=count($data1['alerts']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);

			$this->load->view('header', $data1);
			$this->load->view('assess_journalauditlog', $data);
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