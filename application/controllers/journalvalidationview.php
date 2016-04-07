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
   	   $this->load->library(array('email','swiftmailer','parseplugin'));
	   $this->load->helper(array('form','url','general'));
	   $this->load->model('alertreminder','',TRUE);
       $this->load->model('reminder','',TRUE);
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
				$type=$messagehrecord['type'];
				$this->session->unset_userdata('message');
			}
			else
			{
				$message='';
				$type = '';
			}

			$id=$this->input->get('id');
            $user_id = $session_data['id'];
            //function for updating user alert seen status
            if($this->input->get('alert_id')!="") {
                $alert_id = $this->input->get('alert_id');
            }
            if($this->input->get('alert_user_id')!="") {
                $alert_user_id = $this->input->get('alert_user_id');
            }
            if(!empty($alert_id) && (!empty($alert_user_id)) && ($user_id==$alert_user_id)){
                $this->alertreminder->update_reminder_status($alert_id, $alert_user_id);
            }
            //end

			//Load all record data
			$data['cpagename']='journalvalidationview';
			$data['labels']=$this->securitys->get_label(4);
			$data['labelgroup']=$this->securitys->get_label_group(4);
			$data['labelobject']=$this->securitys->get_label_object(4);
			$data['message']=$message;
			$data['details']=$this->assessment->show_validation_journal_data_entry($id);
			
			$validate_status = $this->assessment->show_validation_status($id);
			if (isset($validate_status[0]) && !($validate_status[0]->validate_status == "1")) redirect('/journalvalidation','refresh');
			$data['validators']=$this->assessment->show_validation_journal_validator($id);
			$data['validatorcount']=$this->assessment->total_validation_journal_validator($id);
			$data['dataentryattbs']=$this->assessment->show_validation_journal_data_entry_detail($id);
			$data['dataimages']=$this->assessment->show_validation_journal_data_entry_picture($id);
			$data['rejectnotes']=$this->assessment->show_validation_journal_data_entry_picture_rejectnotes($id);
			$data['reject_note']=$this->assessment->show_journal_validator_reject_note($id);
			$data['approve_stop_status']=$this->assessment->approve_stop_status_check($id);
			$data['validatorid']=$id;
			
			$data['message_type']=$type;
			
			
			//$this->assessment->update_journal_date_status($id);
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

			$this->load->view('header', $data1);
			$this->load->view('assess_journalvalidationview', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			
			if ($this->input->get("id") != "") redirect(make_login_redirect(uri_string()."?id=".$this->input->get("id")), 'refresh');
			else redirect("login", "refresh");
		}
	}
	function add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('optradio', 'Please Accept or Reject', 'trim|required|xss_clean');
		if($this->input->post('optradio')=="Reject")
		{
			//$this->form_validation->set_rules('comment', 'Reject Notes', 'trim|required|xss_clean');
			$this->form_validation->set_rules('comment', 'Reject Notes', 'trim|xss_clean');
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
//                Added by Agaile, While approve the validator comments where not saved
                //AGAILE :START
                $pict_comment = array();
                foreach($this->input->post() as $k => $post){
                    if(substr($k,0,12) === 'pict-comment'){
                        $pic_id = substr($k,12);
                        $pict_comment[$pic_id]['comment'] = $this->input->post('pict-comment'.$pic_id);
                        $pict_comment[$pic_id]['user'] = $session_data['name'].': ';
                    }
                }

                    $this->assessment->update_validate_accept_picture($pict_comment);
                // AGAILE :END
				$this->assessment->update_validate_accept($validatorno,$dataentryno);

				if($this->assessment->update_validate_accept_email_check($dataentryno)==0)
				{ // What is this for? We could remove this
					//Email for Validator
					$emailres=$this->assessment->publish_journal_data_entry_email($dataentryno,$userid);
					foreach($emailres as $rows):
						$journalname=$rows->journalname;
						$dataentryname=$rows->dataentryname;
						$validatorname=$rows->validatorname;
						$validatoremail=$rows->validatoremail;
						$validatorid=$rows->validatorid;
						$validateno=$rows->data_validate_no;
					endforeach;
					/*$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($validatoremail);
				    $message="Dear ".$validatorname.", <br>".$journalname." data entry published by ".$dataentryname.".Now the journal is ready for validation";
					$this->email->subject($journalname.' data entry completed');
					$this->email->message($message);
					$this->email->send();*/

                    //Modified by Jane. This is for adding alert to next level validator
					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $validatorid,'data_entry_no' => $dataentryno,'alert_message' => 'Data Entry Published','alert_hide' => '0','email_send_option' => '1');
					$this->assessment->add_user_alert($data);
                    $this->assessment->update_alert_on_save($dataentryno,$userid);
					
					$nextvalidator = $this->assessment->next_validator($validateno);
					if($nextvalidator){ //If there is a next level validator, send email notification.
						$this->load->model('mailermodel');
						$this->mailermodel->insert_queue_published($nextvalidator->validate_user_id, 'progressive', $dataentryno);
					}
					
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
					/*
					$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($dataentryemail);
				    $message="Dear ".$dataentryname.", <br>".$journalname." data entry Accpeted by ".$validatorname.".";
					$this->email->subject($journalname.' data entry completed');
					$this->email->message($message);
					$this->email->send();*/
					//disable sending email for accepted journal for now until further notice
					//$this->swiftmailer->data_entry_accepted_progressive($dataentryemail, $dataentryname, $journalname);
					$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $dataentryid,'data_entry_no' => $dataentryno,'alert_message' => 'Date Entry Accepted','alert_hide' => '0','email_send_option' => '1');
					$this->assessment->add_user_alert($data);
					$this->assessment->update_alert_on_save($dataentryno,$userid);
					$this->parseplugin->sendMessageByUserId($userid,'Please sync your mpxd app.', array("sync" => true, "silentforeground" => true));

				}

			}
			else if($this->input->post('optradio')=="Reject")
			{
				$pict_comment = array();
				foreach($this->input->post() as $k => $post){
					if(substr($k,0,12) === 'pict-comment'){
						$pic_id = substr($k,12);
						$pict_comment[$pic_id]['comment'] = $this->input->post('pict-comment'.$pic_id);
						$pict_comment[$pic_id]['user'] = $session_data['name'].': ';
					}
				}
                    $this->assessment->update_validate_reject_picture($pict_comment);
				
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
				endforeach;/*
				$this->email->from('test@hummingsoft.com.my', 'MPXD');
				$this->email->to($dataentryemail);
			    $message="Dear ".$dataentryname.", <br>".$journalname." data entry rejected by ".$validatorname.".";
				$this->email->subject($journalname.' data entry rejected');
				$this->email->message($message);
				$this->email->send();*/
				
				$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $dataentryid,'data_entry_no' => $dataentryno,'alert_message' => 'Data Entry Rejected','alert_hide' => '0','email_send_option' => '1');
				$this->assessment->add_user_alert($data);
				$this->assessment->update_alert_on_save($dataentryno,$userid);
				
				
				$this->load->model('mailermodel');
				$this->mailermodel->insert_queue_rejected_progressive($dataentryid, $dataentryno);
				
				//$this->swiftmailer->data_entry_rejected_progressive($dataentryemail, $dataentryname, $journalname, $dataentryno);
				$this->parseplugin->sendMessageByUserId($userid,'Please sync your mpxd app', array("sync" => true, "silentforeground" => true));

			}
			else if($this->input->post('optradio')=="Close")
			{
				$this->assessment->update_validate_close($validatorno,$dataentryno);
			}

            /*call reminder update function*/
            $this->update();
            /*$reminders_controller = new Reminders();
            $reminders_controller->update();*/

			$sess_array = array('message' => "Journal Validation Updated Successfully","type" => 1); //1 success , 0 error
			// $sess_array = array('message' => "Journal Validation Updated Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
	
	function deleteimage()
	{
		$id=$this->input->post('id');
		$dataid=$this->input->post('dataid');
		//query the database
		$result = $this->assessment->delete_journal_data_entry_picture($id);
		$this->assessment->add_seq_journal_data_entry_picture($dataid);

		$result=$this->assessment->show_journal_data_entry_picture($dataid);
		$value='';
		foreach($result as $row)
		{
			$value .=$row->pict_seq_no.','.$row->pict_file_path.','.$row->pict_file_name.','.$row->pict_definition.','.$row->data_entry_pict_no.','.$row->data_entry_no.',777,';
		}
		echo json_encode(array('st'=>1, 'msg' => 'Success','imgval'=>$value));
	}
	
	function addimage()
	{
		//load the helper
		$this->load->helper('form');

		$id=$this->input->post('dataentryno1');
		$validateid=$this->input->post('validatorid');
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
		$this->form_validation->set_rules('imagedesc', 'Image Description', 'trim|required|xss_clean|max_length[500]');

		if($this->form_validation->run() == FALSE)
		{
			//echo json_encode(array('st'=>0, 'msg' => '','msg1'=>form_error('imagedesc')));
			$sess_array = array('message' => "Upload failed.".form_error('imagedesc'),"type" => 0);
			$this->session->set_userdata('message', $sess_array);
			redirect('/journalvalidationview?id='.$validateid,'refresh');
		}
		else
		{
			//if not successful, set the error message
			if (!$this->upload->do_upload('imagefile'))
			{
				//echo json_encode(array('st'=>0, 'msg' => $this->upload->display_errors(),'msg1'=>form_error('imagedesc')));
				$sess_array = array('message' => "Upload failed. ".$this->upload->display_errors(),"type" => 0);
				$this->session->set_userdata('message', $sess_array);
				redirect('/journalvalidationview?id='.$validateid,'refresh');
			}
			else
			{
				$filedetails=$this->upload->data();
				
				//resize the image
				$this->load->library("imageresize",array($config['upload_path'].$filedetails['file_name']));
				$this->imageresize->crop(800, 600);
				$this->imageresize->save($config['upload_path'].$filedetails['file_name']);
				//
				
				$data = array('data_entry_no' => $id,'pict_file_name' => $filedetails['file_name'],'pict_file_path' => '/journalimage/'.$id.'/'.$userid.'/','pict_definition' => $this->input->post('imagedesc'),'pict_user_id' => $userid,'data_source' => '1');
				$this->assessment->add_journal_data_entry_picture($data);
				$this->assessment->add_seq_journal_data_entry_picture($id);
				/*$result=$this->assessment->show_journal_data_entry_picture($id);
				$value='';
				foreach($result as $row)
				{
					$value .=$row->pict_seq_no.','.$row->pict_file_path.','.$row->pict_file_name.','.$row->pict_definition.','.$row->data_entry_no.',777,';
				}
				echo json_encode(array('st'=>1, 'msg' => 'Success','imgval'=>$value));*/
				$sess_array = array('message' => "Picture Attached to the Journal","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				redirect('/journalvalidationview?id='.$validateid,'refresh');
			}
		}
	}
	
	function updateimgsequence(){
		$seqs = $this->input->post('seqs');
		$seqs = rtrim($seqs,',');
		$eachseq = explode(',',$seqs);
		foreach($eachseq as $s){
			$t = explode(':',$s);
			$this->assessment->update_seq_journal_data_entry_picture($t[0],$t[1]);
		}
	}

    /*function to remove uploaded image from validation view. added by jane*/
    function removeimage()
    {
        header('Access-Control-Allow-Origin: *');
        $dataid = $this->input->post('data_entry_no');
        $pict_file_name = $this->input->post('pict_file_name');
        //query the database
        $result = $this->assessment->remove_journal_data_entry_picture_validator($dataid, $pict_file_name);
        echo json_encode(array('st' => 1, 'msg' => 'Success'));
    }

    /*function to update reminders*/
    function update(){
        $this->reminder->update_reminder();
    }
}
?>