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
			$data['labels']=$this->securitys->get_label(9);
			$data['labelgroup']=$this->securitys->get_label_group(9);
			$data['labelobject']=$this->securitys->get_label_object(9);
			$data['inputtypes']=$this->admin->show_inputtype();
			$data['attbgroups']=$this->admin->show_attbgroup();
			$data['lookups']=$this->admin->show_lookupdatas();
			$data['datatypes']=$this->admin->show_datatype();
			$data['uoms']=$this->admin->show_uoms();
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
		$label=$this->securitys->get_label_object_name(7);
		$label1=$this->securitys->get_label_object_name(8);
		$label2=$this->securitys->get_label_object_name(9);
		$label3=$this->securitys->get_label_object_name(10);
		$label4=$this->securitys->get_label_object_name(11);
		$label5=$this->securitys->get_label_object_name(12);
		$label6=$this->securitys->get_label_object_name(13);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('label', $label, 'trim|required|xss_clean');
		if($this->input->post('inputtype')=="1")
		{
			$this->form_validation->set_rules('datatype', $label4, 'trim|required|xss_clean');
			if($this->input->post('datatype')=="3")
				$this->form_validation->set_rules('decimaldigits', $label5, 'trim|required|xss_clean|is_natural|callback_maximumCheck');
			$this->form_validation->set_rules('uom', $label6, 'trim|required|xss_clean');

		}
		if($this->input->post('inputtype')=="2")
		{
			$this->form_validation->set_rules('lookup', $label3, 'trim|required|xss_clean');
			$this->form_validation->set_rules('uom', $label6, 'trim|required|xss_clean');
		}

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('label'),'msg1'=>form_error('decimaldigits'),'msg2'=>form_error('lookup'),'msg3'=>form_error('datatype'),'msg4'=>form_error('uom'),'msg5'=>''));
		}
		else
		{
			$label=$this->input->post('label');
			$inputtype=$this->input->post('inputtype'); 
			$dtype=$this->input->post('datatype');
			if($this->admin->add_check_dataatt($label,$inputtype,$dtype)==0)
			{	
				if($this->input->post('fieldlock')=="on")
				{
					$fieldllock="1";
				}
				else
				{
					$fieldllock="0";
				}
				if($this->input->post('inputtype')=="1")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_attribute_group_id' => $this->input->post('attbgroup'),'data_attb_data_type_id' => $this->input->post('datatype'),'data_attb_digits' => $this->input->post('decimaldigits'),'uom_id' => $this->input->post('uom'));
				}
				else if($this->input->post('inputtype')=="2")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_attribute_group_id' => $this->input->post('attbgroup'),'data_set_id' => $this->input->post('lookup'),'uom_id' => $this->input->post('uom'));
				}
				else if($this->input->post('inputtype')=="3")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_attribute_group_id' => $this->input->post('attbgroup'),'data_attb_data_type_id' => '1','uom_id' =>  $this->input->post('uom'),'field_lock'=>$fieldllock);
				}
				else if($this->input->post('inputtype')=="4")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype'),'data_attribute_group_id' => $this->input->post('attbgroup'),'field_lock'=>$fieldllock);
				}

				//query the database
				$result = $this->admin->add_dataatt($data);
				$sess_array = array('message' => $this->securitys->get_label_object(9)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>''));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(7);
		$label1=$this->securitys->get_label_object_name(8);
		$label2=$this->securitys->get_label_object_name(9);
		$label3=$this->securitys->get_label_object_name(10);
		$label4=$this->securitys->get_label_object_name(11);
		$label5=$this->securitys->get_label_object_name(12);
		$label6=$this->securitys->get_label_object_name(13);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('label1', $label, 'trim|required|xss_clean');

		if($this->input->post('inputtype1')=="1")
		{
			$this->form_validation->set_rules('datatype1', $label4, 'trim|required|xss_clean');
			if($this->input->post('datatype1')=="3")
				$this->form_validation->set_rules('decimaldigits1', $label5, 'trim|required|xss_clean|is_natural');
			$this->form_validation->set_rules('uom1', $label6, 'trim|required|xss_clean');
		}
		else if($this->input->post('inputtype1')=="2")
		{
			$this->form_validation->set_rules('lookup1', $label3, 'trim|required|xss_clean');
			$this->form_validation->set_rules('uom1', $label6, 'trim|required|xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('label1'),'msg1'=>form_error('decimaldigits1'),'msg2'=>form_error('lookup1'),'msg3'=>form_error('datatype1'),'msg4'=>form_error('uom1'),'msg5'=>''));
		}
		else
		{
			$id=$this->input->post('datasetid');
			$inputtype=$this->input->post('inputtype1');
			$dtype=$this->input->post('datatype1');
			$label=$this->input->post('label1');
			if($this->admin->add_check_dataatt($label,$inputtype,$dtype)==0)
			{
				if($this->input->post('fieldlock1')=="on")
				{
					$fieldllock="1";
				}
				else
				{
					$fieldllock="0";
				}

				if($this->input->post('inputtype1')=="1")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype1'),'data_set_id' => NULL,'data_attribute_group_id' => $this->input->post('attbgroup1'),'data_attb_data_type_id' => $this->input->post('datatype1'),'data_attb_digits' => $this->input->post('decimaldigits1'),'uom_id' => $this->input->post('uom1'));
				}
				else if($this->input->post('inputtype1')=="2")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype1'),'data_attribute_group_id' => $this->input->post('attbgroup1'),'data_set_id' => $this->input->post('lookup1'),'data_attb_data_type_id' => NULL,'data_attb_digits' => '0','uom_id' => $this->input->post('uom1'));
				}
				else if($this->input->post('inputtype1')=="3")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype1'),'data_attribute_group_id' => $this->input->post('attbgroup1'),'data_attb_data_type_id' => '1','uom_id' =>  $this->input->post('uom1'),'data_set_id' => NULL,'field_lock'=>$fieldllock);
				}
				else if($this->input->post('inputtype1')=="4")
				{
					$data = array('data_attb_label' => $label,'data_attb_type_id' => $this->input->post('inputtype1'),'data_attribute_group_id' => $this->input->post('attbgroup1'),'data_set_id' => NULL,'data_attb_data_type_id' => NULL,'data_attb_digits' => '0','uom_id'=>NULL,'field_lock'=>$fieldllock);
				}


				//query the database
				$result = $this->admin->update_dataatt($id,$data);
				$sess_array = array('message' => $this->securitys->get_label_object(9)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label." already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>''));
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
			$sess_array = array('message' => $this->securitys->get_label_object(17)." Deleted Successfully","type" => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(9).", Assigned to ".$this->securitys->get_label_object(7),"type" => 0);
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

	function maximumCheck($num)
	{
	    if (($num > 4 )|| ($num==0))
	    {
	        $this->form_validation->set_message('maximumCheck','The decimal digit field must be greater than 0 or less than 5');
	        return FALSE;
	    }
	    else
	    {
	        return TRUE;
	    }
	}
}
?>