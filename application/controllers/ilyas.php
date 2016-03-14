<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Ilyas extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
        $this->load->helper(array('url', 'general'));
        $this->load->model('alertreminder', '', TRUE);
        $this->load->model('securitys', '', TRUE);
        $this->load->model('assessment', '', TRUE);
        $this->load->model('ilyasmodel', '', TRUE);
        $this->load->model('reminder', '', TRUE);
   	   //$this->load->library('swiftmailer');
	}
	
	function index()
	{	
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
			//access checking
			if($viewperm==0)
				redirect('/home','refresh');
			if(!$this->assessment->check_access_nonp($session_data['id'],$this->input->get('jid')) && $session_data['roleid'] != 1){
				//die("No access");
				redirect('/journaldataentry','refresh');
			}
			//end access checking
				
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
            $user_id = $session_data['id'];
            //function for updating user alert seen status
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
			
			$data['cpagename']='ilyas';
			$data['labels']=$this->securitys->get_label(21);
			$data['labelgroup']=$this->securitys->get_label_group(21);
			$data['labelobject']=$this->securitys->get_label_object(21);
			$data['message']=$message;
			
			//$data['dataentryattbs']=$this->assessment->show_journal_data_entry_detailnonp($id);
			//$data['dataimages']=$this->assessment->show_journal_data_entry_picturenonp($id);
			$data['dataentryno']=$id;
			
			$data['details']=$this->assessment->show_journalnonp($id);
			$data['validator']=$this->assessment->show_journalnp_validator($id);
			//var_dump($id);
			//var_dump($data['validator']);
			if (sizeOf($data['details']) < 1) return;
			$data['details'] = $data['details'][0];
			$data['lookups'] = $this->ilyasmodel->get_lookup_data();
			//var_dump($data);
			/*
			$save = $this->input->post('data');
			if ($save) {
				var_dump($save);
			}
			*/
			
			$data['hot_config'] = $this->ilyasmodel->get_config($id);
			$data['hot_data'] = $this->ilyasmodel->get_data($id);
			$data['hot_lock'] = $this->ilyasmodel->get_validationlock($id);
			$data['hot_read_only_rows'] = $this->ilyasmodel->get_read_only_rows($id);
			$data['hot_comments'] = [];//$this->ilyasmodel->get_validation_comment($id);
			$data['new_comments'] = $this->ilyasmodel->get_validation_comment_row($id);
			$data['data_date'] = $this->ilyasmodel->get_data_date($id);
			
			$this->load->view('header', $data1);
			$this->load->view('ilyas', $data);
			$this->load->view('footer');
		}
   		else
   		{
     		//If no session, redirect to login page
			if ($this->input->get("jid") != "") redirect(make_login_redirect('index.php/'.uri_string()."?jid=".$this->input->get("jid")), 'refresh');
     		else redirect('/login', 'refresh');
   		}
	}
	
	
	function save_data() {
	// Should create audit log by saving in data into audit table, and changing all lookups to its respective values.
		if($this->session->userdata('logged_in'))
   		{
			$session_data = $this->session->userdata('logged_in');
			header('Content-Type: application/json');
			$id = $this->input->get('jid');
			$publish = $this->input->get('publish');
			$ispublish = (isset($publish) && ($publish == "true"));
			if ($id) {
				$jdetails=$this->assessment->show_journalnonp($id);
				if (sizeOf($jdetails) > 0) {
					/* The journal exists */
					$data_date = $this->input->post("data_date");
					
					$q = $this->ilyasmodel->save_data($id,json_decode($this->input->post("data")), $session_data['id'], $data_date, $ispublish);
					if ($q && ($ispublish)) { 
						 $emails = $this->ilyasmodel->get_emails($id)[0];
						
						$validator_id = $emails->validator_id;
						//var_dump($validator_id);
						$validator_name = $emails->validator_name;
						$validator_email = $emails->validator_email;
						$data_id = $emails->data_id;
						$data_name = $emails->data_name;
						$data_email = $emails->data_email;
						$journalname = $jdetails[0]->journal_name;
						
						$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $validator_id,'data_entry_no' => null,'alert_message' => $journalname.' Data Entry Published','alert_hide' => '0','email_send_option' => '1', 'nonp_journal_id' => $id);
						$this->assessment->add_user_alert($data);
						$this->assessment->update_alert_on_save_nonp($id,$session_data['id']);
						/*
						$this->email->from('test@hummingsoft.com.my', 'MPXD');
						$this->email->to($validator_email);
						$message="Dear ".$validator_name.", <br>".$journalname." data entry published by ".$data_name.". Now the journal is ready for validation";
						$header( "Location: http://www.google.com" );
						$this->email->subject($journalname.' data entry completed');*/
						
						$this->load->model('mailermodel');
						$this->mailermodel->insert_queue_published($validator_id, 'nonprogressive', $id);
						
						//$this->swiftmailer->data_entry_published_nonprogressive($validator_email, $validator_name, $data_name, $journalname, $id);
						//$actual_link = "http://192.168.1.52/index.php/ilyasvalidate?jid=187";
						
						//$this->email->message($message);
						//$this->email->send();

						/*call reminder update function if reminder frequency not none*/
                        $reminder_frequency = $jdetails[0]->reminder_frequency;
                        if(!empty($reminder_frequency)) {
                            $this->update();
                            /*$reminders_controller = new Reminders();
                            $reminders_controller->update();*/
                        }

						$sess_array = array('message' => "Journal Published Successfully","type" => 1);
						$this->session->set_userdata('message', $sess_array);


					}
					echo json_decode($q);
				}
			}
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
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