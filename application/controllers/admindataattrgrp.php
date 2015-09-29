<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Admindataattrgrp extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('admin','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"10");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="admindataattrgrp" && $_SERVER['QUERY_STRING']=="")
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
				$type='';
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
			$config['base_url'] = base_url().'index.php/admindataattrgrp/index';
			$config['total_rows'] = $this->admin->totaldataattrgrp($search);
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
			$data['records'] = $this->admin->show_dataattrgrp($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='admindataattrgrp';
			$data['labels']=$this->securitys->get_label(17);
			$data['labelgroup']=$this->securitys->get_label_group(17);
			$data['labelobject']=$this->securitys->get_label_object(17);
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);
			
			$data['message_type']=$type;


			$this->load->view('header', $data1);
			$this->load->view('admin_dataattrgrp', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function add()
	{
		$label=$this->securitys->get_label_object_name(30);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('dataattrgrpdesc', $label, 'trim|required|xss_clean|max_length[40]');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('dataattrgrpdesc')));
		}
		else
		{
			$name=$this->input->post('dataattrgrpdesc');
			if($this->admin->add_check_dataattrgrp($name)==0)
			{
				$data = array('data_attribute_group_desc' => $this->input->post('dataattrgrpdesc'));

				//query the database
				$result = $this->admin->add_dataattrgrp($data);
				$sess_array = array('message' => $this->securitys->get_label_object(17)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>''));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(30);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('dataattrgrpdesc1', $label, 'trim|required|xss_clean|max_length[40]');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('dataattrgrpdesc1')));
		}
		else
		{
			$dataattrgrpid=$this->input->post('dataattrgrpid');
			$name=$this->input->post('dataattrgrpdesc1');
			if($this->admin->update_check_dataattrgrp($dataattrgrpid,$name)==0)
			{
				$data = array('data_attribute_group_desc' => $this->input->post('dataattrgrpdesc1'));

				//query the database
				$result = $this->admin->update_dataattrgrp($dataattrgrpid,$data);
				$sess_array = array('message' => $this->securitys->get_label_object(17)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>''));
			}
		}
	}

	function delete()
	{
		$dataattrgrpid=$this->input->post('dataattrgrpid');
		if($this->admin->delete_check_dataattrgrp($dataattrgrpid)==0)
		{
	   		//query the database
			$result = $this->admin->delete_dataattrgrp($dataattrgrpid);
			$sess_array = array('message' => $this->securitys->get_label_object(17)." Deleted Successfully","type" => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(17).", Assigned to ".$this->securitys->get_label_object(9),"type" => 0);
			$this->session->set_userdata('message', $sess_array);
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