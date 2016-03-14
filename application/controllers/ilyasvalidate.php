<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Ilyasvalidate extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url','general'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
	    $this->load->model('assessment','',TRUE);
	    $this->load->model('ilyasmodel','',TRUE);
        $this->load->model('reminder','',TRUE);
   	    $this->load->library('swiftmailer');
	}
	
	function index()
	{	
   		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];
			$roleid=$session_data['roleid'];
            /*for validator shouldn't be able to open non-progressive journal not assigned to them*/
            $user_id = $session_data['id'];
            $id=$this->input->get('jid');
            $validator = $this->ilyasmodel->get_validator_nonp($id);
            if(!empty($validator['validate_user_id']) && $validator['validate_user_id']==$user_id) {
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

            //function for updating user alert seen status. done by jane
            if($this->input->get('alert_id')!="") {
                $alert_id = $this->input->get('alert_id');
            }
            if($this->input->get('alert_user_id')!="") {
                $alert_user_id = $this->input->get('alert_user_id');
            }
            if(!empty($alert_id) && (!empty($alert_user_id))&& ($user_id==$alert_user_id)){
                $this->alertreminder->update_reminder_status($alert_id, $alert_user_id);
            }
            //end
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);
			
			$data['cpagename']='Ilyasvalidate';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(21);
			$data['message']=$message;
			
			$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detailnonp($id);
			$data['dataimages']=$this->assessment->show_journal_data_entry_picturenonp($id);
			$data['dataentryno']=$id;
			
			$data['details']=$this->assessment->show_journalnonp($id);
			
			if (sizeOf($data['details']) < 1) return;
			$data['details'] = $data['details'][0];
			$data['lookups'] = $this->ilyasmodel->get_lookup_data();
			$data['hot_config'] = $this->ilyasmodel->get_config($id, true);
			$data['hot_data'] = $this->ilyasmodel->get_data($id);
			$data['hot_lock'] = $this->ilyasmodel->get_validationlock($id);
			$data['data_date'] = $this->ilyasmodel->get_data_date($id);

                $this->load->view('header', $data1);
                $this->load->view('ilyas_validate', $data);
                $this->load->view('footer');
            } else{
                redirect('/journalvalidationnonp/index');
            }
		}
   		else
   		{
     		//If no session, redirect to login page
     		if ($this->input->get("jid") != "") redirect(make_login_redirect('index.php/'.uri_string()."?jid=".$this->input->get("jid")), 'refresh');
     		else redirect('/login', 'refresh');
   		}
	}
	
	function validate() {
		$status = 0;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('optradio', 'Please Accept or Reject', 'trim|required|xss_clean');
		if($this->input->post('optradio')=="Reject")
		{
			$this->form_validation->set_rules('reject_notes', 'Reject Notes', 'trim|xss_clean');
		}
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('optradio'),'msg1'=>form_error('comments')));
		}
		else
		{
			$session_data = $this->session->userdata('logged_in');
			$userid= $session_data['id'];
			///$dataattbcount=$this->input->post('dataattbcount');
			$validatorno=$this->input->post('validateid');
			$comments=$this->input->post("comments");
			$jid = $this->input->get('jid');
			if (!isset($jid)) { echo "0"; return; }
			
			
			$jdetails=$this->assessment->show_journalnonp($jid);
			if (sizeOf($jdetails) > 0) {
				/* The journal exists */
				//$q = $this->ilyasmodel->save_data($jid,json_decode($this->input->post("data")), $ispublish);
				//echo json_decode($q);
				//print_r(json_decode($this->input->get("data")));
			} else { echo "0"; return; }
			//print_r(json_decode($this->input->get("data")));
			
			/*$dataentryno=$this->input->post('dataentryid');
			for($i=1;$i<=$dataattbcount;$i++)
			{
				$dataattbid='dataattbid'.$i;
				$comment='comment'.$i;
				$this->assessment->update_journal_data_validate_detail($validatorno,$dataentryno,$this->input->post($dataattbid),$this->input->post($comment));
			}*/
			if($this->input->post('optradio')=="Approve")
			{
				//$this->ilyasmodel->set_validationlock($jid, 0);
				$status = $this->ilyasmodel->validate_approve($jid);
				if ($status) {
					 $emails = $this->ilyasmodel->get_emails($jid)[0];
					
					 $validator_id = $emails->validator_id;
					 $validator_name = $emails->validator_name;
					 $validator_email = $emails->validator_email;
					 $data_id = $emails->data_id;
					 $data_name = $emails->data_name;
					 $data_email = $emails->data_email;
					$journalname = $jdetails[0]->journal_name;

					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $data_id,'data_entry_no' => null,'alert_message' => $journalname.' Data Entry Accepted','alert_hide' => '0','email_send_option' => '1', 'nonp_journal_id' => $jid);
                    $this->assessment->add_user_alert($data);
					$this->assessment->update_alert_on_save_nonp($jid,$userid);

					//$emails = $this->ilyasmodel->get_emails($jid)[0];
					//var_dump($emails);
					//Temporary disabled.
					//$this->swiftmailer->data_entry_accepted_nonprogressive($data_email, $data_name, $journalname);
					
					/*
					$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($data_email);
				    $message="Dear ".$data_name.", <br>".$journalname." data entry Accpeted by ".$validator_name.".";
					$this->email->subject($journalname.' data entry completed');
					$this->email->message($message);
					$this->email->send();*/
					
				}
				/*$this->assessment->update_validate_accept($validatorno,$dataentryno);

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
				}*/

			}
			else if($this->input->post('optradio')=="Reject")
			{
				//var_dump($this->input->post('comments'));
				$status = $this->ilyasmodel->validate_reject_row($jid,$comments); // Change this to validate_reject if you want cell level comments
				if ($status) {
					$emails = $this->ilyasmodel->get_emails($jid)[0];

					$validator_id = $emails->validator_id;
					$validator_name = $emails->validator_name;
					$validator_email = $emails->validator_email;
					$data_id = $emails->data_id;
					$data_name = $emails->data_name;
					$data_email = $emails->data_email;
					$journalname = $jdetails[0]->journal_name;

					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $data_id,'data_entry_no' => null,'alert_message' => $journalname.' Data Entry Rejected','alert_hide' => '0','email_send_option' => '1', 'nonp_journal_id' => $jid);
					$this->assessment->add_user_alert($data);
					$this->assessment->update_alert_on_save_nonp($jid,$userid);

					$this->load->model('mailermodel');
					$this->mailermodel->insert_queue_rejected_nonprogressive($data_id, $jid);


					//$this->swiftmailer->data_entry_rejected_nonprogressive($data_email, $data_name, $journalname, $jid);
					/*
					$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($data_email);
					$message="Dear ".$data_name.", <br>".$journalname." data entry rejected by ".$validator_name.".";
					$this->email->subject($journalname.' data entry rejected');
					$this->email->message($message);
					$this->email->send();*/
				}
				/*$this->assessment->update_validate_reject($validatorno,$dataentryno,$this->input->post('comment'),$userid);

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
				$this->assessment->add_user_alert($data);*/

			}
			else if($this->input->post('optradio')=="Close")
			{
				/*$this->assessment->update_validate_close($validatorno,$dataentryno);*/
			}

            /*call reminder update function if reminder frequency not none*/
            $reminder_frequency = $jdetails[0]->reminder_frequency;
            if(!empty($reminder_frequency)) {
            $this->update();
            }

			$sess_array = array('message' => "Journal Validation Updated Successfully", 'type' => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>$status, 'msg' => 'Success'));
		}
	}
	
	/*function get_config($jid) {
		//header('Content-Type: application/json');
		//print_r($this->ilyasmodel->get_config(19));
		$result = [];
		$q = $this->ilyasmodel->get_config($jid);
		foreach ($q as $i):
			$i = (array) $i;
			array_push($result, [
				'header' => $i['col_header'],
				'width' => $i['col_width'],
				'type' => $i['type'],
				'uom' => $i['uom_id']]);
		endforeach;
		return $result;
	}
	
	function get_data() {
	header('Content-Type: application/json');
		$id=$this->input->get('jid');
		return $this->ilyasmodel->get_data($id);
	}
	

	function hidealert()
	{
		$id=$this->input->post('id');
		$this->alertreminder->hide_alert($id);
	}

	function hidereminder()
	{
		$id=$this->input->post('id');
		$this->alertreminder->hide_reminder($id);
	}*/

    /*function to update reminders*/
    function update(){
        $this->reminder->update_reminder();
    }
}
?>