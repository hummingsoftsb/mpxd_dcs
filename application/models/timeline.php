<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Timeline extends CI_Model {

// Function to fetch total number of records
    function totalprojstat($data) {
        $data = strtolower($data);
        $data = str_replace("'", "''", $data);
        //$query ="select a.*,count(b.journal_no) as cnt from project_template a , journal_master b where a.project_no=b.project_no";
        $query = "(select d.*,count(a.complete_percent) as cper,avg(a.complete_percent) as cavg,count(c.journal_no) as cnt from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no";
        if ($data == "completed") {
            
        } else if ($data == "pending") {
            
        } else {
            $query .=" and lower(d.project_name) like '%" . $data . "%' ";
        }
        $query .=" group by d.project_no";
        if ($data == "completed") {
            $query.=" having avg(a.complete_percent)>=100";
        }
        if ($data == "pending") {
            $query.=" having avg(a.complete_percent)<100";
        }
        $query.=") UNION (select d.*,count(a.complete_percent) as cper,avg(a.complete_percent) as cavg,count(c.journal_no) as cnt from data_entry_status a,journal_data_entry_master_nonprogressive b,journal_master_nonprogressive c,project_template d where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no";
        if ($data == "completed") {
            
        } else if ($data == "pending") {
            
        } else {
            $query .=" and lower(d.project_name) like '%" . $data . "%' ";
        }
        $query .=" group by d.project_no";
        if ($data == "completed") {
            $query.=" having avg(a.complete_percent)>=100";
        }
        if ($data == "pending") {
            $query.=" having avg(a.complete_percent)<100";
        }
        $query.=") ";
        $query = $this->db->query($query);
        return $query->num_rows();
    }

    // Function To Fetch All Project Record
    function show_projstat($data, $offset, $perPage) {
        $data = strtolower($data);
        $data = str_replace("'", "''", $data);
        /* $query ="select a.*,count(b.journal_no) as cnt from project_template a , journal_master b where a.project_no=b.project_no";
          if($data!="") {
          $query .=" and lower(a.project_name) like '%".$data."%' ";
          } */
        $query = "(select d.*,count(a.complete_percent) as cper,avg(a.complete_percent) as cavg,count(c.journal_no) as cnt from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no";
        if ($data == "completed") {
            
        } else if ($data == "pending") {
            
        } else {
            $query .=" and lower(d.project_name) like '%" . $data . "%' ";
        }
        $query .=" group by d.project_no";
        if ($data == "completed") {
            $query.=" having avg(a.complete_percent)>=100";
        }
        if ($data == "pending") {
            $query.=" having avg(a.complete_percent)<100";
        }
        $query.=") UNION (select d.*,count(a.complete_percent) as cper,avg(a.complete_percent) as cavg,count(c.journal_no) as cnt from data_entry_status a,journal_data_entry_master_nonprogressive b,journal_master_nonprogressive c,project_template d where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no";
        if ($data == "completed") {
            
        } else if ($data == "pending") {
            
        } else {
            $query .=" and lower(d.project_name) like '%" . $data . "%' ";
        }
        $query .=" group by d.project_no";
        if ($data == "completed") {
            $query.=" having avg(a.complete_percent)>=100";
        }
        if ($data == "pending") {
            $query.=" having avg(a.complete_percent)<100";
        }
        $query .=") OFFSET " . $offset . "LIMIT " . $perPage;
        $q = $this->db->query($query);
        return $q->result();
    }

// Function To calculate percentage completed project wise
    function show_projper($data) {
        $data = strtolower($data);
        $query = "select count(a.complete_percent) as cper,avg(a.complete_percent) as cavg from data_entry_status a,journal_data_entry_master b,journal_master c where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no='" . $data . "' group by c.project_no";
        $q = $this->db->query($query);
        return $q->result();
    }

    // Function to fetch total number of  journal status records
    function totaljourstat($data) {
        $data = strtolower($data);
        $data = str_replace("'", "''", $data);
        //$query = "(select a.complete_percent,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d,frequency_detail e where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no";
        $query = "(select a.complete_percent,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name,f.user_full_name,f.user_full_name from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d,frequency_detail e,sec_user f, journal_data_user g where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no and g.journal_no = c.journal_no and f.user_id = g.data_user_id";
        if ($data == "completed") {
            $query .=" and a.complete_percent=100.00";
        } else if ($data == "pending") {
            $query .=" and a.complete_percent!=100.00";
        } else {
            $query .=" and ( lower(c.journal_name) like '%" . $data . "%' ";
            $query .=" or lower(d.project_name) like '%" . $data . "%' ";
            $query .=" or lower(e.frequency_detail_name) like '%" . $data . "%' ";
			$query .=" or lower(f.user_full_name) like '%" . $data . "%' )";
        }
		/*
        $query .=") UNION (select a.complete_percent,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name from data_entry_status a,journal_data_entry_master_nonprogressive b,journal_master_nonprogressive c,project_template d,frequency_detail e where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no";
        if ($data == "completed") {
            $query .=" and a.complete_percent=100.00";
        } else if ($data == "pending") {
            $query .=" and a.complete_percent!=100.00";
        } else {
            $query .=" and ( lower(c.journal_name) like '%" . $data . "%' ";
            $query .=" or lower(d.project_name) like '%" . $data . "%' ";
            $query .=" or lower(e.frequency_detail_name) like '%" . $data . "%' )";
        }*/
        $query .=")";
        $query .=" order by project_name, journal_name,frequency_detail_name asc";
        $query = $this->db->query($query);
        return $query->num_rows();
    }

    // Function To Fetch All Journal Record
    function show_jourstat($data, $offset, $perPage) {
        $data = strtolower($data);
        $data = str_replace("'", "''", $data);
        //$query = "(select a.complete_percent,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d,frequency_detail e where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no";
        $query = "(select a.complete_percent,a.data_entry_status_desc,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name,f.user_full_name,f.user_full_name from data_entry_status a,journal_data_entry_master b,journal_master c,project_template d,frequency_detail e,sec_user f, journal_data_user g where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no and g.journal_no = c.journal_no and f.user_id = g.data_user_id";
        if ($data == "completed") {
            $query .=" and a.complete_percent=100.00";
        } else if ($data == "pending") {
            $query .=" and a.complete_percent!=100.00";
        } else {
            $query .=" and ( lower(c.journal_name) like '%" . $data . "%' ";
            $query .=" or lower(d.project_name) like '%" . $data . "%' ";
            $query .=" or lower(e.frequency_detail_name) like '%" . $data . "%' ";
            $query .=" or lower(f.user_full_name) like '%" . $data . "%' )";
        }
		/*
        $query .=") UNION (select a.complete_percent,c.journal_name,c.start_date,c.end_date,d.project_name,e.frequency_detail_name from data_entry_status a,journal_data_entry_master_nonprogressive b,journal_master_nonprogressive c,project_template d,frequency_detail e where a.data_entry_status_id=b.data_entry_status_id and c.journal_no=b.journal_no and c.project_no=d.project_no and b.frequency_detail_no=e.frequency_detail_no";
        if ($data == "completed") {
            $query .=" and a.complete_percent=100.00";
        } else if ($data == "pending") {
            $query .=" and a.complete_percent!=100.00";
        } else {
            $query .=" and ( lower(c.journal_name) like '%" . $data . "%' ";
            $query .=" or lower(d.project_name) like '%" . $data . "%' ";
            $query .=" or lower(e.frequency_detail_name) like '%" . $data . "%' )";
        }*/
        $query .=")";
        $query .=" order by project_name, journal_name, frequency_detail_name asc OFFSET " . $offset . "LIMIT " . $perPage;
        $q = $this->db->query($query);
        return $q->result();
    }

}
?>