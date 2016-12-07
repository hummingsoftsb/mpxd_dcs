<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class IlyasModel extends CI_Model
{
	
	function get_config($jid, $all = FALSE) {
		$this->db->select('config_no,col_header,col_width,uom_id,type,col_order,validate_pending,validate_revision,lookup_id,read_only,progressive_link,non_progressive_link,formula');
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->order_by('col_order', 'asc');
		$q = $this->db->get();
		$result = [];
		if (!$all) {
		foreach ($q->result() as $i):
			$i = (array) $i;
			array_push($result, [
				'header' => $i['col_header'],
				'width' => $i['col_width'],
				'type' => $i['type'],
				'uom' => $i['uom_id'],
				'order' => $i['col_order'],
				'readonly' => $i['read_only'],
				'lookup_id' => $i['lookup_id'],
				'progressive_link' => $i['progressive_link'],
				'non_progressive_link' => $i['non_progressive_link'],
				'formula' => $i['formula']]);
		endforeach;
		}
		else {
			foreach ($q->result() as $i):
			$i = (array) $i;
			array_push($result, [
				'config_no' => $i['config_no'],
				'header' => $i['col_header'],
				'width' => $i['col_width'],
				'type' => $i['type'],
				'uom' => $i['uom_id'],
				'validate_pending' => $i['validate_pending'],
				'validate_revision' => $i['validate_revision'],
				'order' => $i['col_order'],
				'readonly' => $i['read_only'],
				'lookup_id' => $i['lookup_id'],
				'progressive_link' => $i['progressive_link'],
				'non_progressive_link' => $i['non_progressive_link'],
				'formula' => $i['formula']]);
		endforeach;
		}
		return $result;
	}
	
	/* Save config by giving journal id, and config data from hot table */
	function save_config($jid, $cols) {
		if ((sizeOf($cols) < 1) || (!is_array($cols)) || (!is_array($cols[0]))) die("No data"); 
		
		
		$configs_db = $this->get_config($jid, true);
		$confignos_db = [];
		$confignos_hot = [];
		$configs_tocreate = [];
		$configs_toupdate = [];
		$confighot_assoc = [];
		$inserted = 0;
		$deleted = 0;
		$updated = 0;
		foreach ($configs_db as $c):
			array_push($confignos_db, $c['config_no']);
		endforeach;
		
		$confignos_hot = [];
		foreach ($cols as $c):
			$c['readonly'] = ($c['readonly'] === true) ? '1' : '0';
			if (isset($c['config_no'])) { 
				if (!(($c['type'] == "lookup") && isset($c['lookup_id']))) $c['lookup_id'] = null;
				if (!(($c['type'] == "progressive_link") && isset($c['progressive_link']))) $c['progressive_link'] = null;
				if (!(($c['type'] == "non_progressive_link") && isset($c['non_progressive_link']))) $c['non_progressive_link'] = null;
				if (!(($c['type'] == "formula") && isset($c['formula']))) $c['formula'] = null;
				array_push($confignos_hot, $c['config_no']);
				$confighot_assoc[$c['config_no']] = $c;
			}
			else array_push($configs_tocreate, $c);
		endforeach;
		//var_dump($confignos_db);
		//var_dump($cols);
		//var_dump($confighot_assoc);
		// Should fix the following line with a proper way..
		//array_walk_recursive( $confignos_db, array( $this, 'escape_value' ) );
		$confignos_toupdate = array_intersect($confignos_hot, $confignos_db);
		$confignos_todelete = array_diff($confignos_db, $confignos_hot);
		
		
		
		//$confignos_tocreate = array_diff($confignos_hot, $confignos_db);
		$insertdata = [];
		//var_dump($confignos_toupdate);
		//var_dump($confignos_todelete);
		//var_dump($confignos_tocreate);
		//$config = $this->get_config($jid, true);
		
		
		
		$now = '\''.date('Y-m-d H:i:s').'\'';
		
		
		$this->db->trans_start();
		
		// Update existing configs 
		for ($i = 0; $i < sizeOf($confignos_toupdate); $i++) {
			$c = $confighot_assoc[$confignos_toupdate[$i]];
			$d = array(
				'uom_id' => $c['uom'],
				'col_header' => $c['header'],
				'type' => $c['type'],
				'col_order' => $c['order'],
				'col_width' => $c['width'],
				'lookup_id' => $c['lookup_id'],
				'read_only' => $c['readonly'],
				'progressive_link' => $c['progressive_link'],
				'non_progressive_link' => $c['non_progressive_link'],
				'formula' => $c['formula']
			);
			$this->db->update('ilyas_config', $d, array('config_no' => $c['config_no']));
			$updated++;
		}
		
		if (sizeOf($confignos_todelete) > 0) {
			// Delete config_nos that no longer exists 
			$this->db->where_in('config_no', $confignos_todelete);
			$deleteq = $this->db->delete('ilyas_config'); 
			$deleted = sizeOf($confignos_todelete);
		}
		if (sizeOf($configs_tocreate) > 0) {
			/* Sanitze data */
			array_walk_recursive( $configs_tocreate, array( $this, 'escape_value' ) );
			
			// Create new configs
			for ($i = 0; $i < sizeOf($configs_tocreate); $i++) {
				$d = $configs_tocreate[$i];
				
				array_push($insertdata, array($jid, $d['uom'], $d['header'], $d['width'], $d['type'], $d['order'], $d['lookup_id'], $d['readonly'], $d['progressive_link'], $d['non_progressive_link'], $d['formula']));
			}
			
			function fixnullvalue(& $a) {
				
				foreach ($a as $k => $i):
					if (is_null($i)) $a[$k] = "null";
				endforeach;
				
			}
			
			array_walk($insertdata, "fixnullvalue");
			
			//var_dump($insertdata);
			/* Stringify rows */
			for($i = 0; $i < sizeOf($insertdata); $i++) $insertdata[$i] = implode(',', $insertdata[$i]);
			$query = "insert into ilyas_config ";
			$query .= "(journal_no,uom_id,col_header,col_width,type,col_order,lookup_id,read_only,progressive_link,non_progressive_link,formula) ";
			
			
			$query .= "values (" . implode( '),(', $insertdata ) . ")";
			
			$this->db->query($query);
			$inserted = sizeOf($insertdata);
		}
		$q = $this->db->trans_complete();
		
		return array(
			'status' => $q,
			'log' => array(
				"Inserted" => $inserted,
				"Deleted" => $deleted,
				"Updated" => $updated
			)
		);
		
	}
	
	function empty_rows($jid) {
		$this->db->select('config_no,col_header,col_width,uom_id,type');
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->order_by('col_order', 'asc');
		$qj = $this->db->get()->result();
		$confignos = $this->get_confignos( $qj );
		
		$this->db->where_in("config_no", $confignos);
		//print_r($this->db);
		//$this->db->from("ilyas");
		$this->db->delete("ilyas");
		//$this->db->get();
		$q = $this->db->last_query();
		//var_dump($q);
		return $q;
	}
	
	
	/* Save data by giving journal id, and columns data from hot table */
	function save_data($jid, $cols, $userid, $data_date = False, $pub = False) {
		//var_dump($cols); die();
		if ((sizeOf($cols) < 1) || (!is_array($cols)) || (!is_array($cols[0]))) die("No data"); 

		$insertdata = [];
		$config = $this->get_config($jid, true);
		
		if (!$data_date) {
			$data_date = $this->get_data_date($jid);
		}
		
		if (!$data_date) $data_date = '\'\'';
		else $data_date = '\''.pg_escape_string($data_date).'\'';
		
		if (sizeOf($cols) != sizeOf($config)) die("Columns mismatch");
		
		//var_dump($this->db->last_query());
		//var_dump($cols);
		//var_dump($config);
		$now = '\''.date('Y-m-d H:i:s').'\'';
		
		/* Revision = revision + 1 */
		$revision = $this->get_latest_revision($jid)+1;
		if ($pub) {
			$pubvalue = 1;
			$this->set_validationlock($jid, 1, $revision);
		} else {
			$pubvalue = 0;
		}
		
		// Prepare configs for audit
		$column_headers = $this->get_config($jid);
		//var_dump($column_headers);die();
		
		$log = array();
		$log['headers'] = $column_headers;
		$log['data'] = $cols;
		
		// Audit query
		$audit_query = "insert into journal_nonprogressive_data_entry_audit_log (journal_no,log,timestamp,revision,data_date,user_id) values('$jid', '".json_encode($log)."', $now, '$revision', $data_date, $userid)";
		$q_audit = $this->db->query($audit_query);
		
		/* Sanitze data */
		array_walk_recursive( $cols, array( $this, 'escape_value' ) );
		for ($i = 0; $i < sizeOf($config); $i++) {
			if (!is_array($cols[$i])) die ("Invalid data");
			for ($row = 0; $row < sizeOf($cols[$i]); $row++){
				/* If validation enforcement is required, this is the best place to validate at cell-level */
				if (is_null($cols[$i][$row])) $cols[$i][$row] = "''";
				array_push($insertdata, array($config[$i]['config_no'], $row, $cols[$i][$row], $now, $revision, $pubvalue, $userid, $data_date));
			}
		}
		//var_dump($insertdata);die();
		
		/* Stringify rows */
		for($i = 0; $i < sizeOf($insertdata); $i++) $insertdata[$i] = implode(',', $insertdata[$i]);
		
		$query = "insert into ilyas ";
		$query .= "(config_no,row,value,timestamp,revision,validate_status,user_id,data_date) ";
		$query .= "values (" . implode( '),(', $insertdata ) . ")";
		
		//$query="select * from lookup_data_detail";
		
		$q=$this->db->query($query);
		
		
		return $q;
		//return //$this->insert_rows('ilyas', array('row', 'col', 'value', 'timestamp'), array("0","0","1234",date('Y-m-d')));
	}
	/*
	function get_latest_revision($config_no) {
		$this->db->select_max('revision');
		$this->db->where('config_no', $config_no);
		$this->db->from('ilyas');
		$qr = $this->db->get();
		$revision = $qr->row()->revision;
		return $revision;
	}*/
	
	/* Get the latest revision of data based on journal id */
	function get_latest_revision($jid) {
		
		$filter = function($v){ return $v['config_no']; };
		$configs = array_map($filter, $this->get_config($jid, true));
		if (sizeOf($configs) < 1) return [];
		$this->db->select_max('revision');
		$this->db->where_in('config_no', $configs);
		$this->db->from('ilyas');
		$qr = $this->db->get();
		$revision = $qr->row()->revision;
		return $revision;
	}

    /* Added by Sebin for Get the latest revision of data of rejected or accepted based on journal id*/

    function get_latest_rev($jid) {
        $rev_int = array('0', '1');
        $filter = function($v){ return $v['config_no']; };
        $configs = array_map($filter, $this->get_config($jid, true));
        if (sizeOf($configs) < 1) return [];
        $this->db->select_max('revision');
        $this->db->where_in('config_no', $configs);
        $this->db->where_not_in('validate_status', $rev_int);
        $this->db->from('ilyas');
        $qr = $this->db->get();
        $revision = $qr->row()->revision;
        return $revision;
    }

    /* End*/
	
	function get_data($jid, $rev = '') {
		$this->db->select('config_no,col_header,col_width,uom_id,type');
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->order_by('col_order', 'asc');
		$qj = $this->db->get();
		$resultarray = [];
		if ($rev == '') {
			$revision = $this->get_latest_revision($jid);
		} else {
			$revision = str_replace("'", "", $rev);
		}
		//var_dump(is_null($revision));
		
		if (is_null($revision)) return [];
		
		foreach ($qj->result() as $i):
		
			/* Get the associated rows of the column */
			$resultcolumn = [];
			$this->db->select('row,value');
			$this->db->from('ilyas');
			$this->db->where('config_no', $i->config_no);
			$this->db->where('revision', $revision);
			$this->db->order_by('row', 'asc');
			$q = $this->db->get();
			foreach ($q->result() as $j):
				array_push($resultcolumn, $j->value);
			endforeach;

			array_push($resultarray, $resultcolumn);
		endforeach;
		/* If a column-based result is desired, comment the following procedure which transposes column in to rows */
		$resultarray = $this->transpose($resultarray);

		return $resultarray;
	}
    function get_data_compare($jid, $rev = '') {
		// echo $rev;
		// exit;
        $this->db->select('config_no,col_header,col_width,uom_id,type,lookup_id');
        $this->db->from('ilyas_config');
        $this->db->where('journal_no', $jid);
        $this->db->order_by('col_order', 'asc');
        $qj = $this->db->get();
        $resultarray = [];
        if ($rev == '') {
            $revision = $this->get_latest_revision($jid);
        } else {
            $revision = str_replace("'", "", $rev);
        }
        //var_dump(is_null($revision));

        if (is_null($revision)) return [];
        //Ancy Mathew
        //using highlighter
        foreach ($qj->result() as $i):
            $resultcolumn = [];
            if($i->type == "lookup") {
                $this->db->select('row,value');
                $this->db->from('ilyas');
                $this->db->where('config_no', $i->config_no);
                $this->db->where('revision', $revision);
                $this->db->order_by('row', 'asc');
                $arr = $this->db->get();
                foreach ($arr->result() as $a):
                    if (!empty($a->value) && (!empty($i->lookup_id))) {
                        $this->db->select('lk_data as value');
                        $this->db->from('lookup_data_detail');
                        $this->db->where('data_set_id', $i->lookup_id);
                        $this->db->where('lk_value', $a->value, 0);
                        $q = $this->db->get();
                        foreach ($q->result() as $j):
                            array_push($resultcolumn, $j->value);
                        endforeach;
                    }else{
						array_push($resultcolumn, "");
					}
                endforeach;
            } else {
                $this->db->select('row,value');
                $this->db->from('ilyas');
                $this->db->where('config_no', $i->config_no);
                $this->db->where('revision', $revision);
			    $this->db->order_by('row', 'asc');
				$q = $this->db->get();
				foreach ($q->result() as $j):
					array_push($resultcolumn, $j->value);
				endforeach;
            }
            array_push($resultarray, $resultcolumn);
        endforeach;
        /* If a column-based result is desired, comment the following procedure which transposes column in to rows */
        $resultarray = $this->transpose($resultarray);
        return $resultarray;
    }
	function get_data_date($jid){
		$filter = function($v){ return $v['config_no']; };
		$configs = array_map($filter, $this->get_config($jid, true));
		if (sizeOf($configs) < 1) return [];
		$rev = $this->get_latest_revision($jid);
		$this->db->select('data_date');
		$this->db->where_in('config_no', $configs);
		$this->db->where('data_date IS NOT NULL');
		$this->db->where('data_date != \'\'');
		$this->db->where('revision',$rev);
		$this->db->from('ilyas');
		$qr = $this->db->get();
		$result = $qr->result();
		if (sizeOf($result) < 1) return false;
		return $result[0]->data_date;
	}
	
	function get_validation_comment($jid) {
		if (!is_numeric($jid)) return false;
		//$this->db->query('SELECT a.validate_comment,a.config_no,b.col_order FROM ilyas a, ilyas_config b WHERE a.config_no=b.config_no AND a.validate_comment!='' AND a.config_no IN('290') AND a.revision='26'
		$this->db->select('config_no,col_header,col_width,uom_id,type');
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->order_by('col_order', 'asc');
		$qj = $this->db->get()->result();
		$resultarray = [];
		$revision = $this->get_latest_revision($jid);
		if (is_null($revision)) return [];
		$confignos = $this->get_confignos($qj);
		
		
		$confignos_s = "('".implode($confignos,"','")."')";
		//var_dump($revision);
		//var_dump($confignos_s);
		$query = "SELECT a.validate_comment,a.config_no, a.row,b.col_order FROM ilyas a, ilyas_config b WHERE a.config_no=b.config_no AND a.validate_comment!='' AND a.config_no IN $confignos_s AND a.revision=$revision";
		//die();
		
		$q = $this->db->query($query);
		$result = [];
		if ($q) {
			foreach ($q->result() as $i):
				array_push($result, array("row" => $i->row, "col" => $i->col_order, "comment" => $i->validate_comment));
			endforeach;
		}
		
		return $result;
	}
	function get_validation_comment_row($jid) {
		if (!is_numeric($jid)) return false;
		//$this->db->query('SELECT a.validate_comment,a.config_no,b.col_order FROM ilyas a, ilyas_config b WHERE a.config_no=b.config_no AND a.validate_comment!='' AND a.config_no IN('290') AND a.revision='26'
		/*$this->db->select('config_no,col_header,col_width,uom_id,type');
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->order_by('col_order', 'asc');
		$qj = $this->db->get()->result();*/
		
		$revision = $this->get_latest_revision($jid);
		if (is_null($revision)) return [];
		$query = "SELECT DISTINCT ON (a.row) a.validate_comment_row,a.row FROM ilyas a, ilyas_config b WHERE a.config_no=b.config_no AND a.validate_comment_row IS NOT NULL AND a.revision=$revision AND b.journal_no = $jid ORDER BY a.row ASC";
//		$query = "select DISTINCT ON (row) validate_comment_row,row from ilyas,ilyas_config where ilyas.config_no = ilyas_config.config_no and ilyas_config.journal_no = $jid and revision = $revision";
		//die();
//        print_r($query);
		$q = $this->db->query($query);
		$result = $q->result();
		//var_dump($result);
		return $result;
	}
	
	function get_journals_nonp($data,$offset=0,$perPage,$userid,$roleid) {
		
		/* Get all journals for the current user */
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		
		$isSort = ($data=="project_name asc" || $data=="project_name desc" || $data=="journal_name asc" || $data=="journal_name desc");
		$isSearch = (($data != "") && (!$isSort));
		$isOwner = ($userid != "1" && $roleid !="1");
		
		//$owner = "AND b.journal_no IN (SELECT journal_no FROM journal_data_user_nonprogressive WHERE data_user_id=$userid and default_owner_opt=1)";
		$owner = "AND b.journal_no IN (SELECT journal_no FROM journal_data_user_nonprogressive WHERE data_user_id=$userid)";
		$search = " AND (lower(a.project_name) like '%".$data."%' or lower(b.journal_name) like '%".$data."%' )";
		
		$query = "SELECT * FROM (SELECT DISTINCT ON(b.journal_no) i.config_no,i.col_header,i.col_width,i.uom_id,i.type,i.col_order,a.project_name,b.nonp_enabled,b.journal_name, b.project_no, b.reminder_frequency ,b.journal_no,e.user_full_name, e.user_id AS owner_user_id, jvn.validate_user_id, jdu.data_user_id FROM project_template a, journal_master_nonprogressive b, sec_user e, journal_validator_nonprogressive jvn, journal_data_user_nonprogressive jdu, ilyas_config i WHERE a.project_no=b.project_no AND b.user_id=e.user_id AND jvn.journal_no=b.journal_no AND jdu.journal_no=b.journal_no AND i.journal_no=b.journal_no ";
		//AND a.project_name='Power Supply And Distribution System' ".($isOwner ? $owner : "")." ".($isSearch ? $search : ""). "
		// Sorting function
		
		if($isSearch)
		{
			$query .= $search;
		}
		
		$query .= "Order by b.journal_no asc) a ";
		
		
		if ($isSort) {
			$query.=" Order By ".$data;
		} else {
			$query.=" Order By project_name asc,journal_name asc";
		}
		
		
		if (!is_null($perPage)) {
			$query .= " Offset $offset Limit $perPage";
		} else {
			$query .= "";
		}
		
		//print_r($query);
		//var_dump($query);
		
		//SELECT * FROM (SELECT DISTINCT ON (b.journal_no) i.config_no,i.col_header,i.col_width,i.uom_id,i.type,i.col_order, a.project_name,b.nonp_enabled,b.journal_name, b.project_no, b.reminder_frequency ,b.journal_no,e.user_full_name, e.user_id AS owner_user_id, jvn.validate_user_id, jdu.data_user_id FROM project_template a, journal_master_nonprogressive b, sec_user e, journal_validator_nonprogressive jvn, journal_data_user_nonprogressive jdu, ilyas_config i WHERE a.project_no=b.project_no AND b.user_id=e.user_id AND jvn.journal_no=b.journal_no AND jdu.journal_no=b.journal_no AND i.journal_no=b.journal_no Order By b.journal_no asc) a Order By a.project_name asc
		
		$q = $this->db->query($query);
		//var_dump($this->db->last_query());
		
		//$q = $this->db->get();
		
		//var_dump($this->db->last_query());
		if (!$q) return [];
		
		$resultarray = [];
		
		foreach ($q->result() as $j):
			array_push($resultarray, $j);
		endforeach;
		
		return $resultarray;
		/*
		//echo "LOLOLOLOL";
		//var_dump($this->db->last_query());
		$resultarray = [];
		$journals = [];
		$journalids = [];
		
		foreach ($q->result() as $i):
			$journals[$i->journal_no] = $i;
			
			// Filter so that those who still do not have configs but is enabled in master table will be selected
			//var_dump($i->nonp_enabled );
			//if ($emptyAllowed && ($i->nonp_enabled == '1')) array_push($resultarray, $i);
			//else 
			if ($emptyAllowed) array_push($resultarray, $i);
			array_push($journalids, $i->journal_no);
			
		endforeach;
			
		if ($emptyAllowed) {
			return $resultarray;
		} else {
			if (sizeOf($journalids) < 1) return [];
			//var_dump($journals);
			/* Get all existing configs for the journals *
			$this->db->select('DISTINCT ON (journal_no) journal_no, config_no,col_header,col_width,uom_id,type,col_order', false);
			$this->db->from('ilyas_config');
			$this->db->where_in('journal_no', $journalids);
			
			if (!is_null($perPage)) {
				$this->db->offset($offset);
				$this->db->limit($perPage);
			}
			
			//$this->db->order_by('col_order', 'asc');
			$q2 = $this->db->get();
			//var_dump($this->db->last_query());
			if (!$q2) return [];
			
			foreach ($q2->result() as $j):
				array_push($resultarray, $journals[$j->journal_no]);
			endforeach;
		}
		//var_dump($this->db->last_query());
		
		// Filter so that only those who have configs will be selected	
		
		
		
		
		//var_dump($resultarray);
		return $resultarray;*/
	}

    /* Get all pending non-progressive journals for the current user */
    function get_journals_nonp_pending($data, $offset = 0, $perPage, $userid, $emptyAllowed = false, $roleid)
    {
        /*query to select record with no entry in table ilyas */
        $query1 = "SELECT a.project_no,b.journal_no,a.project_name,b.journal_name,a.start_date,a.end_date,b.reminder_frequency,e.user_full_name as data_entry,e.user_id
                    from project_template a join journal_master_nonprogressive b on a.project_no = b.project_no join ilyas_config c on b.journal_no = c.journal_no
                    join journal_validator_nonprogressive d on b.journal_no=d.journal_no join journal_data_user_nonprogressive f on b.journal_no=f.journal_no join sec_user e on f.data_user_id = e.user_id
                    where c.config_no not in (SELECT config_no from ilyas)";
        if($userid!="1" && $roleid!="1")
        {
            $query1 .=" and d.validate_user_id = $userid group by b.journal_no, a.project_no,e.user_full_name,e.user_id";
        } else {
            $query1 .=" group by b.journal_no, a.project_no,e.user_full_name,e.user_id";
        }
        $result1 = $this->db->query($query1)->result();
        /*query to select last updated time from table ilyas */
        $query2 = "SELECT max(timestamp), jmnp.reminder_frequency, jmnp.journal_no FROM ilyas_config ic, ilyas i, journal_master_nonprogressive jmnp, journal_validator_nonprogressive jvnp
               WHERE ic.config_no = i.config_no and ic.journal_no = jmnp.journal_no and jmnp.journal_no = jvnp.journal_no";
        if($userid!="1" && $roleid!="1")
        {
            $query2 .=" and jvnp.validate_user_id = $userid GROUP BY ic.journal_no, jmnp.reminder_frequency, jmnp.journal_no";
        } else {
            $query2 .=" GROUP BY ic.journal_no, jmnp.reminder_frequency, jmnp.journal_no";
        }
        $result2 = $this->db->query($query2)->result();
        $result3 = array();
        foreach ($result2 as $row) {
            $last_revision_date = date_format(date_create($row->max), 'Y-m-d');
            $frequency = $row->reminder_frequency;
            $now = date('Y-m-d');
            $daylen = 60 * 60 * 24;
            $days_diff = (strtotime($now) - strtotime($last_revision_date)) / $daylen;
            if (($frequency == 'Weekly' && $days_diff > 7) || ($frequency == 'Monthly' && $days_diff > 30)) {
                $query3 = "SELECT a.project_no,b.journal_no,a.project_name,b.journal_name,a.start_date,a.end_date,b.reminder_frequency,e.user_full_name as data_entry,e.user_id
                    from project_template a join journal_master_nonprogressive b on a.project_no = b.project_no join ilyas_config c on b.journal_no = c.journal_no
                    join journal_validator_nonprogressive d on b.journal_no=d.journal_no join journal_data_user_nonprogressive f on b.journal_no=f.journal_no join sec_user e on f.data_user_id = e.user_id
                    where c.config_no in (SELECT config_no from ilyas) and b.reminder_frequency !='' ";
                if($userid!="1" && $roleid!="1")
                {
                    $query3 .=" and d.validate_user_id = $userid group by b.journal_no, a.project_no,e.user_full_name,e.user_id";
                } else {
                    $query3 .=" group by b.journal_no, a.project_no,e.user_full_name,e.user_id";
                }
                $result3 = $this->db->query($query3)->result();
            }
        }
        $result = array_merge($result1, $result3);
        return $result;
    }

	// This function gets all non progressive journals INCLUDING the ones that does not have configs yet.
	function get_journals_nonp_all($data,$offset=0,$perPage,$userid,$roleid) {
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		
		$isSort = ($data=="project_name asc" || $data=="project_name desc" || $data=="journal_name asc" || $data=="journal_name desc");
		$isSearch = (($data != "") && (!$isSort));
		$isOwner = ($userid != "1" && $roleid !="1");
		
		//$owner = "AND b.journal_no IN (SELECT journal_no FROM journal_data_user_nonprogressive WHERE data_user_id=$userid and default_owner_opt=1)";
		$owner = "AND b.journal_no IN (SELECT journal_no FROM journal_data_user_nonprogressive WHERE data_user_id=$userid)";
		$search = " AND (lower(a.project_name) like '%".$data."%' or lower(b.journal_name) like '%".$data."%' )";
		
		$query = "SELECT a.project_name,b.nonp_enabled,b.journal_name, b.project_no, b.reminder_frequency ,b.journal_no,e.user_full_name, e.user_id AS owner_user_id, jvn.validate_user_id, jdu.data_user_id FROM project_template a, journal_master_nonprogressive b, sec_user e, journal_validator_nonprogressive jvn, journal_data_user_nonprogressive jdu WHERE a.project_no=b.project_no AND b.user_id=e.user_id AND jvn.journal_no=b.journal_no AND jdu.journal_no=b.journal_no ".($isOwner ? $owner : "")." ".($isSearch ? $search : ""). " ";
		// Sorting function
		
		if($isSearch)
		{
			$query .= $search;
		} else if ($isSort) {
			$query.=" Order By ".$data;
		} else {
			$query.=" Order By project_name asc,journal_name asc";
		}
		
		
		if (!is_null($perPage)) {
			$query .= "";
		} else {
			$query .= "";
		}
		
		$q = $this->db->query($query);
		$result = $q->result();
		//var_dump($this->db->last_query());
		//var_dump($result);
		return $result;
	}
	
	function get_all_journals() {
    $query = "SELECT journal_no, journal_name FROM journal_master_nonprogressive";
    $q = $this->db->query($query);
    $result = $q->result();
    return $result;
}
	
	
	function get_journals_validate($data,$offset,$perPage,$userid,$roleid) {
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		if (!is_numeric($offset)) $offset = 0;
		if (!is_numeric($perPage)) $perPage = 10;
		
		// SELECT DISTINCT ON (a.journal_no) a.journal_no,  a.project_no, b.config_no, c.validate_user_id, d.user_full_name AS validator_user_full_name, e.data_user_id, e.user_full_name as data_user_full_name, f.timestamp FROM journal_master_nonprogressive a, ilyas_config b, journal_validator_nonprogressive c, sec_user d, (SELECT j.journal_no, j.data_user_id, k.user_full_name FROM journal_data_user_nonprogressive j, sec_user k) e, ilyas f WHERE d.user_id=c.validate_user_id AND c.journal_no=a.journal_no AND b.journal_no=a.journal_no AND a.journal_no=e.journal_no AND b.config_no=f.config_no AND b.validate_pending=1 AND b.validate_revision=f.revision
		$query = "SELECT DISTINCT ON (a.journal_no) a.journal_no, j.project_name, a.journal_name, a.project_no, b.config_no, c.validate_user_id, d.user_full_name AS validator_user_full_name, e.data_user_id, e.user_full_name as data_user_full_name, f.timestamp ";
		$query .= "FROM journal_master_nonprogressive a, ilyas_config b, journal_validator_nonprogressive c, sec_user d, (SELECT j.journal_no, j.data_user_id, k.user_full_name FROM journal_master_nonprogressive jmn, journal_data_user_nonprogressive j, sec_user k WHERE jmn.journal_no=j.journal_no AND j.data_user_id = k.user_id) e, ilyas f, project_template j ";
		$query .= "WHERE d.user_id=c.validate_user_id AND c.journal_no=a.journal_no AND b.journal_no=a.journal_no AND a.journal_no=e.journal_no AND b.config_no=f.config_no AND b.validate_pending=1 AND b.validate_revision=f.revision AND j.project_no=a.project_no ";
		
		if($userid!="1" && $roleid!="1")
		{
			$query .=" and validate_user_id=$userid";
		}
		//AND j.project_name = 'Power Supply And Distribution System'
		/*if($data!=""  && $data!="project_name asc" && $data!="project_name desc" && $data!="journal_name asc" && $data!="journal_name desc" && $data!="user_full_name desc" && $data!="user_full_name asc" && $data!="publish_date asc" && $data!="publish_date desc")
		{
			$query .=" and( lower(j.project_name) like '%".$data."%' ";
			$query .=" or lower(a.journal_name) like '%".$data."%' ";
			$query .=" or lower(e.user_full_name) like '%".$data."%' )";
		    //$query .=" or lower(f.frequency_detail_name) like '%".$data."%' )";

		}
		if($data=="project_name asc" || $data=="project_name desc" || $data=="journal_name asc" || $data=="journal_name desc" /*|| $data=="user_full_name desc" || $data=="user_full_name asc" || $data=="publish_date asc" || $data=="publish_date desc") {
		$query.=" Order By journal_no asc,".$data;
		} else {*/

		$query.=" Order By journal_no asc, project_name asc,journal_name asc";

       /* }*/
		/*$query .=" OFFSET ".$offset." LIMIT ".$perPage;*/
		$q = $this->db->query($query);
		//var_dump($q);
		//var_dump($this->db->last_query());
		return $q->result();
	}
	
	
	function update_journal_validator($jid,$data) {
		if (!is_numeric($jid)) return false;
		return $this->db->update('journal_validator_nonprogressive', $data, array('journal_no' => $jid));
	}
	
	// Detect changed user. If user id is not the same with the current, it will return 0, thus the user id will be changed.			
	function is_journal_user_change($jid,$data_user_id) {
		if (!is_numeric($jid)) return false;
		$is_changed_user = ($this->db->query("SELECT count(*) FROM journal_data_user_nonprogressive WHERE journal_no='$jid' AND data_user_id='$data_user_id'")->row()->count == 0);
		return $is_changed_user;
	}
	
	function update_journal_dataentry($jid,$data_user_id,$is_default_owner) {
		if (!is_numeric($jid)) return false;
		$data = array('data_user_id'=>$data_user_id,'default_owner_opt'=> $is_default_owner);
		
		$this->db->update('journal_data_user_nonprogressive', $data, array('journal_no' => $jid));
	}
	
	
	function check_update_journal_nonp($jid) {
		if (!is_numeric($jid)) return false;
		$query=$this->db->query("select journal_name from journal_master_nonprogressive where journal_no='$jid'");
		return $query->num_rows();
	}
	
	function set_validationlock($jid, $status, $revision = FALSE) {
		$data = array("validate_pending" => $status);
		if ($revision) {
			$data["validate_revision"] = $revision;
		}
		return $this->db->update('ilyas_config', $data, array('journal_no' => $jid));
	}
	
	function get_validationlock($jid) {
		$this->db->select('DISTINCT ON(validate_pending) validate_pending',false);
		$this->db->from('ilyas_config');
		$this->db->where('journal_no', $jid);
		$this->db->where('validate_pending', '1');
		$q = $this->db->get()->num_rows();
		
		return $q;
	}
	
	function get_read_only_rows($jid){
		$revision = $this->get_latest_revision($jid);
		if(!is_null($revision)){
			$read_only = array();
			$query = "SELECT DISTINCT ON(i.row) i.row,r_only FROM ilyas i, ilyas_config ic WHERE i.config_no = ic.config_no AND ic.journal_no = $jid AND i.revision = $revision";
			$results = $this->db->query($query);
			$results = $results->result();
			
			
			foreach($results as $r){
				$read_only[] = (int) $r->r_only;
			}
		}
		$read_only[] = 0; //for extra row
		return $read_only;
	}	
	
	function validate_approve($jid,$userid) {
		$j = $this->get_config($jid, true);
		$pending = $j[0]["validate_pending"];
		$revision = $j[0]["validate_revision"];
		$q = FALSE;
		if (($pending != "") && ($revision != "")) {
			$data = array("validate_pending" => 0, "validate_revision" => NULL);
			
			$this->db->trans_start();
			// Remove pending and revision from ilyas_config 
			$this->db->update('ilyas_config', $data, array('journal_no' => $jid));
			
			$filter = function($v){ return $v['config_no']; };
			$configids = array_map($filter, $j);
			//var_dump($configids);
			// Update validate status at ilyas
			$this->db->where_in("config_no", $configids);
			$this->db->where_in("revision", $revision);
//			$this->db->update('ilyas', array("validate_status" => 2));
            // modified by agaile to add validator id : on 02/06/2016
			$this->db->update('ilyas', array("validate_status" => 2,"validator_id" => $userid));

			$q = $this->db->trans_complete();
			
			//Copy approved data into log
            //modified by agaile : 02/06/2016
//			$query = "INSERT INTO ilyas_log (SELECT *,$jid FROM ilyas where config_no in (SELECT config_no FROM ilyas_config WHERE journal_no = $jid) and revision = $revision)";
			$query = "INSERT INTO ilyas_log (SELECT config_no,row,value,timestamp,revision,validate_status,validate_comment,user_id,data_date,validate_comment_row,r_only,$jid,validator_id FROM ilyas where config_no in (SELECT config_no FROM ilyas_config WHERE journal_no = $jid) and revision = $revision)";
            $this->db->query($query);
		}
		return $q;
	}
	
	/* Old function to reject to each cell's comment */
	function validate_reject($jid, $comments) {
		
		$j = $this->get_config($jid, true);
		$pending = $j[0]["validate_pending"];
		$revision = $j[0]["validate_revision"];
		$q = FALSE;
		//var_dump($pending);
		//var_dump($revision);
		if (($pending != "") && ($revision != "")) {
			$this->db->trans_start();
			/*$comarray = [];
			for ($i = 0; $i < sizeOf($j); $i++) {
				if (!isset($comments[$i]) || (sizeOf($comments[$i]) < 1)) continue;
				array_push($comarray, array("config_no" => $j[$i]['config_no'], "comments" => $comments[$i]));
			}*/
			if ($comments) {
				var_dump($comments);
				foreach ($comments as $crow):
					$this->db->update('ilyas', array("validate_comment" => $crow['comment']), array("config_no" => $crow['config_no'], "revision" => $revision, "row" => $crow['row']));
				endforeach;
			}
			//var_dump($this->db->last_query());
			
			
			
			
			// Remove pending and revision from ilyas_config 
			/*
			$confignos = [];
			for ($i = 0; $i < sizeOf($j); $i++) {
				array_push($confignos, $j[$i]['config_no']);
			}
			
			$this->db->where_in('config_no',$confignos); 
			$this->db->where('revision', $revision);
			$this->db->update('ilyas', $data);*/
			//var_dump($this->db->last_query());
			$this->db->update('ilyas_config', array("validate_pending" => 3, "validate_revision" => null), array('journal_no' => $jid));
			
			$filter = function($v){ return $v['config_no']; };
			$configids = array_map($filter, $j);
			//var_dump($configids);
			// Update validate status at ilyas
			$this->db->where_in("config_no", $configids);
			$this->db->where_in("revision", $revision);
			$this->db->update('ilyas', array("validate_status" => 3));
			//var_dump($this->db->last_query());
			$q = $this->db->trans_complete();
		}
		return $q;
	}
	
	
	/* New function to comment on rows */
	function validate_reject_row($jid, $comments) {
		
		$j = $this->get_config($jid, true);
		$pending = $j[0]["validate_pending"];
		$revision = $j[0]["validate_revision"];
		$q = FALSE;
		//var_dump($pending);
		//var_dump($revision);
		if (($pending != "") && ($revision != "")) {
			$this->db->trans_start();
			
			if ($comments) {
				//var_dump($comments);
				foreach ($comments as $idx=>$comment):
					$query = "update ilyas set validate_comment_row = '".$comment."' where revision = $revision and row = $idx and config_no in (select config_no from ilyas_config where journal_no = $jid)";
					$this->db->query($query);
					//$this->db->update('ilyas', array("validate_comment_row" => $comment), array("revision" => $revision, "row" => $idx));
				endforeach;
			}
			$this->db->update('ilyas_config', array("validate_pending" => 3, "validate_revision" => null), array('journal_no' => $jid));
			
			$filter = function($v){ return $v['config_no']; };
			$configids = array_map($filter, $j);
			//var_dump($configids);
			// Update validate status at ilyas
			$this->db->where_in("config_no", $configids);
			$this->db->where_in("revision", $revision);
			$this->db->update('ilyas', array("validate_status" => 3));
			//var_dump($this->db->last_query());
			$q = $this->db->trans_complete();
		}
		return $q;
	}
	
	function check_awaiting_approval($jid){
		$query = "select validate_pending from ilyas_config where journal_no = $jid limit 1";
		$result = $this->db->query($query);
		$pending = $result->row();
		//var_dump($pending);
		
		// If there is no result, the journal does not have a config in ilyas_config.
		if (sizeOf($pending) > 0) {
			if((int)$pending->validate_pending != 1)
				return true;
			else
				return false;
		} else {
			return true;
		}
	}
	
	
	function get_lookup_summary() {
	$query = "SELECT DISTINCT ON (lk_code) lk_code,data_set_id FROM lookup_data";
		$q = $this->db->query($query);
		return $q->result();
	}
	
	function get_lookup_data() {
		$query = "SELECT a.data_set_id, a.lk_code, b.lk_data, b.lk_value FROM lookup_data a,lookup_data_detail b where a.data_set_id=b.data_set_id ORDER BY a.data_set_id";
		$q = $this->db->query($query);
		$resultarray = [];
		foreach ($q->result() as $l):
			$id = $l->lk_code;
			$data = [$l->lk_data, $l->lk_value];
			if (!isset($resultarray[$id])) $resultarray[$id] = ["meta" => ["id" => $l->data_set_id, "code" => $l->lk_code], "data" => []];
			array_push($resultarray[$id]["data"], $data);
		endforeach;
		//Append extra null option.
		foreach($resultarray as $k => $r){
			array_unshift($resultarray[$k]['data'],['','']);
			
		}
		return $resultarray;
	}
	
	function get_config_for_journal($jid) {
		$jid = str_replace("'", "", $jid);
		$query = "SELECT config_no,col_header from ilyas_config WHERE journal_no = '$jid' ORDER BY col_order";
		$q = $this->db->query($query);
		return $q->result();
	}
	function get_column_values_for_journal($jid, $config_no) {
		$jid = str_replace("'", "", $jid);
		$config_no = str_replace("'", "", $config_no);
		$rev = $this->get_latest_revision($jid);
		
		// Empty rev means empty table
		if ($rev == '')  {
			return [];
		}
		$query = "SELECT value FROM ilyas WHERE config_no = '$config_no' AND revision = '$rev' ORDER BY row";
		$q = $this->db->query($query);
		return $q->result();
	}
	
	function get_emails($jid) {
		if (!is_numeric($jid)) return false;
		$query = "SELECT jvn.validate_user_id AS validator_id, jvn.user_full_name AS validator_name, jvn.email_id AS validator_email, jdun.data_user_id AS data_id, jdun.user_full_name AS data_name, jdun.email_id AS data_email from (SELECT user_full_name, validate_user_id, email_id FROM sec_user s, journal_validator_nonprogressive j WHERE j.validate_user_id=s.user_id AND j.journal_no=$jid) jvn, (SELECT user_full_name, data_user_id, email_id FROM sec_user s, journal_data_user_nonprogressive j WHERE j.data_user_id=s.user_id AND j.journal_no=$jid) jdun";
		//print_r($query);
		return $this->db->query($query)->result();
	}
	/*
	function get_progressive_audit_count($data){
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="select count(1) from project_template a, journal_master b,journal_data_entry_master c,sec_user d,frequency_detail f where a.project_no=b.project_no and c.publish_user_id=d.user_id and f.frequency_detail_no=c.frequency_detail_no and c.journal_no=b.journal_no";
		// $query ="select * from project_template pt, journal_master_nonprogressive jmn, journal_data_user_nonprogressive jdun, sec_user su where pt.project_no = jmn.project_no and jmn.journal_no = jdun.journal_no and jdun.data_user_id = su.user_id and jmn.journal_no in (SELECT distinct(journal_no) FROM ilyas_log)";
		if($data!="")
		{
			$query .=" and( lower(a.project_name) like '%".$data."%' ";
			$query .=" or lower(b.journal_name) like '%".$data."%' ";
			$query .=" or lower(d.user_full_name) like '%".$data."%' ";
			$query .=" or lower(f.frequency_detail_name) like '%".$data."%' )";

		}
		//$query .=" Order By project_name asc,journal_name asc";
		$q = $this->db->query($query);
		$aaaa = $q->result();
		// echo "<script type='text/javascript'>alert('".var_dump($aaaa)."')</script>";
		return $q->result()[0]->count;
	}*/
	
	function get_audit($search,$offset,$perpage,$limit = null)
	{
		$search=strtolower($search);
		$search=str_replace("'","''",$search);
		if ($limit != null)
			$actual_limit = min($perpage,$limit);
		else 
			$actual_limit = $perpage;
		$query ="select * from (select DISTINCT ON (b.journal_no) a.project_name, b.journal_no, b.journal_name, d.user_full_name from project_template a, journal_master_nonprogressive b, journal_nonprogressive_data_entry_audit_log c, sec_user d WHERE a.project_no = b.project_no AND b.journal_no = c.journal_no AND c.user_id = d.user_id";
		// $query ="select * from project_template pt, journal_master_nonprogressive jmn, journal_data_user_nonprogressive jdun, sec_user su where pt.project_no = jmn.project_no and jmn.journal_no = jdun.journal_no and jdun.data_user_id = su.user_id and jmn.journal_no in (SELECT distinct(journal_no) FROM ilyas_log)";
		if($search!="")
		{
			$query .=" and( lower(a.project_name) like '%".$search."%' ";
			$query .=" or lower(b.journal_name) like '%".$search."%' ";
			$query .=" or lower(d.user_full_name) like '%".$search."%') ";
		}
//		$query .=" Order By b.journal_no, project_name asc,journal_name asc OFFSET ".$offset."LIMIT ".$perpage.") as temp order by project_name asc,journal_name asc";
        // modified by agaile on 02/06/2016 reason the query is wrong since it was given the offset and limit it wont take the whole records
		$query .=" Order By b.journal_no, project_name asc,journal_name asc ) as temp order by project_name asc,journal_name asc";
//        print_r($query);
        $q = $this->db->query($query);
		$aaaa = $q->result();
		// echo "<script type='text/javascript'>alert('".var_dump($aaaa)."')</script>";
//        echo '<pre>';
//        print_r($aaaa);
//        echo '</pre>';
		return $q->result();
	}

    // added by agaile to fetch all the records tackle the bal issue

    function get_audit_newz(){
        $query ="select * from (select DISTINCT ON (b.journal_no) a.project_name, b.journal_no, b.journal_name, d.user_full_name from project_template a, journal_master_nonprogressive b, journal_nonprogressive_data_entry_audit_log c, sec_user d WHERE a.project_no = b.project_no AND b.journal_no = c.journal_no AND c.user_id = d.user_id Order By b.journal_no, project_name asc,journal_name asc ) as temp order by project_name asc,journal_name asc";
        $q = $this->db->query($query);
        return $q->result();
    }

    // added by agaile to segregate the records based on usr roles ; 04/06/2016
        function get_audit_id_audit($userid){

            $query1 ="select * from (select DISTINCT ON (b.journal_no) a.project_name, b.journal_no, b.journal_name, d.user_full_name from project_template a, journal_master_nonprogressive b, journal_nonprogressive_data_entry_audit_log c, sec_user d WHERE a.project_no = b.project_no AND b.journal_no = c.journal_no AND c.user_id = d.user_id Order By b.journal_no, project_name asc,journal_name asc ) as temp order by project_name asc,journal_name asc";
            $q1 = $this->db->query($query1);
            $rslt1 = $q1->result();

            $query2 ="SELECT * FROM journal_validator_nonprogressive WHERE validate_user_id = $userid";
            $q2 = $this->db->query($query2);
            $rslt2 = $q2->result();

            $final = [];
            foreach ($rslt1 as $value1) {
                foreach ($rslt2 as $value2) {
                    if($value1->journal_no == $value2->journal_no){
                        array_push ($final, $value1);
                    }
                }
            }
            return $final;
        }

	
	function total_audit($search) {
		$search=strtolower($search);
		$search=str_replace("'","''",$search);
		
		$query ="select count(*) from (select DISTINCT (b.journal_no) FROM journal_nonprogressive_data_entry_audit_log b group by b.journal_no";
		// $query ="select * from project_template pt, journal_master_nonprogressive jmn, journal_data_user_nonprogressive jdun, sec_user su where pt.project_no = jmn.project_no and jmn.journal_no = jdun.journal_no and jdun.data_user_id = su.user_id and jmn.journal_no in (SELECT distinct(journal_no) FROM ilyas_log)";
		if($search!="")
		{
			$query .=" WHERE ( lower(a.project_name) like '%".$search."%' ";
			$query .=" or lower(b.journal_name) like '%".$search."%' ";
			$query .=" or lower(d.user_full_name) like '%".$search."%') ";
		}
		$query .= ") as temp";
		//$query .=" Order By b.journal_no, project_name asc,journal_name asc";
		$q = $this->db->query($query);
		$aaaa = $q->result();
		// echo "<script type='text/javascript'>alert('".var_dump($aaaa)."')</script>";
		return $q->result()[0]->count;
	}
	
	function get_audit_revisions($jid) {
		$jid = str_replace("'","",$jid);
		$query = "SELECT DISTINCT b.revision, b.timestamp, c.user_full_name FROM journal_nonprogressive_data_entry_audit_log b, sec_user c WHERE b.journal_no = '$jid' AND b.user_id=c.user_id ORDER BY revision desc";
		$q = $this->db->query($query);
		return $q->result();
	}
	
	function get_audit_data($jid, $rev = '') {
		$jid = str_replace("'","",$jid);
		if ($rev == '') {
			$revision = $this->get_latest_revision($jid);
		} else {
			$revision = str_replace("'", "", $rev);
		}
		//var_dump(is_null($revision));
		
		if (is_null($revision)) return [];
		
		$query = "SELECT b.log FROM journal_nonprogressive_data_entry_audit_log b WHERE b.journal_no = '$jid' AND b.revision='$revision' ORDER BY revision desc";
        //echo $query;
		$q = $this->db->query($query);
		$result = json_decode($q->result()[0]->log, true);
		$result['data'] = $this->transpose($result['data']);
		return $result;
		
	}
	/*
	function get_audit_log($jid, $rev) {
		$jid = str_replace("'","",$jid);
		$rev = str_replace("'","",$rev);
		$query = "SELECT b.* FROM ilyas_config a, ilyas b WHERE a.journal_no='$jid' AND b.config_no = a.config_no AND b.revision = '$rev'";
		$q = $this->db->query($query);
		return $q->result();
	}*/
	
	function get_user_id($jid, $rev) {
		$jid = str_replace("'","",$jid);
		$rev = str_replace("'","",$rev);
		$query = "SELECT distinct b.user_id, c.user_full_name FROM ilyas_config a, ilyas b, sec_user c WHERE a.journal_no='$jid' AND b.config_no = a.config_no AND b.user_id = c.user_id AND b.revision = '$rev' LIMIT 1";
		$q = $this->db->query($query);
		return $q->result();
	}
	
	function get_user_email($user_id) {
		$user_id = str_replace("'","",$user_id);
		$query = "SELECT user_full_name,email_id FROM sec_user WHERE user_id='$user_id'";
		$q = $this->db->query($query);
		return $q->result();
	}

    /*function to get validator email and full_name. done by jane*/
	function get_validator_email($user_id) {
		$query = "SELECT user_full_name,email_id FROM sec_user WHERE user_id='$user_id'";
		$q = $this->db->query($query);
		return $q->result();
	}
	
	/* Below is misc. helper functions */
	
	function get_confignos($j) {
		$filter = function($v){ return $v->config_no; };
		return array_map($filter, $j);
	}
	
	
	function escape_value(& $value)
    {
		//var_dump($value);
        if( is_string($value) )
        {
            $value = "'" . pg_escape_string($value) . "'";
        }  
		if (is_null($value)) {
			//var_dump($value); die();
			$value = NULL;
		}
    }

	/* The following function is from http://stackoverflow.com/questions/797251/transposing-multidimensional-arrays-in-php */
	function transpose($arr) {
		$out = array();
		foreach ($arr as $key => $subarr) {
			foreach ($subarr as $subkey => $subvalue) {
				$out[$subkey][$key] = $subvalue;
			}
		}
		return $out;
	}

    /*function to get validator id - journal non progressive*/
    function get_validator_nonp($jid) {
        $query = "SELECT validate_user_id FROM journal_validator_nonprogressive where journal_no = '$jid'";
        $q = $this->db->query($query);
        return $q->row_array();
    }
}
?>