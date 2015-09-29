<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Designjournalnonp extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('design','',TRUE);
		$this->load->model('securitys','',TRUE);
		$this->load->model('admin','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"20");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="designjournalnonp" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/designjournalnonp/index';
			$config['total_rows'] = $this->design->totaljournalnonp($search);
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
			$data['records'] = $this->design->show_journalnonp($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='designjournalnonp';
			$data['labels']=$this->securitys->get_label(20);
			$data['labelgroup']=$this->securitys->get_label_group(20);
			$data['labelobject']=$this->securitys->get_label_object(20);
			$data['projects']=$this->design->show_projtmps();
			$data['users']=$this->securitys->show_users();
			$data['frequencys']=$this->design->show_frequency();
			$data['dataattbs']=$this->admin->show_dataattsnonp();

			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;

			//Load Validator for each Journal
			$data['validatorvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_validatornonp($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->validate_user_id.",".$datavaluerow->validate_level_no.",777,";
	        	}
        		$data['validatorvalue'][$record->journal_no]=$datavalue;
        	}

			//Load Dataentry for each Journal
			$data['dataentryvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_data_usernonp($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->data_user_id.",".$datavaluerow->default_owner_opt.",777,";
	        	}
        		$data['dataentryvalue'][$record->journal_no]=$datavalue;
        	}

			//Load Validator for each Journal
			$data['dataattbvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_data_attbnonp($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->data_attb_id.",".$datavaluerow->display_seq_no.",777,";
	        	}
        		$data['dataattbvalue'][$record->journal_no]=$datavalue;
        	}
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
			$this->load->view('design_journalnonp', $data);
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
		$label=$this->securitys->get_label_object_name(62);
		$label1=$this->securitys->get_label_object_name(63);
		$label2=$this->securitys->get_label_object_name(65);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname', $label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('journalproperty', $label1, 'trim|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user', $label2, 'trim|required|xss_clean');
		//$this->form_validation->set_rules('frequency', 'Frequency', 'trim|required|xss_clean');
		//$this->form_validation->set_rules('startdate', 'Start Date', 'trim|required|xss_clean');

		$attbcount=$this->input->post('dataattbcount');
		$dataattberror='';
		$attbselect=0;
		$dataentryid=$this->input->post('dataentryid');
		$dataentryids=explode(',',$dataentryid);
		$dataerror='';
		$datamsg='Select atleat one data owner ';
		for($j=0;$j<count($dataentryids);$j++)
		{
			$dataentryowner=$this->input->post('dataentryowner');
			if($dataentryowner==$dataentryids[$j])
			{
				$dataerror="yes";
				$datamsg='';
			}
		}
		for($i=1;$i<=$attbcount;$i++)
		{
			$chk='dataattb'.$i;
			$order='order'.$i;
			if($this->input->post($chk)=="on")
			{
				if($this->input->post($order)=='' || !ctype_digit($this->input->post($order)))
				{
					$dataattberror="Enter Data Attribute Details or Invalid Input";
				}
				$attbselect=1;
			}
		}
		if($attbselect==0)
		{
			$dataattberror="Enter atleast one Data Attribute";
		}
		$enderror='';
		/*if($this->input->post('enddate')!='')
		{
			$projectend=date("Y-m-d", strtotime($this->design->show_proj_field('end_date',$this->input->post('projectname'))));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			if($projectend<$enddate)
			{
				$enderror='End date should be less than the Project End Date ('.$projectend.')';
			}
		}*/
		if($this->form_validation->run() == FALSE || $dataattberror!='' || $dataerror=='' || $enderror!='')
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname'),'msg1'=>form_error('journalname'),'msg2'=>form_error('user'),'msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>$dataattberror,'msg7'=>$datamsg,'msg8'=>'','msg9'=>form_error('journalproperty')));
		}
		else
		{
			$name=$this->input->post('journalname');
			$property=$this->input->post('journalproperty');
			$projectno=$this->input->post('projectname');
			$startdate=date("Y-m-d");
			/*$startdate=date("Y-m-d", strtotime($this->input->post('startdate')));
			if($this->input->post('enddate')!="")
			{
				$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			}
			else
			{
				$enddate=NULL;
			}*/
			if($this->design->add_check_journalnonp($name,$projectno)==0)
			{
				$data = array('project_no' => $projectno,'journal_name' => $name,'journal_property'=>$property,'user_id' => $this->input->post('user'),'start_date' => $startdate);

				//query the database
				$journalid = $this->design->add_journalnonp($data,$projectno,$name);

				//Validator
				$validatorid=$this->input->post('validatorid');
				$validatorids=explode(',',$validatorid);
				for($j=0;$j<count($validatorids);$j++)
				{
					$validatoruser='validateuser'.$validatorids[$j];
					$validatorlevel='level'.$validatorids[$j];
					$validatordata=array('journal_no'=>$journalid,'validate_user_id'=>$this->input->post($validatoruser),'validate_level_no'=>$this->input->post($validatorlevel));
					$this->design->add_journal_validatornonp($validatordata);
				}

				//Data Entry
				$dataentryid=$this->input->post('dataentryid');
				$dataentryids=explode(',',$dataentryid);
				for($j=0;$j<count($dataentryids);$j++)
				{
					$dataentryuser='dataentryuser'.$dataentryids[$j];
					$dataentryowner=$this->input->post('dataentryowner');
					if($dataentryowner==$dataentryids[$j])
					{
						$dataentrydata=array('journal_no'=>$journalid,'data_user_id'=>$this->input->post($dataentryuser),'default_owner_opt'=>'1');
					}
					else
					{
						$dataentrydata=array('journal_no'=>$journalid,'data_user_id'=>$this->input->post($dataentryuser),'default_owner_opt'=>'0');
					}
					$this->design->add_journal_data_entrynonp($dataentrydata);
				}

				//Data Attribute
				$dataattbcount=$this->input->post('dataattbcount');
				for($j=1;$j<=$dataattbcount;$j++)
				{
					$chk='dataattb'.$j;
					$order='order'.$j;
					$attbid='dataattbid'.$j;
					if($this->input->post($chk)=="on")
					{
						$dataattbdata=array('journal_no'=>$journalid,'data_attb_id'=>$this->input->post($attbid),'display_seq_no'=>$this->input->post($order));
						$this->design->add_journal_detailnonp($dataattbdata);
					}
				}

				//select frequency_detail_no from frequency_detail where '2014-01-01' between start_date and end_date
				$frequencystart=$this->design->show_frequency_detail_no($startdate);

				$session_data = $this->session->userdata('logged_in');
				$loginid = $session_data['id'];

				$frequencydata=array('journal_no'=>$journalid,'frequency_detail_no'=>$frequencystart,'data_entry_status_id'=>'1','created_user_id'=>$loginid,'created_date'=>date("Y-m-d"));
				$this->design->add_journal_data_entry_masternonp($frequencydata);

				/*if($enddate!='')
				{
					$frequencyend=$this->design->show_frequency_detail_no($enddate);
					for($j=$frequencystart;$j<=$frequencyend;$j++)
					{
						if($j==$frequencystart)
							$status="1";
						else
							$status="0";
						$frequencydata=array('journal_no'=>$journalid,'frequency_detail_no'=>$j,'data_entry_status_id'=>$status,'created_user_id'=>$loginid,'created_date'=>date("Y-m-d"));
						$this->design->add_journal_data_entry_masternonp($frequencydata);
					}
				}
				else
				{
					$frequencydata=array('journal_no'=>$journalid,'frequency_detail_no'=>$j,'data_entry_status_id'=>'1','created_user_id'=>$loginid,'created_date'=>date("Y-m-d"));
					$this->design->add_journal_data_entry_masternonp($frequencydata);
				}*/

				$sess_array = array('message' => $this->securitys->get_label_object(20)." Added Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Journal Name already exist",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>'','msg7'=>'','msg8'=>'','msg9'=>''));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(62);
		$label1=$this->securitys->get_label_object_name(63);
		$label2=$this->securitys->get_label_object_name(65);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname1',$label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname1', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('journalproperty1', $label1, 'trim|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user1', $label2, 'trim|required|xss_clean');
		//$this->form_validation->set_rules('frequency1', 'Frequency', 'trim|required|xss_clean');

		$attbcount=$this->input->post('dataattbcount1');
		$dataattberror='';
		$attbselect=0;
		$dataentryid=$this->input->post('dataentryid1');
		$dataentryids=explode(',',$dataentryid);
		$dataerror='';
		$datamsg='Select atleat one data owner ';
		for($j=0;$j<count($dataentryids);$j++)
		{
			$dataentryowner=$this->input->post('dataentryowner1');
			if($dataentryowner==$dataentryids[$j])
			{
				$dataerror="yes";
				$datamsg='';
			}
		}
		for($i=1;$i<=$attbcount;$i++)
		{
			$chk='1dataattb'.$i;
			$order='1order'.$i;
			if($this->input->post($chk)=="on")
			{
				if($this->input->post($order)=='' || !ctype_digit($this->input->post($order)))
				{
					$dataattberror="Enter Data Attribute Details or Invalid Input";
				}
				$attbselect=1;
			}
		}
		if($attbselect==0)
		{
			$dataattberror="Enter atleast one Data Attribute";
		}
		if($this->form_validation->run() == FALSE || $dataattberror!='' || $dataerror=='')
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname1'),'msg1'=>form_error('journalname1'),'msg2'=>form_error('user1'),'msg3'=>'','msg4'=>$dataattberror,'msg5'=>$datamsg,'msg6'=>form_error('journalproperty1')));
		}
		else
		{
			$name=$this->input->post('journalname1');
			$property=$this->input->post('journalproperty1');
			$projectno=$this->input->post('projectname1');
			$journalid=$this->input->post('editjournalno');
			if($this->design->update_check_journalnonp($name,$projectno,$journalid)==0)
			{
				$data = array('project_no' => $projectno,'journal_name' => $name,'journal_property'=>$property,'user_id' => $this->input->post('user1'));

				//query the database
				$this->design->update_journalnonp($journalid,$data);

				$this->design->delete_journal_validatornonp($journalid);
				//Validator
				$validatorid=$this->input->post('validatorid1');
				$validatorids=explode(',',$validatorid);
				for($j=0;$j<count($validatorids);$j++)
				{
					$validatoruser='1validateuser'.$validatorids[$j];
					$validatorlevel='1level'.$validatorids[$j];
					$validatordata=array('journal_no'=>$journalid,'validate_user_id'=>$this->input->post($validatoruser),'validate_level_no'=>$this->input->post($validatorlevel));
					$this->design->add_journal_validatornonp($validatordata);
				}

				$this->design->delete_journal_data_entrynonp($journalid);
				//Data Entry
				$dataentryid=$this->input->post('dataentryid1');
				$dataentryids=explode(',',$dataentryid);
				for($j=0;$j<count($dataentryids);$j++)
				{
					$dataentryuser='1dataentryuser'.$dataentryids[$j];
					$dataentryowner=$this->input->post('dataentryowner1');
					if($dataentryowner==$dataentryids[$j])
					{
						$dataentrydata=array('journal_no'=>$journalid,'data_user_id'=>$this->input->post($dataentryuser),'default_owner_opt'=>'1');
					}
					else
					{
						$dataentrydata=array('journal_no'=>$journalid,'data_user_id'=>$this->input->post($dataentryuser),'default_owner_opt'=>'0');
					}
					$this->design->add_journal_data_entrynonp($dataentrydata);
				}

				$this->design->delete_journal_detailnonp($journalid);
				//Data Attribute
				$dataattbcount=$this->input->post('dataattbcount1');
				for($j=1;$j<=$dataattbcount;$j++)
				{
					$chk='1dataattb'.$j;
					$order='1order'.$j;
					$attbid='1dataattbid'.$j;
					if($this->input->post($chk)=="on")
					{
						$dataattbdata=array('journal_no'=>$journalid,'data_attb_id'=>$this->input->post($attbid),'display_seq_no'=>$this->input->post($order));
						$this->design->add_journal_detailnonp($dataattbdata);
					}
				}

				$sess_array = array('message' => $this->securitys->get_label_object(20)." Updated Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Journal Name already exist",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>''));
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
			$sess_array = array('message' => $this->securitys->get_label_object(20)." Deleted Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(20).", Assigned to ".$this->securitys->get_label_object(21).$id);
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
	function close()
	{
		$id=$this->input->post('id');
		$session_data = $this->session->userdata('logged_in');
		$userid= $session_data['id'];
		$this->design->publish_journal_data_entrynonp($id,$userid);
		$sess_array = array('message' => "Project Journal Data Entry Closed Successfully");
		$this->session->set_userdata('message', $sess_array);
		echo json_encode(array('st'=>1, 'msg' => 'Success'));
	}
}
?>