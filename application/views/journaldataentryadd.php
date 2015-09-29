<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI


class Journaldataentryadd extends CI_Controller
{
	
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('assessment','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"14");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="journaldataentryadd" && $_SERVER['QUERY_STRING']=="")
			{
				$this->session->unset_userdata('searchrecord');
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
			$id=$this->input->get('jid');

			//Load all record data
			$data['searchrecord']=$search;
			$data['cpagename']='journaldataentryadd';
			$data['details']=$this->assessment->show_journal_data_entry($id);
			$data['validators']=$this->assessment->show_journal_validator($id);
			$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detail($id);
			$data['lookupdetail']=$this->assessment->show_lookup_details();
			$data['dataimages']=$this->assessment->show_journal_data_entry_picture($id);
			$data['dataentryno']=$id;

			$this->load->view('header', $data);
			$this->load->view('assess_journalentryadd', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function addimage()
	{
		//load the helper
		$this->load->helper('form');
		
		$id=$this->input->post('dataentryno');
		$session_data = $this->session->userdata('logged_in');
		$userid= $session_data['id'];
		
		if (!is_dir('journalimage')) {
            mkdir('./journalimage', 0777, true);
        }
        if (!is_dir('journalimage/'.$id)) {
            mkdir('./journalimage/'.$id, 0777, true);
        }
        if (!is_dir('journalimage/'.$id.'/'.$userid)) {
            mkdir('./journalimage/'.$id.'/'.$userid, 0777, true);
        }
		//Configure
		//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
		$config['upload_path'] = 'journalimage/'.$id.'/'.$userid.'/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']=date('dmYHis');	
		//load the upload library
		$this->load->library('upload', $config);
    
	    $this->upload->initialize($config);
	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('imagedesc', 'Description', 'trim|required|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => '','msg1'=>form_error('imagedesc')));
		}
		else
		{
			//if not successful, set the error message
			if (!$this->upload->do_upload('imagefile')) 
			{
				echo json_encode(array('st'=>0, 'msg' => $this->upload->display_errors(),'msg1'=>form_error('imagedesc')));
			} 
			else 
			{ 
				$filedetails=$this->upload->data();
				$data = array('data_entry_no' => $id,'pict_file_name' => $filedetails['file_name'],'pict_file_path' => $filedetails['file_path'],'pict_definition' => $this->input->post('imagedesc'),'pict_user_id' => $userid,'data_source' => '1');
				$this->assessment->add_journal_data_entry_picture($data);
				$this->assessment->add_seq_journal_data_entry_picture($id);
				echo json_encode(array('st'=>1,'msg'=>'Success'));
			}
		}
	}
	
	function update()
	{
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('imagefile', 'Image File', 'trim|required|xss_clean');
		$this->form_validation->set_rules('imagedesc', 'Description', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('imagefile'),'msg1'=>form_error('imagedesc')));
		}
		else
		{
			$uomid=$this->input->post('uomid');
			$data = array('uom_name' => $this->input->post('uom'),'uom_desc' => $this->input->post('uomdesc'));

			//query the database
			$result = $this->admin->update_uom($uomid,$data);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
	
	function deleteimage()
	{
		$id=$this->input->post('id');
		//query the database
		$result = $this->assessment->delete_journal_data_entry_picture($id);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}

	function delete()
	{
		$uomid=$this->input->post('id');

	   //query the database
		$result = $this->admin->delete_uom($uomid);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));

	}
}
?>