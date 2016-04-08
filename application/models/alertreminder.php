<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Alertreminder extends CI_Model
{
    //by smijith for status read progressive
    function pg_read_upd()
    {
        $qrypg = "select data_entry_no from journal_data_entry_master where data_entry_status_id=4";
        $qrypg = $this->db->query($qrypg)->result();
        foreach ($qrypg as $qr) {
            $did = $qr->data_entry_no;
            //echo("update user_alert set alert_seen_status=1 where data_entry_no=" . $did);
            $this->db->query("update user_alert set alert_seen_status=1 where data_entry_no=" . $did);
        }
    }
    //by smijith for status read non-progressive
    function nonpg_read_upd(){
        $qrynonpg = "select distinct journal_no from ilyas_config where config_no in (select distinct config_no from ilyas where validate_status=2)";
        $qrynonpg = $this->db->query($qrynonpg)->result();
        foreach ($qrynonpg as $qr){
            $did=$qr->journal_no;
            //echo("update user_alert set alert_seen_status=1 where nonp_journal_id=" . $did);
            $this->db->query("update user_alert set alert_seen_status=1 where nonp_journal_id=".$did);
        }
    }

    function show_alert($id)
    {

        //$query ="select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,jm.journal_name,ua.alert_message,fd.frequency_period,alert_hide from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no order by alert_no desc";

        //Modified by Jane. For getting all notifications to admin user - progressive
        $user_role_query = "select sec_role_id from sec_user where user_id=$id";
        $user_role_query = $this->db->query($user_role_query)->row();
        if($user_role_query->sec_role_id==1){
            /*$query = "select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_seen_status,ua.alert_user_id,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 and jdvm.validate_status!=4 and jdem.data_entry_status_id!=4 order by alert_no desc";*/
            $query = "select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_seen_status,ua.alert_user_id,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 order by alert_no desc";
            $query = $this->db->query($query);
            $query_result = $query->result();
            if (!empty($query_result)) {
                foreach ($query_result as $subKey => $subArray) {
                    //coded by ANCY MATHEW
                    //ANCY :START
                    if($subArray->alert_message == 'Data Entry Published' && ($subArray->alert_user_id != $id))
                    {
                    $intDataEntryNo=$subArray->data_entry_no;
                        $get_dataEntry_query = "select validate_status from journal_data_validate_master where data_entry_no=$intDataEntryNo";
                        $get_dataEntry_query = $this->db->query($get_dataEntry_query)->row();
                        if($get_dataEntry_query->validate_status==2){
                            unset($query_result[$subKey]);
                        }
                    }
                    //ANCY :END
                    if ($subArray->alert_message == 'Date Entry Accepted' && ($subArray->alert_user_id != $id)) {
                        unset($query_result[$subKey]);
                    }
                }
            }
        } else {
            $query = "select jdvm.data_validate_no,jdvm.validate_status,jdem.data_entry_status_id,ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_seen_status,ua.alert_user_id,jm.journal_name,ua.alert_message,fd.frequency_period from user_alert ua,journal_data_entry_master jdem,frequency_detail fd,journal_master jm, journal_data_validate_master jdvm where ua.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ua.alert_user_id=$id and jdem.data_entry_no=jdvm.data_entry_no and alert_hide=0 order by alert_no desc";
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
        if($user_role_query->sec_role_id==1) {
            $query = "select ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_message,ua.alert_seen_status,ua.alert_user_id,ua.nonp_journal_id,jmn.journal_name from user_alert ua, journal_master_nonprogressive jmn where ua.alert_hide=0 AND ua.data_entry_no IS NULL AND jmn.journal_no=ua.nonp_journal_id order by alert_no desc";
            $query = $this->db->query($query);
            $query_result2 = $query->result();
            if (!empty($query_result2)) {
                foreach ($query_result2 as $subKey => $subArray) {
                    if ($subArray->alert_message == $subArray->journal_name . " " . 'Data Entry Accepted' && ($subArray->alert_user_id != $id)) {
                        unset($query_result2[$subKey]);
                    }
                }
            }
        } else {
            $query = "select ua.data_entry_no,ua.alert_no,ua.alert_date,ua.alert_message,ua.alert_seen_status,ua.alert_user_id,ua.nonp_journal_id,jmn.journal_name from user_alert ua, journal_master_nonprogressive jmn where ua.alert_hide=0 AND ua.data_entry_no IS NULL AND jmn.journal_no=ua.nonp_journal_id AND ua.alert_user_id=$id order by alert_no desc";
            $query = $this->db->query($query);
            $query_result2 = $query->result();
        }
        if (!empty($query_result2)) {
            foreach ($query_result2 as $qr2):
                $qr2->journal_name = "";
                $qr2->frequency_period = "";
                $qr2->data_validate_no = FALSE;
            endforeach;
        }
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
        if (!empty($query_result)) {
            foreach ($query_result as $val) {
                if (!in_array($val->data_entry_no, $key_array)) {
                    $key_array[$i] = $val->data_entry_no;
                    $temp_array[$i] = $val;
                }
                $i++;
            }
        }
        $temp_array_nonp = array();
        $i = 0;
        $key_array_nonp = array();
        if (!empty($query_result2)) {
            foreach ($query_result2 as $val) {
                if (!in_array($val->nonp_journal_id, $key_array_nonp)) {
                    $key_array_nonp[$i] = $val->nonp_journal_id;
                    $temp_array_nonp[$i] = $val;
                }
                $i++;
            }
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
        $user_role_query = $this->db->query($user_role_query)->row();
        if($user_role_query->sec_role_id==1) {
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
    function show_reminder($id)
    {
        //For progressive reminder
        $user_role_query = "select sec_role_id from sec_user where user_id=$id";
        $user_role_query = $this->db->query($user_role_query)->row();
        if ($user_role_query->sec_role_id == 1) {
            //$query = "select ur.reminder_status_id, ur.data_entry_no,ur.reminder_no,ur.reminder_date::timestamp(0),jm.journal_name,ur.reminder_message,fd.frequency_period,sc.user_full_name,sr.sec_role_name, max(url.TIMESTAMP) from user_reminder ur,journal_data_entry_master jdem,frequency_detail fd,journal_master jm,sec_user sc,sec_role sr,user_reminder_log url where ur.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no and jdem.journal_no=jm.journal_no and ur.reminder_user_id = sc.user_id and sc.sec_role_id = sr.sec_role_id and reminder_hide=0 and ur.data_entry_no = url.data_entry_no group by sc.user_full_name,ur.reminder_status_id,ur.data_entry_no,ur.reminder_no,jm.journal_name,fd.frequency_period,sr.sec_role_name order by reminder_no desc";
            $query = "select a.journal_no,b.data_entry_no,a.journal_name,c.reminder_status_id,c.reminder_no,c.reminder_date::timestamp(0),c.reminder_message,d.frequency_period,e.user_full_name,e.sec_role_id,f.sec_role_name,
                        max(g.timestamp)maxt
                        from journal_master a join journal_data_entry_master b on a.journal_no = b.journal_no
                        join user_reminder c on b.data_entry_no = c.data_entry_no and c.reminder_hide=0
                        join frequency_detail d on b.frequency_detail_no = d.frequency_detail_no
                        join sec_user e on c.reminder_user_id = e.user_id join sec_role f on e.sec_role_id = f.sec_role_id left join user_reminder_log g on b.data_entry_no = g.data_entry_no
                        group by a.journal_no,b.data_entry_no,a.journal_name,c.reminder_status_id,c.reminder_no,c.reminder_date,d.frequency_period,e.user_full_name,f.sec_role_name,e.sec_role_id";
            $query = $this->db->query($query);
            $query_result = $query->result();
        } else {
            $query = "select ur.reminder_status_id, ur.data_entry_no,ur.reminder_no,ur.reminder_date::timestamp(0),jm.journal_name,ur.reminder_message,fd.frequency_period, sc.user_full_name, sc.sec_role_id,sr.sec_role_name,case sc.sec_role_id when '2' then (select data_validate_no from journal_data_validate_master jdvm where ur.data_entry_no=jdvm.data_entry_no and jdvm.validate_user_id = $id) end as data_validate_no
                         from user_reminder ur,journal_data_entry_master jdem,frequency_detail fd,journal_master jm,sec_user sc,sec_role sr where ur.data_entry_no=jdem.data_entry_no and jdem.frequency_detail_no=fd.frequency_detail_no
                         and jdem.journal_no=jm.journal_no and ur.reminder_user_id = sc.user_id and sc.sec_role_id = sr.sec_role_id and ur.reminder_user_id=$id and reminder_hide=0
                         group by sc.user_full_name,ur.data_entry_no, ur.reminder_status_id,ur.reminder_no,jm.journal_name,fd.frequency_period,sc.sec_role_id,sr.sec_role_name order by reminder_no desc";

            $query = $this->db->query($query);
            $query_result = $query->result();
        }
        //For non progressive reminder
        if ($user_role_query->sec_role_id == 1) {
            //$query2 = "select ur.reminder_status_id, ur.nonp_journal_id,ur.reminder_no,ur.reminder_date::timestamp(0),jmnp.journal_name,ur.reminder_message,jmnp.reminder_frequency,sc.user_full_name,sr.sec_role_name, max(url.TIMESTAMP) from user_reminder ur,journal_master_nonprogressive jmnp,frequency_detail fd,sec_user sc,sec_role sr,user_reminder_log url where ur.nonp_journal_id=jmnp.journal_no and ur.reminder_user_id = sc.user_id and sc.sec_role_id = sr.sec_role_id and reminder_hide=0 and ur.nonp_journal_id = url.nonp_journal_id group by ur.reminder_no,jmnp.journal_name,jmnp.reminder_frequency,sc.user_full_name,sr.sec_role_name order by reminder_no desc";
            $query2 = "select a.journal_name,a.journal_no,a.reminder_frequency,b.reminder_status_id,b.nonp_journal_id,b.reminder_no,b.reminder_date::timestamp(0),b.reminder_message,
                        c.user_full_name,d.sec_role_name,max(f.timestamp)maxt
                        from journal_master_nonprogressive a
                        join user_reminder b on a.journal_no=b.nonp_journal_id and b.reminder_hide=0
                        join sec_user c on b.reminder_user_id = c.user_id
                        join sec_role d on c.sec_role_id = d.sec_role_id
                        left join user_reminder_log f on b.nonp_journal_id = f.nonp_journal_id
                        group by a.journal_name,a.journal_no,a.reminder_frequency,b.reminder_status_id,b.nonp_journal_id,b.reminder_no,c.user_full_name,d.sec_role_name";
            $query2 = $this->db->query($query2);
            $query_result2 = $query2->result();
        } else {
            $query2 = "select ur.reminder_status_id, ur.nonp_journal_id,ur.reminder_no,ur.reminder_date::timestamp(0),jmnp.journal_name,ur.reminder_message,jmnp.reminder_frequency,sc.user_full_name,sr.sec_role_name
                         from user_reminder ur,journal_master_nonprogressive jmnp,frequency_detail fd,sec_user sc,sec_role sr where ur.nonp_journal_id=jmnp.journal_no and ur.reminder_user_id = sc.user_id and sc.sec_role_id = sr.sec_role_id and ur.reminder_user_id=$id and reminder_hide=0
                         group by ur.reminder_no,jmnp.journal_name,jmnp.reminder_frequency,sc.user_full_name,sr.sec_role_name order by reminder_no desc";
            $query2 = $this->db->query($query2);
            $query_result2 = $query2->result();
        }
        $merge1 = array_merge($query_result, $query_result2);
        return $merge1;
    }

	function count_reminder($id)
	{
        $user_role_query = "select sec_role_id from sec_user where user_id=$id";
        $user_role_query = $this->db->query($user_role_query)->row();
        if($user_role_query->sec_role_id==1) {
            /*for getting all notifications to admin user*/
            $query = $this->db->query("select reminder_no from user_reminder where reminder_hide=0");
        } else {
            $query = $this->db->query("select reminder_no from user_reminder where reminder_hide=0 and reminder_user_id=" . $id);
        }
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
    //function for updating user alert seen status. done by jane
    function update_reminder_status($alert_id,$alert_user_id){
        $this->db->query("update user_alert set alert_seen_status=1 where alert_no=$alert_id AND alert_user_id=$alert_user_id");
    }
}


?>