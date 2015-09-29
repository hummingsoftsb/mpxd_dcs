<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Designjournal extends CI_Controller
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"7");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="designjournal" && $_SERVER['QUERY_STRING']=="")
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
			$config['base_url'] = base_url().'index.php/designjournal/index';
			$config['total_rows'] = $this->design->totaljournal($search);
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
			$data['records'] = $this->design->show_journal($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='designjournal';
			$data['labels']=$this->securitys->get_label(7);
			$data['labelgroup']=$this->securitys->get_label_group(7);
			$data['labelobject']=$this->securitys->get_label_object(7);
			$data['projects']=$this->design->show_projtmps();
			$data['users']=$this->securitys->show_users();
			$data['frequencys']=$this->design->show_frequency();
			$data['dataattbs']=$this->admin->show_dataatts();
			$data['dataattbgroups']=$this->admin->show_dataattrgrps();

			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
			
			$data['message_type']=$type;

			//Load Validator for each Journal
			$data['validatorvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_validator($record->journal_no);
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
				$datavalues = $this->design->show_journal_data_user($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->data_user_id.",".$datavaluerow->default_owner_opt.",777,";
	        	}
        		$data['dataentryvalue'][$record->journal_no]=$datavalue;
        		$data['is_images'][$record->journal_no]=$record->is_image; //journal type;
        	}

			//Load Validator for each Journal
			$data['dataattbvalue'] = array ();
			foreach ( $data['records'] as $record )
			{
				$datavalue="";
				$datavalues = $this->design->show_journal_data_attb($record->journal_no);
				foreach ( $datavalues as $datavaluerow )
				{
					$datavalue.=$datavaluerow->data_attb_id.",".$datavaluerow->start_value.",".$datavaluerow->end_value.",".$datavaluerow->frequency_max_value.",".$datavaluerow->display_seq_no.",777,";
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
			$this->load->view('design_journal', $data);
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
		$label=$this->securitys->get_label_object_name(77);
		$label1=$this->securitys->get_label_object_name(78);
		$label2=$this->securitys->get_label_object_name(80);
		$label3=$this->securitys->get_label_object_name(81);
		$label4=$this->securitys->get_label_object_name(82);
		$label5=$this->securitys->get_label_object_name(193);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname', $label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('journalproperty', $label1, 'trim|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('user', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('frequency', $label3, 'trim|required|xss_clean');
		$this->form_validation->set_rules('startdate', $label4, 'trim|required|numeric_dash|xss_clean');
		$this->form_validation->set_rules('enddate', 'End Date', 'trim|numeric_dash|xss_clean');
		$this->form_validation->set_rules('j_type', '$label5', 'trim|numeric_dash|xss_clean');
		$this->form_validation->set_rules('albumname', '$label5', 'trim|xss_clean');
		
		$journal_type = $this->input->post('j_type'); if($journal_type == 2) {$is_image = 1;} else {$is_image = 0;} //Check journal type. Return 1 if image type;
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
		for($i=1;$i<=$attbcount-1;$i++)
		{
			//$chk='dataattb'.$i;
			$start='start'.$i;
			$end='end'.$i;
			$week='week'.$i;
			$order='order'.$i;
			/*if($this->input->post($chk)=="on")
			{*/
				if($this->input->post($start)=='' /*|| !ctype_digit($this->input->post($start))*/)
				{
					$dataattberror="Start value is required.";
				}
				if($this->input->post($end)=='' /*|| !ctype_digit($this->input->post($end))*/)
				{
					$dataattberror="End value is required.";
				}
				if($this->input->post($week)=='' /*|| !ctype_digit($this->input->post($week))*/)
				{
					$dataattberror="Weekly Max value is required.";
				}
				if($this->input->post($order)=='' || !ctype_digit($this->input->post($order)))
				{
					//$dataattberror="Enter Data Attribute Details or Invalid Input 4 asd ".$attbcount.' dsa';
					$dataattberror="Attribute Order is required.";
				}
				//$attbselect=1;
			//}
		}
		/*if($attbselect==0)
		{
			$dataattberror="Enter atleast one Data Attribute";
		}*/
		$enderror='';
		if($this->input->post('enddate')!='')
		{
			$projectend=date("Y-m-d", strtotime($this->design->show_proj_field('end_date',$this->input->post('projectname'))));
			$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			if($projectend<$enddate)
			{
				$enderror='End date should be less than the Project End Date ('.$projectend.')';
			}
		}
		
		
		if($this->form_validation->run() == FALSE || $dataattberror!='' || $dataerror=='' || $enderror!='')
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname'),'msg1'=>form_error('journalname'),'msg2'=>form_error('user'),'msg3'=>form_error('frequency'),'msg4'=>form_error('startdate'),'msg5'=>'','msg6'=>$dataattberror,'msg7'=>$datamsg,'msg8'=>$enderror,'msg9'=>form_error('journalproperty')));
		}
		else
		{
			$name=$this->input->post('journalname');
			$property=$this->input->post('journalproperty');
			$projectno=$this->input->post('projectname');
			$startdate=date("Y-m-d", strtotime($this->input->post('startdate')));
			$dependency=$this->input->post('dependency');
			if (json_decode($dependency) && (json_last_error()!=JSON_ERROR_NONE)) { echo "JSON ERROR IN DEPENDENCY!"; die(); }
		
		
			if($this->input->post('enddate')!="")
			{
				$enddate=date("Y-m-d", strtotime($this->input->post('enddate')));
			}
			else
			{
				$enddate=NULL;
			}
			if($this->design->add_check_journal($name,$projectno)==0)
			{
				$data = array('project_no' => $projectno,'journal_name' => $name,'journal_property' => $property,'user_id' => $this->input->post('user'),'start_date' => $startdate ,'end_date' => $enddate,'frequency_no' => $this->input->post('frequency'), 'dependency' => $dependency, 'is_image' => $is_image, 'album_name' => $this->input->post('albumname'));

				//query the database
				$journalid = $this->design->add_journal($data,$projectno,$name);

				//Validator
				$validatorid=$this->input->post('validatorid');
				$validatorids=explode(',',$validatorid);
				for($j=0;$j<count($validatorids);$j++)
				{
					$validatoruser='validateuser'.$validatorids[$j];
					$validatorlevel='level'.$validatorids[$j];
					$validatordata=array('journal_no'=>$journalid,'validate_user_id'=>$this->input->post($validatoruser),'validate_level_no'=>$this->input->post($validatorlevel));
					$this->design->add_journal_validator($validatordata);
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
					$this->design->add_journal_data_entry($dataentrydata);
				}

				//Data Attribute
				$dataattbcount=$this->input->post('dataattbcount');
				for($j=1;$j<=$dataattbcount;$j++)
				{
					//$chk='dataattb'.$j;
					$start='start'.$j;
					$end='end'.$j;
					$week='week'.$j;
					$order='order'.$j;
					$attbid='dataattbid'.$j;
					//if($this->input->post($chk)=="on")
					//{
					if (($this->input->post($attbid)) != FALSE) {
						$dataattbdata=array('journal_no'=>$journalid,'data_attb_id'=>$this->input->post($attbid),'start_value'=>$this->input->post($start),'end_value'=>$this->input->post($end),'frequency_max_value'=>$this->input->post($week),'display_seq_no'=>$this->input->post($order));
						$this->design->add_journal_detail($dataattbdata);
					}
					//}
				}

				//select frequency_detail_no from frequency_detail where '2014-01-01' between start_date and end_date
				$frequencystart=$this->design->show_frequency_detail_no($startdate);
				$session_data = $this->session->userdata('logged_in');
				$loginid = $session_data['id'];

				if($enddate!='')
				{
					$frequencyend=$this->design->show_frequency_detail_no($enddate);
					for($j=$frequencystart;$j<=$frequencyend;$j++)
					{
						if($j==$frequencystart)
							$status="1";
						else
							$status="0";
						$frequencydata=array('journal_no'=>$journalid,'frequency_detail_no'=>$j,'data_entry_status_id'=>$status,'created_user_id'=>$loginid,'created_date'=>date("Y-m-d"));
						$this->design->add_journal_data_entry_master($frequencydata);
					}
				}
				else
				{
					$frequencydata=array('journal_no'=>$journalid,'frequency_detail_no'=>$frequencystart,'data_entry_status_id'=>'1','created_user_id'=>$loginid,'created_date'=>date("Y-m-d"));
					$this->design->add_journal_data_entry_master($frequencydata);
				}

				$sess_array = array('message' => $this->securitys->get_label_object(7)." Added Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label1." already exist",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>'','msg7'=>'','msg8'=>'','msg9'=>'',));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(77);
		$label1=$this->securitys->get_label_object_name(78);
		$label2=$this->securitys->get_label_object_name(80);
		$label3=$this->securitys->get_label_object_name(81);
		$label4=$this->securitys->get_label_object_name(82);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('projectname1', $label, 'trim|required|xss_clean');
		$this->form_validation->set_rules('journalname1', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('journalproperty1', $label1, 'trim|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('albumname1', $label1, 'trim|xss_clean');
		$this->form_validation->set_rules('user1', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('frequency1', $label3, 'trim|required|xss_clean');

		$attbcount=$this->input->post('dataattbcount1');
		$dataattberror='';
		$attbselect=0;
		$dataentryid=$this->input->post('dataentryid1');
		$dataentryids=explode(',',$dataentryid);
		$dataerror='';
		$datamsg='Select at least one data owner ';
		for($j=0;$j<count($dataentryids);$j++)
		{
			$dataentryowner=$this->input->post('dataentryowner1');
			if($dataentryowner==$dataentryids[$j])
			{
				$dataerror="yes";
				$datamsg='';
			}
		}
		for($i=1;$i<=$attbcount-1;$i++)
		{
			//$chk='1dataattb'.$i;
			$start='1start'.$i;
			$end='1end'.$i;
			$week='1week'.$i;
			$order='1order'.$i;
			//$attbid='1dataattbid'.$i;
			//var_dump($attbid);
			/*if($this->input->post($chk)=="on")
			{*/
			
				if($this->input->post($start)=='' /*|| !ctype_digit($this->input->post($start))*/)
				{
					$dataattberror="Start value is required.";
				}
				if($this->input->post($end)=='' /*|| !ctype_digit($this->input->post($end))*/)
				{
					$dataattberror="End value is required.";
				}
				if($this->input->post($week)=='' /*|| !ctype_digit($this->input->post($week))*/)
				{
					$dataattberror="Weekly Max value is required.";
				}
				if($this->input->post($order)=='' || !ctype_digit($this->input->post($order)))
				{
					//$dataattberror="Enter Data Attribute Details or Invalid Input 4 asd ".$attbcount.' dsa';
					$dataattberror="Attribute Order is required.";
				}
				//$attbselect=1;
			//}
		}
		/*if($attbselect==0)
		{
			$dataattberror="Enter atleast one Data Attribute";
		}*/
		if($this->form_validation->run() == FALSE || $dataattberror!='' || $dataerror=='')
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('projectname1'),'msg1'=>form_error('journalname1'),'msg2'=>form_error('user1'),'msg3'=>form_error('frequency1'),'msg4'=>$dataattberror,'msg5'=>$datamsg,'msg6'=>form_error('journalproperty1')));
		}
		else
		{
			$name=$this->input->post('journalname1');
			$property=$this->input->post('journalproperty1');
			$projectno=$this->input->post('projectname1');
			$journalid=$this->input->post('editjournalno');
			$dependency=$this->input->post('dependency');
			$albumname=$this->input->post('albumname1');
			
			if (json_decode($dependency) && (json_last_error()!=JSON_ERROR_NONE)) { echo "JSON ERROR IN DEPENDENCY!"; die(); }
			
			if($this->design->update_check_journal($name,$projectno,$journalid)==0)
			{
				$data = array('project_no' => $projectno,'journal_name' => $name,'journal_property' => $property,'user_id' => $this->input->post('user1'),'frequency_no' => $this->input->post('frequency1'), 'dependency' => $dependency, 'album_name' => $albumname);

				//query the database
				$this->design->update_journal($journalid,$data);

				$this->design->delete_journal_validator($journalid);
				//Validator
				$validatorid=$this->input->post('validatorid1');
				$validatorids=explode(',',$validatorid);
				for($j=0;$j<count($validatorids);$j++)
				{
					$validatoruser='1validateuser'.$validatorids[$j];
					$validatorlevel='1level'.$validatorids[$j];
					$validatordata=array('journal_no'=>$journalid,'validate_user_id'=>$this->input->post($validatoruser),'validate_level_no'=>$this->input->post($validatorlevel));
					$this->design->add_journal_validator($validatordata);
				}

				$this->design->delete_journal_data_entry($journalid);
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
					$this->design->add_journal_data_entry($dataentrydata);
				}

				$this->design->delete_journal_detail($journalid);
				//Data Attribute
				$dataattbcount=$this->input->post('dataattbcount1');
				for($j=1;$j<=$dataattbcount;$j++)
				{
					$chk='1dataattb'.$j;
					$start='1start'.$j;
					$end='1end'.$j;
					$week='1week'.$j;
					$order='1order'.$j;
					$attbid='1dataattbid'.$j;
					//var_dump($this->input->post($chk));
					//var_dump($this->input->post($attbid));
					if($this->input->post($chk)=="on")
					{
						if (($this->input->post($attbid)) != FALSE) {
							$dataattbdata=array('journal_no'=>$journalid,'data_attb_id'=>$this->input->post($attbid),'start_value'=>$this->input->post($start),'end_value'=>$this->input->post($end),'frequency_max_value'=>$this->input->post($week),'display_seq_no'=>$this->input->post($order));
							$this->design->add_journal_detail($dataattbdata);
						}
					}
				}

				$sess_array = array('message' => $this->securitys->get_label_object(7)." Updated Successfully","type" => 1);
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>''));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => $label1." already exist",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>''));
			}
		}
	}

	function delete()
	{
		$id=$this->input->post('id');
		if($this->design->delete_check_journal($id)==0)
		{
			//query the database
			$result = $this->design->delete_journal($id);
			$sess_array = array('message' => $this->securitys->get_label_object(7)." Deleted Successfully","type" => 1);
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(7).", Assigned to ".$this->securitys->get_label_object(3)." ".$id,"type" => 0);
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
}
?>