<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Journaldataentryaddnonp extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('assessment','',TRUE);
   	   $this->load->model('securitys','',TRUE);
   	   $this->load->library('email');
	   $this->load->helper(array('form','url'));
	   $this->load->model('alertreminder','',TRUE);
	}

	function index($offset=0)
	{
		// Load Pagination
		$this->load->library('pagination');

		if($this->session->userdata('logged_in'))
		{
			$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];

			$roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"3");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');
				
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
			
			$id=$this->input->get('jid');

			//Load all record data
			$data['cpagename']='journaldataentryaddnonp';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(21);
			$data['message']=$message;
			$data['details']=$this->assessment->show_journal_data_entrynonp($id);
			$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detailnonp($id);
			$data['dataimages']=$this->assessment->show_journal_data_entry_picturenonp($id);
			$data['dataentryno']=$id;
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
			$this->load->view('assess_journalentryaddnonp', $data);
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
		
		$id=$this->input->post('dataentryno1');
		$session_data = $this->session->userdata('logged_in');
		$userid= $session_data['id'];
		
		if (!is_dir('journalimagenonp')) {
            mkdir('./journalimagenonp', 0777, true);
        }
        if (!is_dir('journalimagenonp/'.$id)) {
            mkdir('./journalimagenonp/'.$id, 0777, true);
        }
        if (!is_dir('journalimagenonp/'.$id.'/'.$userid)) {
            mkdir('./journalimagenonp/'.$id.'/'.$userid, 0777, true);
        }
		//Configure
		//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
		$config['upload_path'] = 'journalimagenonp/'.$id.'/'.$userid.'/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']=date('dmYHis');	
		//load the upload library
		$this->load->library('upload', $config);
    
	    $this->upload->initialize($config);
	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('imagedesc', 'Defintion', 'trim|required|xss_clean|max_length[200]');

		if($this->form_validation->run() == FALSE)
		{
			//echo json_encode(array('st'=>0, 'msg' => '','msg1'=>form_error('imagedesc')));
			$sess_array = array('message' => "Picture not uploaded. ".form_error('imagedesc'));
			$this->session->set_userdata('message', $sess_array);
			redirect('/journaldataentryaddnonp?jid='.$id,'refresh');
		}
		else
		{
			//if not successful, set the error message
			if (!$this->upload->do_upload('imagefile')) 
			{
				//echo json_encode(array('st'=>0, 'msg' => $this->upload->display_errors(),'msg1'=>form_error('imagedesc')));
				$sess_array = array('message' => "Picture not uploaded. ".$this->upload->display_errors());
				$this->session->set_userdata('message', $sess_array);
				redirect('/journaldataentryaddnonp?jid='.$id,'refresh');
			} 
			else 
			{ 
				$filedetails=$this->upload->data();
				$data = array('data_entry_no' => $id,'pict_file_name' => $filedetails['file_name'],'pict_file_path' => '/journalimagenonp/'.$id.'/'.$userid.'/','pict_definition' => $this->input->post('imagedesc'),'pict_user_id' => $userid,'data_source' => '1');
				$this->assessment->add_journal_data_entry_picturenonp($data);
				$this->assessment->add_seq_journal_data_entry_picturenonp($id);
				/*$result=$this->assessment->show_journal_data_entry_picture($id);
				$value='';
				foreach($result as $row)
				{
					$value .=$row->pict_seq_no.','.$row->pict_file_path.','.$row->pict_file_name.','.$row->pict_definition.','.$row->data_entry_no.',777,';
				}
				echo json_encode(array('st'=>1, 'msg' => 'Success','imgval'=>$value));*/
				$sess_array = array('message' => "Picture Attached to the Journal");
				$this->session->set_userdata('message', $sess_array);
				redirect('/journaldataentryaddnonp?jid='.$id,'refresh');
			}
		}
	}
	
	function deleteimage()
	{
		$id=$this->input->post('id');
		$dataid=$this->input->post('dataid');
		//query the database
		$result = $this->assessment->delete_journal_data_entry_picturenonp($id);
		$this->assessment->add_seq_journal_data_entry_picturenonp($dataid);
		
		$result=$this->assessment->show_journal_data_entry_picturenonp($dataid);
		$value='';
		foreach($result as $row)
		{
			$value .=$row->pict_seq_no.','.$row->pict_file_path.','.$row->pict_file_name.','.$row->pict_definition.','.$row->data_entry_pict_no.','.$row->data_entry_no.',777,';
		}
		echo json_encode(array('st'=>1, 'msg' => 'Success','imgval'=>$value));
	}
	
	function add()
	{
		$this->load->library('form_validation');
		
		$dataattbcount=$this->input->post('dataattbcount');
		for($i=1;$i<=$dataattbcount;$i++)
		{			
			$dataattb='dataattb'.$i;
			$datalabel='datalabel'.$i;

			$this->form_validation->set_rules($dataattb, $this->input->post($datalabel), 'trim|required|xss_clean|max_length[1000]');
			
		}
		if($this->form_validation->run() == FALSE)
		{
			$dataattbcount=$this->input->post('dataattbcount');
			for($i=1;$i<=$dataattbcount;$i++)
			{
				$dataattb='dataattb'.$i;
				$error=form_error($dataattb);
				if($error!='')
					break;
			}
			echo json_encode(array('st'=>0, 'msg' => $error));
		}
		else
		{	
			$dataid=$this->input->post('dataentryno');
			$session_data = $this->session->userdata('logged_in');
			$userid= $session_data['id'];	
			$dataattbcount=$this->input->post('dataattbcount');
			for($i=1;$i<=$dataattbcount;$i++)
			{
				$dataattb='dataattb'.$i;
				$dataattbid='dataattbid'.$i;
				$this->assessment->update_journal_data_entry_detailnonp($dataid,$this->input->post($dataattbid),$this->input->post($dataattb),$userid);
			}			

			$sess_array = array('message' => "Project Journal Data Entry Updated Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
	
}
?>