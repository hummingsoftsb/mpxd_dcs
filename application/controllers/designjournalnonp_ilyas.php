<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Designjournalnonp_ilyas extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('design','',TRUE);
		$this->load->model('securitys','',TRUE);
		$this->load->model('admin','',TRUE);
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('ilyasmodel','',TRUE);
        $this->load->model('reminder','',TRUE);
        $this->load->library('swiftmailer');
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
			$userid=$session_data['id'];
			$roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"20");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="designjournalnonp_ilyas" && $_SERVER['QUERY_STRING']=="")
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
			
			
			
			//var_dump();

			// Config setup for Pagination
			$config['base_url'] = base_url().'index.php/designjournalnonp_ilyas/index';
			
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
			$config['total_rows'] = sizeOf($this->ilyasmodel->get_journals_nonp_all($search,0,null,$userid,true));
			// Initialize
			$this->pagination->initialize($config);
			
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


			//Load all record data
			$data['records'] = $this->ilyasmodel->get_journals_nonp_all($search,$offset,$config['per_page'],$userid,$roleid,true);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='designjournalnonp_ilyas';
			$data['labels']=$this->securitys->get_label(20);
			$data['labelgroup']=$this->securitys->get_label_group(20);
			$data['labelobject']=$this->securitys->get_label_object(20);
			$data['projects']=$this->design->show_projtmps();
			$data['users']=$this->securitys->show_users();
			//$data['frequencys']=$this->design->show_frequency();
			//$data['dataattbs']=$this->admin->show_dataattsnonp();

			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
			
			$data['message_type']=$type;

			//Load Validator for each Journal

			//Load Validator for each Journal
			/*$data['dataattbvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_data_attbnonp($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->data_attb_id.",".$datavaluerow->display_seq_no.",777,";
	        	}
        		$data['dataattbvalue'][$record->journal_no]=$datavalue;
        	}*/
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
			$this->load->view('design_journalnonp_ilyas', $data);
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
		//var_dump($this->input->post());
		$label=$this->securitys->get_label_object_name(62);
		$label1=$this->securitys->get_label_object_name(63);
		$label2=$this->securitys->get_label_object_name(65);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname', $label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('reminder_frequency', 'Reminder frequency', 'trim|alpha|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname').form_error('journalname').form_error('user').form_error('reminder_frequency')));
		}
		else
		{
			$name=$this->input->post('journalname');
			
			$projectno=$this->input->post('projectname');
			
			if ($this->design->add_check_journalnonp($name,$projectno)==0)
			{
				$data = array('project_no' => $projectno,'journal_name' => $name,'user_id' => $this->input->post('user'), 'nonp_enabled' => '1', 'reminder_frequency' => $this->input->post('reminder_frequency'));
				
				
				//query the database
				$jid = $this->design->add_journalnonp($data,$projectno,$name);
				
				$validatordata=array('journal_no'=>$jid,'validate_user_id'=>$this->input->post('validateuser1'),'validate_level_no'=>'1');
				$this->design->add_journal_validatornonp($validatordata);

				
				$dataentryowner = $this->input->post('dataentryowner');
				$data_user_id = $this->input->post('dataentryuser1');
				$dataentrydata=array('journal_no'=>$jid,'data_user_id'=>$data_user_id,'default_owner_opt'=> ($dataentryowner == $data_user_id) ? "1":"0");
				
				$this->design->add_journal_data_entrynonp($dataentrydata);

                /*call reminder update function*/
                $reminder_frequency = $this->input->post('reminder_frequency');
                if($reminder_frequency!=""){
                $this->reminder_update();
                /*$reminders_controller = new Reminders();
                $reminders_controller->update();*/
                }

				$sess_array = array('message' => $this->securitys->get_label_object(20)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg'=>"Journal Name already exist"));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(62);
		$label1=$this->securitys->get_label_object_name(63);
		$label2=$this->securitys->get_label_object_name(65);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname', $label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('reminder_frequency', 'Reminder frequency', 'trim|alpha|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname').form_error('journalname').form_error('user').form_error('reminder_frequency')));
		}
		else
		{
			$name=$this->input->post('journalname');
			$jid = $this->input->post('journal_no');
			$projectno=$this->input->post('projectname');
			
			if(!$this->ilyasmodel->check_awaiting_approval($jid)){
				echo json_encode(array('st'=>0, 'msg'=>"Unable to change assignation. Journal is pending for approval"));
			}
			else if ($this->ilyasmodel->check_update_journal_nonp($jid)==1)
			{
				// Journal exists.
				$data = array('project_no' => $projectno,'journal_name' => $name,'user_id' => $this->input->post('user'), 'reminder_frequency' => $this->input->post('reminder_frequency'));

				//update journal
				//$jid = $this->design->add_journalnonp($data,$projectno,$name);
				$this->design->update_journalnonp($jid, $data);
				
				$validatordata = array('validate_user_id'=>$this->input->post('validateuser1'),'validate_level_no'=>'1');

                /*Validator user change, send email to the new user. Modified by jane.*/
                $previous_validator = $this->design->get_previous_nonp_journal_validator($jid);
                $current_validator = $this->input->post('validateuser1');
                if($previous_validator[0]->validate_user_id != $current_validator) {
                    $user = $this->ilyasmodel->get_user_email($current_validator);
                    $email = $user[0]->email_id;
                    $validator = $user[0]->user_full_name;
                    $this->swiftmailer->validation_assigned($email, $validator, $name, $jid);
                }
                /*End*/

				$this->ilyasmodel->update_journal_validator($jid, $validatordata);

				$dataentryowner = $this->input->post('dataentryowner');
				$data_user_id = $this->input->post('dataentryuser1');
				//$dataentrydata=array('data_user_id'=>$data_user_id,'default_owner_opt'=> ($dataentryowner == $data_user_id) ? "1":"0");
				$is_default_owner = (($dataentryowner == $data_user_id) ? "1":"0");
				
				// Data entry user change, email to the new user.
				if ($this->ilyasmodel->is_journal_user_change($jid,$data_user_id)) {
					/*$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $data_id,'data_entry_no' => null,'alert_message' => $journalname.' Data Entry Rejected','alert_hide' => '0','email_send_option' => '1', 'nonp_journal_id' => $jid);
					$this->assessment->add_user_alert($data);
					$this->assessment->update_alert_on_save_nonp($jid,$userid);*/
					$user = $this->ilyasmodel->get_user_email($data_user_id)[0];
					$email = $user->email_id;
					$dename = $user->user_full_name;
					$this->swiftmailer->data_entry_assigned($email, $dename, $name, $jid);
				}
				
				$this->ilyasmodel->update_journal_dataentry($jid, $data_user_id, $is_default_owner);
				
				$sess_array = array('message' => $this->securitys->get_label_object(20)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg'=>"Journal does not exist"));
			}
		}
	}

    function delete()
    {
        $id=$this->input->post('id');
        if($this->design->delete_check_journalnonp($id)==0)
        {
            //query the database
            $result = $this->design->delete_journalnonp($id);
            $sess_array = array('message' => $this->securitys->get_label_object(20)." Deleted Successfully","type" => 1);
            $this->session->set_userdata('message', $sess_array);
            echo json_encode(array('st'=>1, 'msg' => 'Success'));
        }
        else
        {
            //$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(20).", Assigned to ".$this->securitys->get_label_object(21).$id,"type" => 0);
            $sess_array = array('message' => "Failed to delete, Journal is in progress.","type" => 0);
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

    /*function to update reminders*/
    function reminder_update(){
        $this->reminder->update_reminder();
    }
}
?>