<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Admindataattr extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('admin','',TRUE);
		$this->load->model('securitys','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"9");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="admindataattr" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/admindataattr/index';
			$config['total_rows'] = $this->admin->totaldataatt($search);
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
			$data['records'] = $this->admin->show_dataatt($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='admindataattr';
			$data['inputtypes']=$this->admin->show_inputtype();
			$data['lookups']=$this->admin->show_lookupdatas();
			$data['datatypes']=$this->admin->show_datatype();
			$data['uoms']=$this->admin->show_uoms();
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;

			$this->load->view('header', $data);
			$this->load->view('admin_dataattr', $data);
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
		$this->load->library('form_validation');
		$this->form_validation->set_rules('label', 'Label', 'trim|required|xss_clean');
		$this->form_validation->set_rules('uom', 'Unit of Measurements', 'trim|required|xss_clean');
		if($this->input->post('inputtype')=="1")
		{
			$this->form_validation->set_rules('datatype', 'Data Type', 'trim|required|xss_clean');
			if($this->input->post('datatype')=="3")
				$this->form_validation->set_rules('decimaldigits', 'Decimal Digits', 'trim|required|xss_clean|is_natural');

		}
		if($this->input->post('inputtype')=="2")
		{
			$this->form_validation->set_rules('lookup', 'Lookup Code', 'trim|required|xss_clean');
		}

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('label'),'msg1'=>form_error('decimaldigits'),'msg2'=>form_error('lookup'),'msg3'=>form_error('datatype'),'msg4'=>form_error('uom')));
		}
		else
		{
			$label=$this->input->post('label');
			if($this->admin->add_check_dataatt($label)==0)
			{
				if($this->input->post('inputtype')=="1")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_attb_data_type_id' => $this->input->post('datatype'),'data_attb_digits' => $this->input->post('decimaldigits'),'uom_id' => $this->input->post('uom'));
				}
				else
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_set_id' => $this->input->post('lookup'),'uom_id' => $this->input->post('uom'));
				}

				//query the database
				$result = $this->admin->add_dataatt($data);
				$sess_array = array('message' => "Data Attribute Added Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Label already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
		}
	}

	function update()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('label1', 'Label', 'trim|required|xss_clean');
		$this->form_validation->set_rules('uom1', 'Unit of Measurements', 'trim|required|xss_clean');
		if($this->input->post('inputtype1')=="1")
		{
			$this->form_validation->set_rules('datatype1', 'Data Type', 'trim|required|xss_clean');
			if($this->input->post('datatype1')=="3")
				$this->form_validation->set_rules('decimaldigits1', 'Decimal Digits', 'trim|required|xss_clean|is_natural');
		}
		else
		{
			$this->form_validation->set_rules('lookup1', 'Lookup Code', 'trim|required|xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('label1'),'msg1'=>form_error('decimaldigits1'),'msg2'=>form_error('lookup1'),'msg3'=>form_error('datatype1'),'msg4'=>form_error('uom1')));
		}
		else
		{
			$id=$this->input->post('datasetid');
			$label=$this->input->post('label1');
			if($this->admin->update_check_dataatt($id,$label)==0)
			{
				if($this->input->post('inputtype1')=="1")
				{
					$data = array('data_attb_label' => $label ,'data_attb_type_id' => $this->input->post('inputtype1'),'data_set_id' => NULL,'data_attb_data_type_id' => $this->input->post('datatype1'),'data_attb_digits' => $this->input->post('decimaldigits1'),'uom_id' => $this->input->post('uom1'));
				}
				else
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype1'),'data_set_id' => $this->input->post('lookup1'),'data_attb_data_type_id' => NULL,'data_attb_digits' => '0','uom_id' => $this->input->post('uom1'));
				}


				//query the database
				$result = $this->admin->update_dataatt($id,$data);
				$sess_array = array('message' => "Data Attribute Updated Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Label already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
		}
	}

	function delete()
	{
		$id=$this->input->post('id');
		if($this->admin->delete_check_dataatt($id)==0)
		{
			//query the database
			$result = $this->admin->delete_dataatt($id);
			$sess_array = array('message' => "Data Attribute Deleted Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete Data Attribute, Assigned to Journal");
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