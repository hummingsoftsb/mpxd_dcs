<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Api extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
		$this->load->model('assessment','',TRUE);
		$this->load->model('ilyasmodel','',TRUE);
	}
	
	function index()
	{	
   		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];

		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}
	}
	
	function getalert() {
		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			$data['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			/*$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);*/
            $data1['alertcount']=count($data1['alerts']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			
			header("Content-type: application/json");
			echo json_encode($data1);

		} else {
		}
	}
	
	function getlatestnotification(){
		if($this->session->userdata('logged_in'))
   		{
     		$session_data = $this->session->userdata('logged_in');
			// $data_username = $session_data['username'];
			$alerts = $this->alertreminder->show_alert($session_data['id']);
			/*$alert_count = $this->alertreminder->count_alert($session_data['id']);*/
            $alert_count=count($alerts);
			// $data1_alertcount = $this->alertreminder->count_alert($session_data['id']);
			// $data1_reminders = $this->alertreminder->show_reminder($session_data['id']);
			// $data1_remindercount = $this->alertreminder->count_reminder($session_data['id']);
			// echo json_encode($alerts); die();
			$output = array();
			foreach ($alerts as $k => $alert){
				$href = "";
                if(($alert->alert_seen_status == 0) && ($alert->alert_user_id == $session_data['id'])){
                    $class = 'not_seen';
                } else {
                    $class = 'seen';
                }
				if(is_null($alert->data_entry_no) && substr($alert->alert_message,-9,9) === 'Published' ){ //non-progressive journal
					$href = base_url()."/index/ilyasvalidate?jid=".$alert->nonp_journal_id."&alert_id=".$alert->alert_no."&alert_user_id=".$alert->alert_user_id;
				}
				else if(is_null($alert->data_entry_no) && substr($alert->alert_message,-8,8) === 'Rejected'){ //non-progressive journal
					$href = base_url()."index.php/ilyas?jid=".$alert->nonp_journal_id."&alert_id=".$alert->alert_no."&alert_user_id=".$alert->alert_user_id;;
				}
				else if(!is_null($alert->data_entry_no) && substr($alert->alert_message,-9,9) === 'Published'){ //progressive journal
					$href = base_url()."journalvalidationview?id=".$alert->data_validate_no."&alert_id=".$alert->alert_no."&alert_user_id=".$alert->alert_user_id;;
				}
				else if(!is_null($alert->data_entry_no) && substr($alert->alert_message,-8,8) === 'Rejected' ){ //progressive journal
					$href = base_url()."journaldataentryadd?jid=".$alert->data_entry_no."&alert_id=".$alert->alert_no."&alert_user_id=".$alert->alert_user_id;;
				}
                /* Usage : Redirection to journal info page for modified journal
                Author : Sebin */
                else if(!is_null($alert->data_entry_no) && substr($alert->alert_message,-12,12) === 'Modification' ){ //progressive journal
                    $href = base_url()."journaldataentryadd?jid=".$alert->data_entry_no."&alert_id=".$alert->alert_no."&alert_user_id=".$alert->alert_user_id;;
                }
				if(substr($alert->alert_message,-8,8) === 'Accepted') :
                    //$alert_d0 ="<input type='checkbox' name='chk_chk' id='chk_chk[]'  value='$alert->alert_no' onclick='test()'>" ;
                    $alert_d0 ="<input type='checkbox' class='cboxes $class' name='chk_chk' id='chk_chk[]' checked='checked' value='$alert->alert_no'>" ;
					$alert_d1 = $alert->journal_name." ".$alert->alert_message;
					$alert_d2 = date("d-M-y", strtotime($alert->alert_date));
					$alert_d3 = $alert->frequency_period != "" ? $alert->frequency_period : '-' ;
					$alert_d4 = "<a href='$href' data-toggle='modal' class='alerthide' data-id='$alert->alert_no'><span title='Delete' class='glyphicon glyphicon-trash'></span></a>";
					$href = $alert_d1;
					// echo json_encode($data);
                /* Usage : Alerting Validator with Delete option ::: Alerting Data Entry and Admin with Edit option
                   Author : Sebin */
/*                elseif(substr($alert->alert_message,-12,12) === 'Modification') :
                    $sec_role=$this->alertreminder->fn_chk_user_role($session_data['id']);
                    if($sec_role[0]->sec_role_id == 2) :
                        $alert_d0 = "<input type='checkbox' class='cboxes $class' name='chk_chk' id='chk_chk[]' checked='checked' value='$alert->alert_no'>";
                        $alert_d1 = $alert->journal_name . " " . $alert->alert_message;
                        $alert_d2 = date("d-M-y", strtotime($alert->alert_date));
                        $alert_d3 = $alert->frequency_period != "" ? $alert->frequency_period : '-';
                        $alert_d4 = "<a href='$href' data-toggle='modal' class='alerthide' data-id='$alert->alert_no'><span title='Delete' class='glyphicon glyphicon-trash'></span></a>";
                        $href = $alert_d1;
                    else:
                        $alert_d0 ="<input type='checkbox' class='cboxes $class' id='dis_chk' name='dis_chk'  disabled='disabled'>" ;
                        $alert_d1 = $alert->journal_name." ".$alert->alert_message;
                        $alert_d2 = date("d-M-y", strtotime($alert->alert_date));
                        $alert_d3 = $alert->frequency_period != "" ? $alert->frequency_period : '-' ;
                        $alert_d4 = "<a href='$href'><span title='Update' class='glyphicon glyphicon-edit'></span></a>";
                        $href = "<a href='$href'>$alert_d1</a>";
                    endif;*/
				else:
                    $alert_d0 ="<input type='checkbox' class='cboxes $class' id='dis_chk' name='dis_chk'  disabled='disabled'>" ;
					$alert_d1 = $alert->journal_name." ".$alert->alert_message;
					$alert_d2 = date("d-M-y", strtotime($alert->alert_date));
					$alert_d3 = $alert->frequency_period != "" ? $alert->frequency_period : '-' ;
					$alert_d4 = "<a href='$href'><span title='Update' class='glyphicon glyphicon-edit'></span></a>";
					$href = "<a href='$href'>$alert_d1</a>";
					// echo json_encode($data);
				endif;
				$outputdata = new stdClass();
				$outputdata->test = 'ddd';
				$output['data'][] = array($alert_d0,$k+1,$href,$alert_d2,$alert_d3,$alert_d4);
				$output['count'] = $alert_count;
			}
			header("Content-type: application/json");
			echo json_encode($output);

		} else {
		}
		
		// $data = array('sda','dsadsa','ds');
		// echo json_encode($data);
	}
	function get_progressive_attributes() {
		$jid = $_GET['jid'];
		$r = $this->assessment->get_progressive_attributes($jid);
		$result = array();
		foreach ($r as $k => $v):
			array_push($result, $v->data_attb_label);
		endforeach;
		echo json_encode($result);	
	}
	
	function get_nonp_columns() {
		$jid = $_GET['jid'];
		$r = $this->ilyasmodel->get_config_for_journal($jid);
		//var_dump($r);
		$result = array();
		foreach ($r as $k=>$v):
			array_push($result, $v);
		endforeach;
		
		echo json_encode($result);
	}
	
	function get_nonp_column_value() {
		$jid = $_GET['jid'];
		$config_no = $_GET['config_no'];
		$r = $this->ilyasmodel->get_column_values_for_journal($jid, $config_no);
		//var_dump($r);
		$result = array();
		foreach ($r as $k=>$v):
			array_push($result, $v->value);
		endforeach;
		
		echo json_encode($result);
	}
	
}
?>