<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Securityroles extends CI_Controller
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"13");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="securityroles" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/securityroles/index';
			$config['total_rows'] = $this->securitys->totalrole($search);
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
			$data['records'] = $this->securitys->show_role($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='securityroles';
			$data['labels']=$this->securitys->get_label(13);
			$data['labelgroup']=$this->securitys->get_label_group(13);
			$data['labelobject']=$this->securitys->get_label_object(13);
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
			
			$data['message_type']=$type;

			//Load permission data view
			$data['permi'] = $this->securitys->show_permission();

			//Load permission for each role
			$data['rolepermi'] = array ();
			foreach ( $data['records'] as $role )
			{
				$datap="";
				$data['rolepermis'] = $this->securitys->show_permission_data($role->sec_role_id);
				foreach ( $data['rolepermis'] as $rolep )
				{
					$datap.=$rolep->sec_role_id.",".$rolep->sec_obj_id.",".$rolep->view_opt.",".$rolep->add_opt.",".$rolep->edit_opt.",".$rolep->del_opt.",".$rolep->export_opt.",".$rolep->print_opt.",".$rolep->email_opt.",777,";
	        	}
        		$data['rolepermi'][$role->sec_role_id]=$datap;
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
			$this->load->view('security_roles', $data);
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
		$label=$this->securitys->get_label_object_name(4);
		$label1=$this->securitys->get_label_object_name(5);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('role', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('roledesc',$label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('role'),'msg1'=>form_error('roledesc')));
		}
		else
		{
			if($this->securitys->add_check_role($this->input->post('role'))==0)
			{
				$data = array('sec_role_name' => $this->input->post('role'),'sec_role_desc' => $this->input->post('roledesc'));

				//query the database
				$result = $this->securitys->add_role($this->input->post('role'),$this->input->post('roledesc'));
				$sess_array = array('message' =>  $this->securitys->get_label_object(13)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => $result));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>''));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(4);
		$label1=$this->securitys->get_label_object_name(5);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('role1', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('roledesc1', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('role1'),'msg1'=>form_error('roledesc1')));
		}
		else
		{
			$id=$this->input->post('roleid');
			$rolename=$this->input->post('role1');
			if($this->securitys->update_check_role($id,$rolename)==0)
			{
				$data = array('sec_role_name' => $rolename,'sec_role_desc' => $this->input->post('roledesc1'));

				//query the database
				$result = $this->securitys->update_role($id,$data);
				$sess_array = array('message' => $this->securitys->get_label_object(13)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>''));
			}
		}
	}

	function update_perm()
	{
		$label=$this->securitys->get_label_object_name(6);
		$roleid=$this->input->post('rolepermid');
		$records=$this->securitys->show_objects();
		foreach($records as $record)
		{
			$objid=$record->sec_obj_id;
			$fieldname1=$objid."_1";
			$fieldname2=$objid."_2";
			$fieldname3=$objid."_3";
			$fieldname4=$objid."_4";
			$fieldname5=$objid."_5";
			$fieldname6=$objid."_6";
			$fieldname7=$objid."_7";
			$data=array('view_opt' => $this->input->post($fieldname1),'add_opt' => $this->input->post($fieldname2),'edit_opt' => $this->input->post($fieldname3),'del_opt' => $this->input->post($fieldname4),'export_opt' => $this->input->post($fieldname5),'print_opt' => $this->input->post($fieldname6),'email_opt' => $this->input->post($fieldname7));
			$data1=array('view_opt' => $this->input->post($fieldname1),'add_opt' => $this->input->post($fieldname2),'edit_opt' => $this->input->post($fieldname3),'del_opt' => $this->input->post($fieldname4),'export_opt' => $this->input->post($fieldname5),'print_opt' => $this->input->post($fieldname6),'email_opt' => $this->input->post($fieldname7),'sec_role_id' => $roleid,'sec_obj_id' => $objid);

			$this->securitys->update_role_perm($roleid,$objid,$data,$data1);
		}
		if($this->input->post('roleupdate')=="Add")
		{
			$sess_array = array('message' => $this->securitys->get_label_object(13)." Added Successfully","type" => 1);
		}
		else
		{
			$sess_array = array('message' => $label." Updated Successfully","type" => 1);
		}
		$this->session->set_userdata('message', $sess_array);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function delete()
	{
		$id=$this->input->post('id');

		if($this->securitys->delete_check_role($id)==0)
		{
			//query the database
			$result = $this->securitys->delete_role($id);
			$sess_array = array('message' => $this->securitys->get_label_object(13)." Deleted Successfully","type" => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(18).", Assigned to ".$this->securitys->get_label_object(14),"type" => 0);
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