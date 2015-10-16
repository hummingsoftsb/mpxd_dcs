<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	$ci = & get_instance();
	$id=$ci->input->post('id');
	$is_mobile = $ci->input->post('is_mobile') == 1;
	//$id="4780";
	$result=$ci->assessment->get_journal_data_entry_details($id);
	$varientvalue=array();
	$varientcount=0;

	foreach($result as $rows):
		$varient=ceil($rows->varient);
		if($varient>0)
		{
			array_push($varientvalue, array(
				$rows->data_entry_no,
				$rows->data_attb_id,
				$rows->data_attb_label,
				$rows->prev_actual_value,
				$rows->actual_value,
				$rows->uom_name,
				$rows->start_value,
				$rows->end_value,
				$rows->frequency_max_value,
				ceil($rows->varient)
				));
			$varientcount++;
		}
	endforeach;

	if($is_mobile || sizeOf($varientvalue) == 0)
	{
		$session_data = $ci->session->userdata('logged_in');
		$userid= $session_data['id'];
		$ci->assessment->publish_journal_data_entry($id,$userid);

		//Email
		 $emailres=$ci->assessment->publish_journal_data_entry_email($id,$userid);
		 //update alert based on data_entry_no
		 $ci->assessment->update_alert_on_save($id,$userid);
		foreach($emailres as $rows):
			$datavalidateno=$rows->data_validate_no;
			$journalno=$rows->journal_no;
			$journalname=$rows->journalname;
			$dataentryname=$rows->dataentryname;
			$validatorname=$rows->validatorname;
			$validatoremail=$rows->validatoremail;
			$validatorid=$rows->validatorid;
		endforeach;
		
		/*
		$ci->email->from('test@hummingsoft.com.my', 'MPXD');
		$ci->email->to($validatoremail);
		$message="Dear ".$validatorname.", <br>".$journalname." data entry published by ".$dataentryname.".Now the journal is ready for validation";
		$ci->email->subject($journalname.' data entry completed');
		$ci->email->message($message);
		$ci->email->send();*/
		$data = array('alert_date' => date("Y-m-d"),'alert_user_id' => $validatorid,'data_entry_no' => $id,'alert_message' => 'Data Entry Published','alert_hide' => '0','email_send_option' => '1');
		$ci->assessment->add_user_alert($data);
		
		$ci->load->model('mailermodel');
		$ci->mailermodel->insert_queue_published($validatorid, 'progressive', $id);
		
		//$ci->swiftmailer->data_entry_published_progressive($validatoremail, $validatorname, $dataentryname, $journalname, $datavalidateno);

		$sess_array = array('message' => "Journal Published Successfully","type" => 1);
		$ci->session->set_userdata('message', $sess_array);
	}
	echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>$varientvalue,'msg2'=>$varientcount));