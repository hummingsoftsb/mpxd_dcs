<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Securitylabel extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
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

			$roleperms=$this->securitys->show_permission_object_data($roleid,"18");

			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$editperm=$roleperm->edit_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="securitylabel" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/securitylabel/index';
			$config['total_rows'] = $this->securitys->totallabel($search);
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
			$data['records'] = $this->securitys->show_label($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='securitylabel';
			$data['labels']=$this->securitys->get_label(18);
			$data['labelgroup']=$this->securitys->get_label_group(18);
			$data['labelobject']=$this->securitys->get_label_object(18);
			$data['editperm']=$editperm;
			$data['message']=$message;
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			/*$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);*/
            $data1['alertcount']=count($data1['alerts']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);
			
			$data['message_type']=$type;


			$this->load->view('header', $data1);
			$this->load->view('security_label', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			redirect('/login', 'refresh');
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(16);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('labeldesc', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => validation_errors()));
		}
		else
		{
			$id=$this->input->post('labelid');
			$groupid=$this->input->post('groupid');
			$objid=$this->input->post('objid');
			$desc=$this->input->post('labeldesc');
			if($this->securitys->update_check_label($id,$desc,$objid)==0)
			{
				$data = array('sec_label_id' => $this->input->post('labelid'),'sec_label_desc' => $desc);

				//query the database
				$result = $this->securitys->update_label($id,$data);
				$sess_array = array('message' =>  $this->securitys->get_label_object(18)." Updated Successfully","type" => 1);
			    $this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists"));
			}
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