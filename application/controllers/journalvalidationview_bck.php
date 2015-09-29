<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Journalvalidationview extends CI_Controller
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"4");
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
			
			$id=$this->input->get('id');

			//Load all record data
			$data['cpagename']='journalvalidationview';
			$data['message']=$message;
			$data['details']=$this->assessment->show_validation_journal_data_entry($id);
			$data['validators']=$this->assessment->show_validation_journal_validator($id);
			$data['validatorcount']=$this->assessment->total_validation_journal_validator($id);
			$data['dataentryattbs']=$this->assessment->show_validation_journal_data_entry_detail($id);
			$data['dataimages']=$this->assessment->show_validation_journal_data_entry_picture($id);
			$data['validatorid']=$id;
			$this->assessment->update_journal_date_status($id);
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);

			$this->load->view('header', $data1);
			$this->load->view('assess_journalvalidationview', $data);
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
		$this->form_validation->set_rules('optradio', 'Please Accept or Reject', 'trim|required|xss_clean');
		if($this->input->post('optradio')=="Reject")
		{
			$this->form_validation->set_rules('comment', 'Reject Notes', 'trim|required|xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('optradio'),'msg1'=>form_error('comment')));
		}
		else
		{
			$session_data = $this->session->userdata('logged_in');
			$userid= $session_data['id'];	
			$dataattbcount=$this->input->post('dataattbcount');
			$validatorno=$this->input->post('validateid');
			$dataentryno=$this->input->post('dataentryid');
			for($i=1;$i<=$dataattbcount;$i++)
			{
				$dataattbid='dataattbid'.$i;
				$comment='comment'.$i;
				$this->assessment->update_journal_data_validate_detail($validatorno,$dataentryno,$this->input->post($dataattbid),$this->input->post($comment));
			}
			if($this->input->post('optradio')=="Approve")
			{
				$this->assessment->update_validate_accept($validatorno,$dataentryno);
				
				if($this->assessment->update_validate_accept_email_check($dataentryno)==0)
				{
					//Email for Validator
					$emailres=$this->assessment->publish_journal_data_entry_email($dataentryno,$userid);
					foreach($emailres as $rows):
						$journalname=$rows->journalname;
						$dataentryname=$rows->dataentryname;
						$validatorname=$rows->validatorname;
						$validatoremail=$rows->validatoremail;
						$validatorid=$rows->validatorid;
					endforeach;
					$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($validatoremail);
				    $message="Dear ".$validatorname.", <br>".$journalname." data entry published by ".$dataentryname.".Now the journal is ready for validation";
					$this->email->subject($journalname.' data entry completed');
					$this->email->message($message);
					$this->email->send();
					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $validatorid,'data_entry_no' => $dataentryno,'alert_message' => 'Data Entry Published','alert_hide' => '0','email_send_option' => '1');
					$this->assessment->add_user_alert($data);
				}
				else
				{
					//Email Data Entry
					$emailres=$this->assessment->update_validate_accept_email($dataentryno,$userid);
					foreach($emailres as $rows):
						$journalname=$rows->journalname;
						$dataentryname=$rows->dataentryname;
						$dataentryemail=$rows->dataentryemail;
						$dataentryid=$rows->dataentryid;
						$validatorname=$rows->validatorname;
					endforeach;
					$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($dataentryemail);
				    $message="Dear ".$dataentryname.", <br>".$journalname." data entry Accpeted by ".$validatorname.".";
					$this->email->subject($journalname.' data entry completed');
					$this->email->message($message);
					$this->email->send();
					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $dataentryid,'data_entry_no' => $dataentryno,'alert_message' => 'Date Entry Accepted','alert_hide' => '0','email_send_option' => '1');
					$this->assessment->add_user_alert($data);
				}
				
			}
			else if($this->input->post('optradio')=="Reject")
			{
				$this->assessment->update_validate_reject($validatorno,$dataentryno,$this->input->post('comment'),$userid);
				
				$this->assessment->update_validate_reject_varient($dataentryno);
				
				//Email
				$emailres=$this->assessment->update_validate_reject_email($dataentryno,$userid);
				foreach($emailres as $rows):
					$journalname=$rows->journalname;
					$dataentryname=$rows->dataentryname;
					$dataentryemail=$rows->dataentryemail;
					$dataentryid=$rows->dataentryid;
					$validatorname=$rows->validatorname;
				endforeach;
				$this->email->from('test@hummingsoft.com.my', 'MPXD');
				$this->email->to($dataentryemail);
			    $message="Dear ".$dataentryname.", <br>".$journalname." data entry rejected by ".$validatorname.".";
				$this->email->subject($journalname.' data entry rejected');
				$this->email->message($message);
				$this->email->send();
				$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $dataentryid,'data_entry_no' => $dataentryno,'alert_message' => 'Data Entry Rejected','alert_hide' => '0','email_send_option' => '1');
				$this->assessment->add_user_alert($data);
				
			}
			$sess_array = array('message' => "Journal Validation Updated Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
}
?>