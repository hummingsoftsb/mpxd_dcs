<?php
	header("Access-Control-Allow-Origin: *");

	$db = pg_connect('host=localhost dbname=pilot_db_new user=postgres password=mrt@mpxd!@#123');

	$data=$_GET['id'];

	if($data=="label")
	{
		$query="select sec_label_desc,seq_no from sec_label where sec_obj_id=3 order by seq_no asc";
		$result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }
		$cnt=pg_num_rows($result);
		$out='';
        while($myrow = pg_fetch_assoc($result)) {
       		$out .= $myrow['sec_label_desc'].",".$myrow['seq_no'].",777,";

		}
		if($cnt==0) {
		echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		} else {
		echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}
	if($data=="journallist")
	{
		$userid=$_GET['userid'];
		$query = "select (select journal_name from journal_master jm where jm.journal_no=jdem.journal_no)as journal,(select project_name from project_template pt where pt.project_no in(select project_no from journal_master jm1 where jm1.journal_no=jdem.journal_no))as project,jdem.data_entry_no,jdem.journal_no,(select frequency_period from frequency_detail fm where fm.frequency_detail_no=jdem.frequency_detail_no) as frequency_detail_no,(select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1) as userid,(select start_date from journal_master jm where jm.journal_no=jdem.journal_no)as start_date,(select end_date from journal_master jm where jm.journal_no=jdem.journal_no)as end_date  from journal_data_entry_master jdem where journal_no in (select journal_no from journal_data_user where data_user_id=".$userid." and default_owner_opt=1) and data_entry_status_id=1 ";
        $result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

		$cnt=pg_num_rows($result);
		$out='';
        while($myrow = pg_fetch_assoc($result)) {
			if($myrow['frequency_detail_no']<=Date('W'))
			{
       			$out .= $myrow['journal'].",".$myrow['project'].",".$myrow['data_entry_no'].",".$myrow['journal_no'].",".$myrow['frequency_detail_no'].",".$myrow['userid'].",".$myrow['start_date'].",".$myrow['end_date'].",777,";
			}

		}
		if($cnt==0) {
		echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		} else {
		echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="checkpublish")
	{
		$userid=$_GET['userid'];
		$id=$_GET['dataentry'];
		$query="select journal_no from journal_data_entry_master where data_entry_no=$id and data_entry_status_id in (0,1)";
		$result=pg_query($query);
		$cnt=pg_num_rows($result);
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $cnt));
		}
	}

	if($data=="checkdataowner")
	{
		$userid=$_GET['userid'];
		$id=$_GET['dataentry'];
		$query="select journal_no from journal_data_user where data_user_id=$userid and default_owner_opt=1 and journal_no in (select journal_no from journal_data_entry_master where data_entry_no=$id)";
		$result=pg_query($query);
		$cnt=pg_num_rows($result);
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $cnt));
		}
	}

	if($data=="rejectjournallist")
	{
		$userid=$_GET['userid'];
		$query = "select (select journal_name from journal_master jm where jm.journal_no=jdem.journal_no)as journal,(select project_name from project_template pt where pt.project_no in(select project_no from journal_master jm1 where jm1.journal_no=jdem.journal_no))as project,jdem.data_entry_no,jdem.journal_no,(select frequency_period from frequency_detail fm where fm.frequency_detail_no=jdem.frequency_detail_no) as frequency_detail_no,(select data_user_id from journal_data_user jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1) as userid,(select start_date from journal_master jm where jm.journal_no=jdem.journal_no)as start_date,(select end_date from journal_master jm where jm.journal_no=jdem.journal_no)as end_date  from journal_data_entry_master jdem where journal_no in (select journal_no from journal_data_user where data_user_id=".$userid." and default_owner_opt=1) and data_entry_status_id=1  and data_entry_no in (select data_entry_no from journal_data_validate_master where validate_status=3) ";
        $result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }
		$cnt=pg_num_rows($result);
		$out='';
        while($myrow = pg_fetch_assoc($result)) {
			if($myrow['frequency_detail_no']<=Date('W'))
			{
       			$out .= $myrow['journal'].",".$myrow['project'].",".$myrow['data_entry_no'].",".$myrow['journal_no'].",".$myrow['frequency_detail_no'].",".$myrow['userid'].",".$myrow['start_date'].",".$myrow['end_date'].",777,";
			}

		}
		if($cnt==0) {
		echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		} else {
		echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="uom")
	{
		$id=$_GET['dataentry'];
		$query = "select * from unit_measure where uom_id in (select uom_id from data_attribute where data_attb_id in (select data_attb_id from journal_data_entry_detail where data_entry_no=".$id."))";
		$result = pg_query($query);
		if (!$result)
		{
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}
		$cnt=pg_num_rows($result);
		$out='';
		while($myrow = pg_fetch_assoc($result))
		{
			$out .= $myrow['uom_id'].",".$myrow['uom_name'].",777,";
		}
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="lookup")
	{
		$id=$_GET['dataentry'];
		$query = "select * from lookup_data where data_set_id in (select data_set_id from data_attribute where data_attb_id in (select data_attb_id from journal_data_entry_detail where data_entry_no=".$id."))";
		$result = pg_query($query);
		if (!$result)
		{
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}
		$cnt=pg_num_rows($result);
		$out='';
		while($myrow = pg_fetch_assoc($result))
		{
			$out .= $myrow['data_set_id'].",".$myrow['lk_code'].",777,";
		}
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="lookupdata")
	{
		$id=$_GET['dataentry'];
		$query = "select * from lookup_data_detail where data_set_id in (select data_set_id from data_attribute where data_attb_id in (select data_attb_id from journal_data_entry_detail where data_entry_no=".$id."))";
		$result = pg_query($query);
		if (!$result)
		{
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}
		$cnt=pg_num_rows($result);
		$out='';
		while($myrow = pg_fetch_assoc($result))
		{
			$out .= $myrow['data_set_detail_id'].",".$myrow['data_set_id'].",".$myrow['lk_data'].",".$myrow['lk_value'].",777,";
		}
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="dataattb")
	{
		$id=$_GET['dataentry'];
		$query = "select * from data_attribute where data_attb_id in (select data_attb_id from journal_data_entry_detail where data_entry_no=".$id.")";
		$result = pg_query($query);
		if (!$result)
		{
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}
		$cnt=pg_num_rows($result);
		$out='';
		while($myrow = pg_fetch_assoc($result))
		{
			$out .= $myrow['data_attb_id'].",".$myrow['data_attb_label'].",".$myrow['data_attb_type_id'].",".$myrow['data_set_id'].",".$myrow['data_attb_data_type_id'].",".$myrow['data_attb_digits'].",".$myrow['uom_id'].",".$myrow['field_lock'].",777,";
		}
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}

	if($data=="validator")
	{
		$id=$_GET['dataentry'];
		$out='';
		$query="select validate_level_no,(select user_full_name from sec_user where user_id=validate_user_id) as name ,data_entry_no from journal_data_validate_master where data_entry_no=$id";
		$res=pg_query($query);
		while($myrow=pg_fetch_assoc($res))
		{
			$out .=$id.",".$myrow['validate_level_no'].",".$myrow['name'].",777,";
		}
		echo json_encode(array('st'=>1,'msg'=>$out));
	}

	if($data=="journalimage")
	{
		$id=$_GET['dataentry'];
		$out='';
		$query="select data_entry_no,pict_seq_no,pict_file_name,pict_file_path,pict_definition,pict_user_id from journal_data_entry_picture where data_entry_no=$id";
		$res=pg_query($query);
		while($myrow=pg_fetch_assoc($res))
		{
			//$imagedesc=file_get_contents("C:/xampp/htdocs/mrt_mpxd".$myrow['pict_file_path'].$myrow['pict_file_name']);
			//$encoded_data = base64_encode($imagedesc);
			$imagedesc=$myrow['pict_file_path'].$myrow['pict_file_name'];
			$out .=$id.",".$myrow['pict_seq_no'].",".$imagedesc.",".$myrow['pict_definition'].",777,";
		}
		echo json_encode(array('st'=>1,'msg'=>$out));
	}



	if($data=="journalentry")
	{
		$id=$_GET['dataentry'];
		$loginid=$_GET['loginid'];

		$query="select data_entry_no from journal_data_entry_detail where data_entry_no=$id";
		$q=pg_query($query);
		if(pg_num_rows($q)==0)
		{
			$query="select journal_no from journal_data_entry_master where data_entry_no=$id";
			$res=pg_query($query);
			while($myrow=pg_fetch_assoc($res))
			{
				$journalno=$myrow['journal_no'];
			}
			$query="select * from journal_detail where journal_no=$journalno";
			$res=pg_query($query);
			while($myrow = pg_fetch_assoc($res))
			{
				$query="insert into journal_data_entry_detail";
				$query .="(data_entry_no,data_attb_id,start_value,actual_value,end_value,frequency_max_value,display_seq_no,data_source,created_user_id,created_date)";
				$query .=" values('$id','".$myrow['data_attb_id']."','".$myrow['start_value']."','".intval($myrow['start_value'])."','".$myrow['end_value']."','".$myrow['frequency_max_value']."','".$myrow['display_seq_no']."',2,'$loginid','".date("Y-m-d")."')";
				pg_query($query);
			}
			$query="select frequency_detail_no from journal_data_entry_master where data_entry_no=$id";
			$res=pg_query($query);
			while($myrow = pg_fetch_assoc($res))
			{
				$frequencyno=$myrow['frequency_detail_no'];
				$frequencyno--;
			}
			$query="select data_attb_id,actual_value,data_entry_no from journal_data_entry_detail where data_entry_no=(select data_entry_no from journal_data_entry_master where frequency_detail_no=$frequencyno and journal_no=$journalno)";
			$res=pg_query($query);
			while($myrow = pg_fetch_assoc($res))
			{
				pg_query("update journal_data_entry_detail set actual_value='".$myrow['actual_value']."',prev_actual_value='".$myrow['actual_value']."' where data_entry_no=$id and data_attb_id=".$myrow['data_attb_id']);
				$res1=pg_query("select cur_user_id,cur_date from journal_data_entry_audit_log where data_entry_no=".$myrow['data_entry_no']." and data_attb_id=".$myrow['data_attb_id']." order by audit_log_no desc limit 1");
				while($myrow1 = pg_fetch_assoc($res1))
				{
					$prevuserid=$myrow1['cur_user_id'];
					$prevdate=$myrow1['cur_date'];
				}
				$query="insert into journal_data_entry_audit_log";
				$query .="(data_entry_no,data_attb_id,cur_user_id,cur_date,cur_value,prv_value,prv_user_id,prv_date)";
				$query .=" values('$id','".$myrow['data_attb_id']."','$loginid','".date("Y-m-d")."','".$myrow['actual_value']."','".$myrow['actual_value']."',".$prevuserid.",'".$prevdate."')";
				pg_query($query);
			}

			$query="select * from journal_validator where journal_no=$journalno";
			$res=pg_query($query);
			while($myrow = pg_fetch_assoc($res))
			{
				$query="insert into journal_data_validate_master";
				$query .="(data_entry_no,validate_user_id,validate_level_no,validate_status)";
				$query .=" values('$id','".$myrow['validate_user_id']."','".$myrow['validate_level_no']."',0)";
				pg_query($query);
			}
		}

		$query = "select *,(SELECT validate_comment FROM journal_data_validate_detail jdvd where jdvd.data_attb_id = jded.data_attb_id AND jdvd.data_entry_no = jded.data_entry_no) as comments from journal_data_entry_detail jded where data_entry_no='".$id."'";
		$result = pg_query($query);
		if (!$result)
		{
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}
		$cnt=pg_num_rows($result);
		$out='';
		while($myrow = pg_fetch_assoc($result))
		{
			$out .= $myrow['data_entry_no'].",".$myrow['data_attb_id'].",".$myrow['actual_value'].",".$myrow['start_value'].",".$myrow['end_value'].",".$myrow['frequency_max_value'].",".$myrow['prev_actual_value'].",".$myrow['frequency_max_opt'].",".$myrow['display_seq_no'].",".$myrow['data_source'].",".$myrow['comments'].",777,";
		}
		if($cnt==0)
		{
			echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		}
		else
		{
			echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}
	if($data=="updatedataentry")
	{
		$id=$_GET['dataentry'];
		$attbid=$_GET['dataattb'];
		$value=$_GET['value'];
		$userid=$_GET['userid'];
		$varient=$_GET['varient'];
		
		if($varient!="")
			pg_query("update journal_data_entry_detail set actual_value='$value',frequency_max_opt='$varient' where data_entry_no=$id and data_attb_id=$attbid");
		else
			pg_query("update journal_data_entry_detail set actual_value='$value' where data_entry_no=$id and data_attb_id=$attbid");

		$results=pg_query("select cur_value,cur_user_id,cur_date from journal_data_entry_audit_log where data_entry_no=$id and data_attb_id=$attbid order by audit_log_no desc limit 1");
		if(pg_num_rows($results)==0)
		{
			pg_query("insert into journal_data_entry_audit_log(data_entry_no,data_attb_id,cur_value,cur_user_id,cur_date)values($id,$attbid,'$value',$userid,'".date('Y-m-d')."')");
		}
		else
		{
			while($myrow = pg_fetch_assoc($results))
			{
				$prevvalue=$myrow['cur_value'];
				$prevuser=$myrow['cur_user_id'];
				$prevdate=$myrow['cur_date'];
			}
			if($value!=$prevvalue)
			{
				pg_query("insert into journal_data_entry_audit_log(data_entry_no,data_attb_id,cur_value,cur_user_id,cur_date,prv_value,prv_user_id,prv_date)values($id,$attbid,'$value',$userid,'".date('Y-m-d')."','$prevvalue',$prevuser,'$prevdate')");
			}
		}
	}
	if($data=="updatevarient")
	{
		$id=$_GET['dataentry'];
		$attbid=$_GET['dataattb'];
		$varient=$_GET['varient'];
		pg_query("update journal_data_entry_detail set frequency_max_opt='$varient' where data_entry_no=$id and data_attb_id=$attbid");
	}
	if($data=="updatedataimage")
	{
		$id=$_GET['dataentry'];
		$image=$_GET['image'];
		$value=$_GET['desc'];
		$userid=$_GET['userid'];

		pg_query("insert into journal_data_entry_picture(data_entry_no,pict_file_name,pict_file_path,pict_definition,pict_user_id,data_source)values($id,'".$image."','/journalimage/".$id."/".$userid."/','$value',$userid,2)");
		echo json_encode(array('st'=>1, 'msg' => 'sucess'));
		$query="select data_entry_pict_no from journal_data_entry_picture where data_entry_no=$id order by data_entry_pict_no asc";
		$result=pg_query($query);
		$sno=1;
		while($myrow = pg_fetch_assoc($result))
		{
			pg_query("update journal_data_entry_picture set pict_seq_no=$sno where data_entry_pict_no=".$myrow['data_entry_pict_no']);
			$sno++;
		}
	}
	if($data=="deletedataimage")
	{
		$id=$_GET['dataentry'];
		pg_query("delete from journal_data_entry_picture where data_entry_no=".$id);
	}
	if($data=="publish")
	{
		$id=$_GET['dataentry'];
		$userid=$_GET['userid'];

		pg_query("update journal_data_entry_master set data_entry_status_id=2,publish_user_id=$userid,publish_date='".date("Y-m-d")."' where data_entry_no=$id");

		$query="select data_validate_no from journal_data_validate_master where data_entry_no=$id and validate_status in (0,3) order by validate_level_no asc limit 1";
		$q=pg_query($query);
		while($myrow=pg_fetch_assoc($q))
		{
			$datavalid=$myrow['data_validate_no'];
		}
		pg_query("update journal_data_validate_master set validate_status=1 where data_validate_no=$datavalid");
		
		
		// Notification -- added by ilyas
		
		$query = "SELECT validate_user_id FROM journal_data_validate_master where data_entry_no=$id";
		$q = pg_query($query);
		$myrow=pg_fetch_assoc($q);
		$validate_user_id=$myrow['validate_user_id'];
		$alertdate = date("Y-m-d");
		//$data = array('alert_date' => ,'alert_user_id' => $validatorid,'data_entry_no' => $id,'alert_message' => 'Data Entry Published','alert_hide' => '0','email_send_option' => '1');
		$query_alert = "INSERT INTO user_alert (alert_date, alert_user_id, data_entry_no, alert_message, alert_hide, email_send_option) VALUES ('$alertdate', '$validate_user_id', '$id', 'Data Entry Published', '0', '0') ";
		$q=pg_query($query_alert);
		//var_dump($q);
		echo json_encode(array('st'=>1, 'msg' => $out));
	}

?>