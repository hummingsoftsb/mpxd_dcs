<?php
	header("Access-Control-Allow-Origin: *");

	$db = pg_connect('host=localhost dbname=pilot_db_new user=postgres password=mrt@mpxd!@#123');

	$data=$_GET['id'];
	
	if($data=="label")
	{
		$query="select sec_label_desc,seq_no from sec_label where sec_obj_id=21 order by seq_no asc";
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
		$query = "select (select journal_name from journal_master_nonprogressive jm where jm.journal_no=jdem.journal_no)as journal,(select project_name from project_template pt where pt.project_no in(select project_no from journal_master_nonprogressive jm1 where jm1.journal_no=jdem.journal_no))as project,jdem.data_entry_no,jdem.journal_no,jdem.frequency_detail_no,(select data_user_id from journal_data_user_nonprogressive jdu where jdu.journal_no=jdem.journal_no and default_owner_opt=1) as userid,(select start_date from journal_master_nonprogressive jm where jm.journal_no=jdem.journal_no)as start_date,(select end_date from journal_master_nonprogressive jm where jm.journal_no=jdem.journal_no)as end_date  from journal_data_entry_master_nonprogressive jdem where journal_no in (select journal_no from journal_data_user_nonprogressive where data_user_id=".$userid." and default_owner_opt=1) and data_entry_status_id=1 ";
        $result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }
		$cnt=pg_num_rows($result);
		$out='';
        while($myrow = pg_fetch_assoc($result)) {
       		$out .= $myrow['journal'].",".$myrow['project'].",".$myrow['data_entry_no'].",".$myrow['journal_no'].",".$myrow['frequency_detail_no'].",".$myrow['userid'].",".$myrow['start_date'].",".$myrow['end_date'].",777,";

		}
		if($cnt==0) {
		echo json_encode(array('st'=>0, 'msg' => 'No Record'));
		} else {
		echo json_encode(array('st'=>1, 'msg' => $out));
		}
	}
	
	if($data=="checkdataowner")
	{
		$userid=$_GET['userid'];
		$id=$_GET['dataentry'];
		$query="select journal_no from journal_data_user_nonprogressive where data_user_id=$userid and default_owner_opt=1 and journal_no in (select journal_no from journal_data_entry_master_nonprogressive where data_entry_no=$id)";
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
	
	if($data=="dataattb")
	{
		$id=$_GET['dataentry'];
		$query = "select * from data_attribute_nonprogressive where data_attb_id in (select data_attb_id from journal_data_entry_detail_nonprogressive where data_entry_no=".$id.")";
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
			$out .= $myrow['data_attb_id'].",".$myrow['data_attb_label'].",".$myrow['data_attb_type_id'].",777,";
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

	if($data=="journalimage")
	{
		$id=$_GET['dataentry'];
		$out='';
		$query="select data_entry_no,pict_seq_no,pict_file_name,pict_file_path,pict_definition,pict_user_id from journal_data_entry_picture_nonprogressive where data_entry_no=$id";
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

		$query="select journal_no from journal_data_entry_master_nonprogressive where data_entry_no=$id";
		$res=pg_query($query);
		while($myrow=pg_fetch_assoc($res))
		{
			$journalno=$myrow['journal_no'];
		}
		$query="select * from journal_detail_nonprogressive where journal_no=$journalno";
		$res=pg_query($query);
		while($myrow = pg_fetch_assoc($res))
		{
			$query="select data_entry_no from journal_data_entry_detail_nonprogressive where data_entry_no=$id and data_attb_id='".$myrow['data_attb_id']."'";
			$q=pg_query($query);
			if(pg_num_rows($q)==0)
			{
				$query="insert into journal_data_entry_detail_nonprogressive";
				$query .="(data_entry_no,data_attb_id,display_seq_no,data_source,created_user_id,created_date)";
				$query .=" values('$id','".$myrow['data_attb_id']."','".$myrow['display_seq_no']."',2,'$loginid','".date("Y-m-d")."')";
				pg_query($query);
			}
		}

		$query = "select * from journal_data_entry_detail_nonprogressive where data_entry_no='".$id."'";
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
			$out .= $myrow['data_entry_no'].",".$myrow['data_attb_id'].",".$myrow['actual_value'].",".$myrow['prev_actual_value'].",".$myrow['display_seq_no'].",".$myrow['data_source'].",777,";
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

		pg_query("update journal_data_entry_detail_nonprogressive set actual_value='$value' where data_entry_no=$id and data_attb_id=$attbid");
		
	}
	if($data=="updatedataimage")
	{
		$id=$_GET['dataentry'];
		$image=$_GET['image'];
		$value=$_GET['desc'];
		$userid=$_GET['userid'];

		pg_query("insert into journal_data_entry_picture_nonprogressive(data_entry_no,pict_file_name,pict_file_path,pict_definition,pict_user_id,data_source)values($id,'".$image."','/journalimagenonp/".$id."/".$userid."/','$value',$userid,2)");
		echo json_encode(array('st'=>1, 'msg' => 'sucess'));
		$query="select data_entry_pict_no from journal_data_entry_picture_nonprogressive where data_entry_no=$id order by data_entry_pict_no asc";
		$result=pg_query($query);
		$sno=1;
		while($myrow = pg_fetch_assoc($result))
		{
			pg_query("update journal_data_entry_picture_nonprogressive set pict_seq_no=$sno where data_entry_pict_no=".$myrow['data_entry_pict_no']);
			$sno++;
		}
	}
	if($data=="deletedataimage")
	{
		$id=$_GET['dataentry'];
		pg_query("delete from journal_data_entry_picture_nonprogressive where data_entry_no=".$id);
	}
	if($data=="publish")
	{
		$id=$_GET['dataentry'];
		pg_query("update journal_data_entry_master_nonprogressive set data_entry_status_id=1 where data_entry_no=".$id);
	}
?>