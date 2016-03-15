<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Designtemplate extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('design','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"6");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="designtemplate" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/designtemplate/index';
			$config['total_rows'] = $this->design->totalprojtmp($search);
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
			$data['records'] = $this->design->show_projtmp($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='designtemplate';
			$data['labels']=$this->securitys->get_label(6);
			$data['labelgroup']=$this->securitys->get_label_group(6);
			$data['labelobject']=$this->securitys->get_label_object(6);
			$data['users']=$this->securitys->show_users();
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
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
			$this->load->view('design_template', $data);
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
		$label=$this->securitys->get_label_object_name(38);
		$label1=$this->securitys->get_label_object_name(39);
		$label2=$this->securitys->get_label_object_name(40);
		$label3=$this->securitys->get_label_object_name(41);
		$label4=$this->securitys->get_label_object_name(42);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('desc', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('startdate', $label3, 'trim|required|xss_clean|numeric_dash|callback_valid_date');
		$this->form_validation->set_rules('enddate', $label4, 'trim|required|xss_clean|numeric_dash');


		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('name'),'msg1'=>form_error('desc'),'msg2'=>form_error('user'),'msg3'=>form_error('startdate'),'msg4'=>form_error('enddate')));
		}
		else
		{
			$name=$this->input->post('name');
			$startdate=date("Y-m-d", strtotime($this->input->post('startdate')));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			if($this->design->add_check_projtmp($name)==0)
			{
				$data = array('project_name' => $name,'project_definition' => $this->input->post('desc'),'user_id' => $this->input->post('user'),'start_date' => $startdate ,'end_date' => $enddate);

				//query the database
				$result = $this->design->add_projtmp($data);
				$sess_array = array('message' => $this->securitys->get_label_object(6)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
		}
	}

	function valid_date()
	{
		$label3=$this->securitys->get_label_object_name(41);
		$label4=$this->securitys->get_label_object_name(42);
		if($this->input->post('startdate')!='' && $this->input->post('enddate')!='')
		{
			$startdate=date("Y-m-d", strtotime($this->input->post('startdate')));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			if($startdate>$enddate)
			{
				$this->form_validation->set_message('valid_date', $label4.' should be greater than '.$label3);
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(38);
		$label1=$this->securitys->get_label_object_name(39);
		$label2=$this->securitys->get_label_object_name(40);
		$label3=$this->securitys->get_label_object_name(41);
		$label4=$this->securitys->get_label_object_name(42);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name1', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('desc1', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user1', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('startdate1', $label3, 'trim|required|xss_clean|numeric_dash|callback_valid_date1');
		$this->form_validation->set_rules('enddate1', $label4, 'trim|required|xss_clean|numeric_dash');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('name1'),'msg1'=>form_error('desc1'),'msg2'=>form_error('user1'),'msg3'=>form_error('startdate1'),'msg4'=>form_error('enddate1')));
		}
		else
		{
			$id=$this->input->post('editid');
			$name=$this->input->post('name1');
			$startdate=date("Y-m-d", strtotime($this->input->post('startdate1')));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate1')));
			if($this->design->update_check_projtmp($id,$name)==0)
			{
				$data = array('project_name' => $name,'project_definition' => $this->input->post('desc1'),'user_id' => $this->input->post('user1'),'start_date' => $startdate,'end_date' => $enddate);

				//query the database
				$result = $this->design->update_projtmp($id,$data);
				$sess_array = array('message' => $this->securitys->get_label_object(6)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
		}
	}

	function valid_date1()
	{
		$label3=$this->securitys->get_label_object_name(41);
		$label4=$this->securitys->get_label_object_name(42);
		if($this->input->post('startdate1')!='' && $this->input->post('enddate1')!='')
		{
			$startdate=date("Y-m-d", strtotime($this->input->post('startdate1')));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate1')));
			if($startdate>$enddate)
			{
				$this->form_validation->set_message('valid_date1', $label4.' should be greater than '.$label3);
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	function delete()
	{
		$id=$this->input->post('id');
		if($this->design->delete_check_projtmp($id)==0)
		{
			//query the database
			$result = $this->design->delete_projtmp($id);
			$sess_array = array('message' => $this->securitys->get_label_object(6)." Deleted Successfully","type" => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(6).", Assigned to ".$this->securitys->get_label_object(7),"type" => 0);
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