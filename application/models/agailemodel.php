<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

Class AgaileModel extends CI_Model
{
    /* function to get the count for number of rows in pagination*/

    function get_count($data,$offset,$perPage, $userid, $roleid)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);

        $query = "SELECT DISTINCT ON (a.journal_no) a.journal_no, j.project_name, a.journal_name, a.project_no, b.config_no, c.validate_user_id, d.user_full_name AS validator_user_full_name, e.data_user_id, e.user_full_name as data_user_full_name, f.timestamp ";
        $query .= "FROM journal_master_nonprogressive a, ilyas_config b, journal_validator_nonprogressive c, sec_user d, (SELECT j.journal_no, j.data_user_id, k.user_full_name FROM journal_master_nonprogressive jmn, journal_data_user_nonprogressive j, sec_user k WHERE jmn.journal_no=j.journal_no AND j.data_user_id = k.user_id) e, ilyas f, project_template j ";
        $query .= "WHERE d.user_id=c.validate_user_id AND c.journal_no=a.journal_no AND b.journal_no=a.journal_no AND a.journal_no=e.journal_no AND b.config_no=f.config_no AND b.validate_pending=1 AND b.validate_revision=f.revision AND j.project_no=a.project_no ";

        if($userid!="1" && $roleid!="1")
        {
            $query .=" and validate_user_id=$userid";
        }

        if($data!=""  && $data!="project_name asc" && $data!="project_name desc" && $data!="journal_name asc" && $data!="journal_name desc" && $data!="user_full_name desc" && $data!="user_full_name asc" && $data!="publish_date asc" && $data!="publish_date desc")
        {
            $query .=" and( lower(j.project_name) like '%".$data."%' ";
            $query .=" or lower(a.journal_name) like '%".$data."%' ";
            $query .=" or lower(e.user_full_name) like '%".$data."%' )";
            //$query .=" or lower(f.frequency_detail_name) like '%".$data."%' )";

        }
        if($data=="project_name asc" || $data=="project_name desc" || $data=="journal_name asc" || $data=="journal_name desc" /*|| $data=="user_full_name desc" || $data=="user_full_name asc" || $data=="publish_date asc" || $data=="publish_date desc"*/) {
            $query.=" Order By journal_no asc,".$data;
        } else {
            $query.=" Order By journal_no asc, project_name asc,journal_name asc";
        }
        $query .=" OFFSET ".$offset." LIMIT ".$perPage;
        $q = $this->db->query($query);
        return $q->num_rows();
    }

    function delete_image_for_replace($picid,$datenno){
        $this->db->where('data_entry_pict_no', $picid);
        $this->db->where('data_entry_no', $datenno);
        return $this->db->delete('journal_data_entry_picture');
    }

    function fetch_dataentry_no($journal_no)
    {
        $query = "select * from journal_data_entry_master where journal_no=$journal_no";
        $data_entry_no = $this->db->query($query)->result();
        $deno = $data_entry_no[0]->data_entry_no;
        $query2 = "select * from journal_data_entry_audit_log where data_entry_no=$deno";
        $q = $this->db->query($query2);
        return $q->num_rows();

    }
}

?>