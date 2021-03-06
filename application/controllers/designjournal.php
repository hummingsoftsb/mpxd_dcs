<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Designjournal extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('design','',TRUE);
        $this->load->model('assessment','',TRUE);
        $this->load->model('securitys','',TRUE);
        $this->load->model('admin','',TRUE);
        $this->load->model('alertreminder','',TRUE);
        $this->load->model('agailemodel','',TRUE);
        $this->load->model('reminder','',TRUE);
        $this->load->model('ilyasmodel');
        $this->load->library('swiftmailer');
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
            /*$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);*/
            $data1['alertcount']=count($data1['alerts']);
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
//        echo '<pre>';
//        print_r($_POST);
//        echo '<pre>';
//        exit;
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
       // $this->form_validation->set_rules('j_type', '$label5', 'trim|numeric_dash|xss_clean');
        $this->form_validation->set_rules('albumname', '$label5', 'trim|xss_clean');

        $journal_type = $this->input->post('j_type');

        // Commented by Agaile on 26/11/2015

        // if($journal_type == 2) {$is_image = 1;} else {$is_image = 0;} //Check journal type. Return 1 if image type;

        // AGAILE : START
        //Modification done by Agaile for taking both data entry and image 26/11/2015
        if (!empty($journal_type)) {
            $count = sizeof($journal_type);
            if ($count > 1) {
//                echo('inside_both');
//                exit;
                $is_image = 2; // means both
            } else {
                // either data entry or image find it (foreach)
                foreach ($journal_type as $jrtype) {
                    if ($jrtype == 1) {
                        $is_image = 0; // means data entry
                    } else {
                        $is_image = 1; // means image entry
                    }
                }
            }
        }
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

                /*call reminder update function*/
                $this->update_reminder();
                /*$reminders_controller = new Reminders();
                $reminders_controller->update();*/

                $sess_array = array('message' => $this->securitys->get_label_object(7)." Added Successfully","type" => 1);
                $this->session->set_userdata('message', $sess_array);
                echo json_encode(array('st'=>1, 'msg' => 'Success','msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>''));
            }
            else
            {
                echo json_encode(array('st'=>0, 'msg' => $label1." already exist",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>'','msg7'=>'','msg8'=>'','msg9'=>'',));
            }
        }
        // AGAILE : END
    }

    /*Modified by jane*/
    function update()
    {
        $label = $this->securitys->get_label_object_name(77);
        $label1 = $this->securitys->get_label_object_name(78);
        $label2 = $this->securitys->get_label_object_name(80);
        $label3 = $this->securitys->get_label_object_name(81);
        $label4 = $this->securitys->get_label_object_name(82);
        $label5 = $this->securitys->get_label_object_name(41);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('projectname1', $label, 'trim|required|xss_clean');
        $this->form_validation->set_rules('journalname1', $label1, 'trim|required|alpha_numeric_spaces_special|xss_clean');
        $this->form_validation->set_rules('journalproperty1', $label1, 'trim|alpha_numeric_spaces_special|xss_clean');
        $this->form_validation->set_rules('albumname1', $label1, 'trim|xss_clean');
        $this->form_validation->set_rules('user1', $label2, 'trim|required|xss_clean');
        $this->form_validation->set_rules('frequency1', $label3, 'trim|required|xss_clean');
        $this->form_validation->set_rules('startdate1', $label5, 'trim|required|xss_clean');

        $attbcount = $this->input->post('dataattbcount1');
        $dataattberror = '';
        $attbselect = 0;
        $dataentryid = $this->input->post('dataentryid1');
        $dataentryids = explode(',', $dataentryid);
        $dataerror = '';
        $datamsg = 'Select at least one data owner ';
        for ($j = 0; $j < count($dataentryids); $j++) {
            $dataentryowner = $this->input->post('dataentryowner1');
            if ($dataentryowner == $dataentryids[$j]) {
                $dataerror = "yes";
                $datamsg = '';
            }
        }
        for ($i = 1; $i <= $attbcount - 1; $i++) {
            //$chk='1dataattb'.$i;
            $start = '1start' . $i;
            $end = '1end' . $i;
            $week = '1week' . $i;
            $order = '1order' . $i;
            //$attbid='1dataattbid'.$i;
            //var_dump($attbid);
            /*if($this->input->post($chk)=="on")
            {*/

            if ($this->input->post($start) == '' /*|| !ctype_digit($this->input->post($start))*/) {
                $dataattberror = "Start value is required.";
            }
            if ($this->input->post($end) == '' /*|| !ctype_digit($this->input->post($end))*/) {
                $dataattberror = "End value is required.";
            }
            if ($this->input->post($week) == '' /*|| !ctype_digit($this->input->post($week))*/) {
                $dataattberror = "Weekly Max value is required.";
            }
            if ($this->input->post($order) == '' || !ctype_digit($this->input->post($order))) {
                //$dataattberror="Enter Data Attribute Details or Invalid Input 4 asd ".$attbcount.' dsa';
                $dataattberror = "Attribute Order is required.";
            }
            //$attbselect=1;
            //}
        }
        /*if($attbselect==0)
        {
            $dataattberror="Enter atleast one Data Attribute";
        }*/
        if ($this->form_validation->run() == FALSE || $dataattberror != '' || $dataerror == '') {
            echo json_encode(array('st' => 0, 'msg' => form_error('projectname1'), 'msg1' => form_error('journalname1'), 'msg2' => form_error('user1'), 'msg3' => form_error('frequency1'), 'msg4' => $dataattberror, 'msg5' => $datamsg, 'msg6' => form_error('journalproperty1')));
        } else {
            $name = $this->input->post('journalname1');
            $property = $this->input->post('journalproperty1');
            $projectno = $this->input->post('projectname1');
            $journalid = $this->input->post('editjournalno');
            $dependency = $this->input->post('dependency');
            $albumname = $this->input->post('albumname1');
            $startdate = date("Y-m-d", strtotime($this->input->post('startdate1')));
            // Added start date in array for updation -Agaile 24/11/2015

            if (json_decode($dependency) && (json_last_error() != JSON_ERROR_NONE)) {
                echo "JSON ERROR IN DEPENDENCY!";
                die();
            }

            if ($this->design->update_check_journal($name, $projectno, $journalid) == 0) {
                $data = array('project_no' => $projectno, 'journal_name' => $name, 'journal_property' => $property, 'user_id' => $this->input->post('user1'), 'start_date' => $startdate, 'frequency_no' => $this->input->post('frequency1'), 'dependency' => $dependency, 'album_name' => $albumname);

                //query the database
                $this->design->update_journal($journalid, $data);

                /*for getting previous journal validators. modified by jane*/
                $previous_validator_ids = array();
                $ids = $this->design->get_previous_journal_validator($journalid);
                foreach ($ids as $row):
                    $previous_validator_ids[] = $row->validate_user_id;
                endforeach;
                /*end*/

                $this->design->delete_journal_validator($journalid);
                //Validator
                $validator_data = array();
                $current_validator_ids = array();
                $validatorid = $this->input->post('validatorid1');
                $validatorids = explode(',', $validatorid);
                for ($j = 0; $j < count($validatorids); $j++) {
                    $validatoruser = '1validateuser' . $validatorids[$j];
                    $validatorlevel = '1level' . $validatorids[$j];
                    $validator_data[$this->input->post($validatoruser)] = $this->input->post($validatorlevel);
                    $validatordata = array('journal_no' => $journalid, 'validate_user_id' => $this->input->post($validatoruser), 'validate_level_no' => $this->input->post($validatorlevel));
                    $current_validator_ids[] = $this->input->post($validatoruser);

                    $this->design->add_journal_validator($validatordata);
                }
                /*for sending email notifications to the new journal validators. modified by jane*/
                $diff = $this->array_recursive_diff($current_validator_ids, $previous_validator_ids);
                foreach ($diff as $validator) {
                    if (!empty($validator)) {
                        $user = $this->ilyasmodel->get_user_email($validator);
                        $email = $user[0]->email_id;
                        $validator = $user[0]->user_full_name;
                        $this->swiftmailer->validation_assigned($email, $validator, $name, $journalid);
                    }
                }
                /*end*/

                $this->design->update_journal_data_entry_validator($journalid, $validator_data);

                // Update existing data entries to have new validators
                $previous_owner_id = $this->design->get_journal_data_entry_owner($journalid);
                $this->design->delete_journal_data_entry($journalid);
                //Data Entry
                $dataentryid = $this->input->post('dataentryid1');
                $dataentryids = explode(',', $dataentryid);
                //$this->design->get_journal_data_entry_owner($journalid);
                for ($j = 0; $j < count($dataentryids); $j++) {
                    $dataentryuser = '1dataentryuser' . $dataentryids[$j];
                    $dataentryowner = $this->input->post('dataentryowner1');
                    if ($dataentryowner == $dataentryids[$j]) {
                        $dataentrydata = array('journal_no' => $journalid, 'data_user_id' => $this->input->post($dataentryuser), 'default_owner_opt' => '1');
                        // Email to the new data entry user
                        if (($previous_owner_id) && ($this->input->post($dataentryuser) != $previous_owner_id)) {
                            $user = $this->ilyasmodel->get_user_email($this->input->post($dataentryuser))[0];
                            $email = $user->email_id;
                            $dename = $user->user_full_name;
                            $this->swiftmailer->data_entry_assigned($email, $dename, $name, $journalid);
                        }
                    } else {
                        $dataentrydata = array('journal_no' => $journalid, 'data_user_id' => $this->input->post($dataentryuser), 'default_owner_opt' => '0');
                    }
                    $this->design->add_journal_data_entry($dataentrydata);
                }

                // CREATE A MODEL QUERY FOR CURRENT DE, THEN COMPARE, THEN EMAIL IF NECESSARY

                // modified By: Sebin on 06-04-2016
                $checked=$this->input->post('dataupdatecj');
                $data_entry_no=$this->design->select_journal_entry_no($journalid);
                $this->design->delete_journal_detail($journalid);
                //Data Attribute
                $dataattbcount = $this->input->post('dataattbcount1');
                for ($j = 1; $j <= $dataattbcount; $j++) {
                    $chk = '1dataattb' . $j;
                    $start = '1start' . $j;
                    $end = '1end' . $j;
                    $week = '1week' . $j;
                    $order = '1order' . $j;
                    $attbid = '1dataattbid' . $j;
                    if ($this->input->post($chk) == "on") {
                        if (($this->input->post($attbid)) != FALSE) {
                            $dataattbdata = array('journal_no' => $journalid, 'data_attb_id' => $this->input->post($attbid), 'start_value' => $this->input->post($start), 'end_value' => $this->input->post($end), 'frequency_max_value' => $this->input->post($week), 'display_seq_no' => $this->input->post($order));
                            $this->design->update_journal_detail($journalid, $this->input->post($attbid), $this->input->post($start), $this->input->post($end), $this->input->post($week), $this->input->post($order));
                            //check box value is checked
                            if(!empty($checked)){
                                if($this->design->fn_validate_journal_data_entry_detail($data_entry_no[0]['data_entry_no'])>0) {
                                    //Modified By Sebin on 06-04-2016. Usage: if Attribute ID exists Update Else Insert.
                                    if ($this->design->count_data_attb_id($this->input->post($attbid), $data_entry_no[0]['data_entry_no']) > 0) {
                                        $this->design->update_journal_data_entry_detail($this->input->post($attbid), $this->input->post($start), $this->input->post($end), $this->input->post($week), $journalid, $data_entry_no[0]['data_entry_no']);
                                    } else {
                                        $this->design->add_journal_data_entry_detail($data_entry_no[0]['data_entry_no'], $dataattbdata);
                                    }
                                }
                            }
                        }
                    }
                }
                if(!empty($checked)){
                    if($this->design->fn_validate_journal_data_entry_detail($data_entry_no[0]['data_entry_no'])>0) {
                        $this->design->chk_att_id($journalid);
                        if($this->design->fn_journal_status($journalid)){
                            $this->design->fn_update_journal_status($journalid);
                            $arr_data_user_id=$this->design->select_journal_owner($journalid);
                            $this->design->fn_delete_other_alerts($data_entry_no[0]['data_entry_no']);
                            $data_alert = array('alert_date' => date("Y-m-d"),'alert_user_id' => $arr_data_user_id[0]['data_user_id'],'data_entry_no' => $data_entry_no[0]['data_entry_no'],'alert_message' => 'Data Entry Rejected Due to Journal Modification','alert_hide' => '0','email_send_option' => '1');
                            $this->assessment->add_user_alert($data_alert);
                            $obj_validator_list=$this->design->fn_alert_check_validator($data_entry_no[0]['data_entry_no']);
                            foreach($obj_validator_list as $val){
                                if($val->validate_status>=1){
                                    $data_alert = array('alert_date' => date("Y-m-d"),'alert_user_id' => $val->validate_user_id,'data_entry_no' => $data_entry_no[0]['data_entry_no'],'alert_message' => 'Data Entry Rejected Due to Journal Modification','alert_hide' => '0','email_send_option' => '1');
                                    $this->assessment->add_user_alert($data_alert);
                                }
                            }
                            $this->design->fn_update_journal_validate_status($data_entry_no[0]['data_entry_no']);
                        }
                    }
                }

                $sess_array = array('message' => $this->securitys->get_label_object(7) . " Updated Successfully", "type" => 1);
                $this->session->set_userdata('message', $sess_array);
                echo json_encode(array('st' => 1, 'msg' => 'Success', 'msg1' => '', 'msg2' => '', 'msg3' => '', 'msg4' => ''));
            } else {
                echo json_encode(array('st' => 0, 'msg' => $label1 . " already exist", 'msg1' => '', 'msg2' => '', 'msg3' => '', 'msg4' => '', 'msg5' => '', 'msg6' => ''));
            }
        }
    }

    /*private function to compare are find difference between two arrays. done by jane.*/
    private function array_recursive_diff($current_validator_ids, $previous_validator_ids)
    {
        $difference = array();
        foreach ($current_validator_ids as $key => $value) {
            if (array_key_exists($key, $previous_validator_ids)) {
                if (is_array($value)) {
                    $recursive_diff = $this->array_recursive_diff($value, $previous_validator_ids[$key]);
                    if (count($recursive_diff)) {
                        $difference[$key] = $recursive_diff;
                    }
                } else {
                    if ($value != $previous_validator_ids[$key]) {
                        $difference[$key] = $value;
                    }
                }
            } else {
                $difference[$key] = $value;
            }
        }

        return $difference;
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

    // added by agaile to enable edit and non edit start date 23/11/2015
    function fetch_dataentry_no()
    {
        $journal_no = trim($_POST['journal_no']);
        $data = array();
        if ($journal_no != "") {
            $result = $this->agailemodel->fetch_dataentry_no($journal_no);
            if ($result) {
                //$this->load->helper('image_upload');
                $data['status'] = 'failed';
            } else {
                $data['status'] = 'success';
            }
        } else {
            $data['status'] = '';
        }
        echo json_encode($data);
    }

    // function to get lookup data detail. done by jane
    function get_lookup_data()
    {
        $id=$this->input->post('id');
        if(!empty($id))
        {
            //query the database
            $data['menu_details'] = $this->design->get_lookup_data($id);
            if ($data) {
                $data['status'] = "success";
            } else {
                $data['status'] = "fail";
            }
            echo json_encode($data);
        }
    }


    /*function to update reminders*/
    function update_reminder(){
        $this->reminder->update_reminder();
    }
}
?>