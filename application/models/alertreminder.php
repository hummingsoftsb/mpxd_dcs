<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Alertreminder extends CI_Model
{

    function show_alert($id)
    {
        //$query ="select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,jm.journal_name,ua.alert_message,fd.frequency_period,alert_hide from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no order by alert_no desc";

        //Modified by Jane. For getting all notifications to admin user - progressive
        $user_role_query = "select sec_role_id from sec_user where user_id=$id";
        $user_role_query = $this->db->query($user_role_query)->result();
        if($user_role_query[0]->sec_role_id==1){
            $query = "select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_seen_status,ua.alert_user_id,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 order by alert_no desc";
            $query = $this->db->query($query);
            $query_result = $query->result();
        } else {
            $query = "select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_seen_status,ua.alert_user_id,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 order by alert_no desc";
            /*echo $query;
            echo "</br>";*/
            $query = $this->db->query($query);
            $query_result = $query->result();
        }

        //We remove duplicate notification for the same entry
        /*$query_result_filtered = array();
        foreach($query_result as $r){
            if(isset($check_dup[$r->data_entry_no])){
                continue;
            }
            else{
                $check_dup[$r->data_entry_no] = TRUE;
                array_push($query_result_filtered,$r);
            }
        }*/

        //Modified by Jane. For getting all notifications to admin user - non progressive
        if($user_role_query[0]->sec_role_id==1) {
            $query = "select ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_message,ua.alert_seen_status,ua.alert_user_id,ua.nonp_journal_id from user_alert ua where alert_hide=0 AND ua.data_entry_no IS NULL order by alert_no desc";
            $query = $this->db->query($query);
            $query_result2 = $query->result();
        } else {
            $query = "select ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_message,ua.alert_seen_status,ua.alert_user_id,ua.nonp_journal_id from user_alert ua where alert_hide=0 AND ua.data_entry_no IS NULL AND ua.alert_user_id=$id order by alert_no desc";
            $query = $this->db->query($query);
            $query_result2 = $query->result();
        }
        foreach ($query_result2 as $qr2):
            $qr2->journal_name = "";
            $qr2->frequency_period = "";
            $qr2->data_validate_no = FALSE;
        endforeach;

        /*$query_result2_filtered = array();
        foreach($query_result2 as $r){
            if(isset($check_dup[$r->nonp_journal_id])){
                continue;
            }
            else{
                $check_dup[$r->nonp_journal_id] = TRUE;
                array_push($query_result2_filtered,$r);
            }
        }*/

        // added by jane for avoiding the duplication of entry
        $temp_array = array();
        $i = 0;
        $key_array = array();
        foreach ($query_result as $val) {
            if (!in_array($val->data_entry_no, $key_array)) {
                $key_array[$i] = $val->data_entry_no;
                $temp_array[$i] = $val;
            }
            $i++;
        }

        $temp_array_nonp = array();
        $i = 0;
        $key_array_nonp = array();
        foreach ($query_result2 as $val) {
            if (!in_array($val->nonp_journal_id, $key_array_nonp)) {
                $key_array_nonp[$i] = $val->nonp_journal_id;
                $temp_array_nonp[$i] = $val;
            }
            $i++;
        }
        $merged = array_merge($temp_array, $temp_array_nonp);
        foreach ($merged as $k => $m) {
            //$data_validate_no[$k] = $m->data_validate_no;
            //$data_entry_no[$k] = $m->data_entry_no;
            $alert_no[$k] = $m->alert_no;
            //$alert_date[$k] = $m->alert_date;
            //$journal_name[$k] = $m->journal_name;
            //$alert_message[$k] = $m->alert_message;
            //$frequency_period[$k] = $m->frequency_period;
        }

        if (count($merged) != 0) {
            array_multisort($alert_no, SORT_DESC, $merged);
        }
        return $merged;
    }




//	function show_alert($id)
//	{
//		//$query ="select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,jm.journal_name,ua.alert_message,fd.frequency_period,alert_hide from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no order by alert_no desc";
//		$query ="select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 order by alert_no desc";
//		$query = $this->db->query($query);
//		$query_result = $query->result();
//
//		//We remove duplicate notification for the same entry
//		$query_result_filtered = array();
//		foreach($query_result as $r){
//			if(isset($check_dup[$r->data_entry_no])){
//				continue;
//			}
//			else{
//				$check_dup[$r->data_entry_no] = TRUE;
//				array_push($query_result_filtered,$r);
//			}
//		}
//
//		// Added by ilyas
//		$query ="select ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_message, ua.nonp_journal_id from user_alert ua where alert_hide=0 AND ua.data_entry_no IS NULL AND ua.alert_user_id=$id order by alert_no desc";
//		$query = $this->db->query($query);
//		$query_result2 = $query->result();
//
//		//$merged = (object) array_merge((array) $obj1, (array) $obj2);
//
//		foreach ($query_result2 as $qr2):
//			$qr2->journal_name = "";
//			$qr2->frequency_period = "";
//			$qr2->data_validate_no = FALSE;
//		endforeach;
//
//		$query_result2_filtered = array();
//		foreach($query_result2 as $r){
//			if(isset($check_dup[$r->nonp_journal_id])){
//				continue;
//			}
//			else{
//				$check_dup[$r->nonp_journal_id] = TRUE;
//				array_push($query_result2_filtered,$r);
//			}
//		}
//
//		$merged = array_merge($query_result, $query_result2);
//		//$merged = array_merge($query_result_filtered, $query_result2_filtered);
//
//		foreach($merged as $k => $m){
//
//			//$data_validate_no[$k] = $m->data_validate_no;
//			//$data_entry_no[$k] = $m->data_entry_no;
//			$alert_no[$k] = $m->alert_no;
//			//$alert_date[$k] = $m->alert_date;
//			//$journal_name[$k] = $m->journal_name;
//			//$alert_message[$k] = $m->alert_message;
//			//$frequency_period[$k] = $m->frequency_period;
//		}
//
//		if(count($merged) != 0){
//			array_multisort($alert_no, SORT_DESC, $merged);
//		}
//		//var_dump($query_result);
//		//var_dump($query_result2);
//		//var_dump($merged);
//		return $merged;
//	}

    /*function to return count of user alerts*/
	function count_alert($id)
	{
		// $query=$this->db->query("select alert_no from user_alert where alert_hide=0 and alert_user_id=".$id);
        $user_role_query = "select sec_role_id from sec_user where user_id=$id";
        $user_role_query = $this->db->query($user_role_query)->result();
        if($user_role_query[0]->sec_role_id==1) {
            /*for getting all notifications to admin user*/
            $query = $this->db->query("select alert_no from user_alert where alert_hide=0");
        } else {
            $query = $this->db->query("select alert_no from user_alert where alert_hide=0 and alert_seen_status = 0 and alert_user_id=" . $id);
        }
		return $query->num_rows();
	}

	function hide_alert($id)
	{
		$this->db->query("update user_alert set alert_hide=1 where alert_no=".$id);
	}

	function del_alert($id)
	{
		$this->db->where('alert_no', $id);
		$this->db->delete('user_alert');
	}
//	function show_reminder($id)
//	{
//		$query ="select ur.reminder_status_id, ur.data_entry_no,ur.reminder_no,ur.reminder_date,jm.journal_name,ur.reminder_message,fd.frequency_period from user_reminder ur,journal_data_entry_master jdem,frequency_detail fd,journal_master jm where ur.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ur.reminder_user_id=$id and reminder_hide=0 order by reminder_no desc";
//		// $query = "select * from user_reminder";
//		$query = $this->db->query($query);
//		$query_result = $query->result();
//		return $query_result;
//	}

// modified by jane

// Start

    function show_reminder($id)
    {
        //For progressive reminder
        $query ="select ur.reminder_status_id, ur.data_entry_no,ur.reminder_no,ur.reminder_date,jm.journal_name,ur.reminder_message,fd.frequency_period from user_reminder ur,journal_data_entry_master jdem,frequency_detail fd,journal_master jm where ur.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ur.reminder_user_id=$id and reminder_hide=0 order by reminder_no desc";
        $query = $this->db->query($query);
        $query_result = $query->result();
        //For non progressive reminder
        $query2 = "select ur.reminder_status_id, ur.nonp_journal_id,ur.reminder_no,ur.reminder_date,jmnp.journal_name,ur.reminder_message,jmnp.reminder_frequency from user_reminder ur,journal_master_nonprogressive jmnp,frequency_detail fd where ur.nonp_journal_id=jmnp.journal_no and ur.reminder_user_id=$id and reminder_hide=0 group by ur.reminder_no,jmnp.journal_name,jmnp.reminder_frequency order by reminder_no desc";
        $query2 = $this->db->query($query2);
        $query_result2 = $query2->result();
        $merge = array_merge($query_result, $query_result2);
        return $merge;
    }




//end

	function count_reminder($id)
	{
		$query=$this->db->query("select reminder_no from user_reminder where reminder_hide=0 and reminder_user_id=".$id);
		return $query->num_rows();
	}

	function hide_reminder($id)
	{
		$this->db->query("update user_reminder set reminder_hide=1 where reminder_no=".$id);
	}

	function del_reminder($id)
	{
		$this->db->where('reminder_no', $id);
		$this->db->delete('user_reminder');
	}
    //function for updating user alert seen status
    function update_reminder_status($alert_id,$alert_user_id){
        $this->db->query("update user_alert set alert_seen_status=1 where alert_no=$alert_id AND alert_user_id=$alert_user_id");
    }
}


?>