
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Reportphoto extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('admin', '', TRUE);
        $this->load->model('securitys', '', TRUE);
        $this->load->model('alertreminder', '', TRUE);
        $this->load->model('assessment', '', TRUE);
        $this->load->model('design', '', TRUE);
        $this->load->library('phpppt');
        $this->load->helper('url');
		
        //smijith 18/04/2016
        //$this->load->library(array('imageresize'));

    }

//    function index($offset = 0) {
//        $curr = 0;
//        $settings[0] = array(10, 38, 10, 295);
//        $settings[2] = array(10, 340, 10, 597);
//        $settings[4] = array(10, 642, 10, 899);
//
//        $settings[1] = array(373, 38, 373, 295);
//        $settings[3] = array(373, 340, 373, 597);
//        $settings[5] = array(373, 642, 373, 899);
//        $freq_id = $this->input->get('freq');
//        $projects = $this->input->get('project');
//        $pdate = $this->input->get('date');
//        $file_name = $this->input->get('ppt_filename');
//
//        //Requests for ppt
//        if ($freq_id && $projects) {
//            $pjcts_no = implode(",", $projects);
//            $this->phpppt->removefirstslide();
//            $imgs = $this->assessment->get_image_by_date($pdate, $pjcts_no);
//            $projects = array();
//            foreach ($imgs as $img) {
//                $projects[$img->project_no] = array('project_no' => $img->project_no, 'project_name' => $img->project_name, 'as_at' => $img->cut_off_date);
//            }
//            //var_dump($projects);
//            //start:mod by ANCY MATHEW for redusing the size of ppt
//           /* $session_data = $this->session->userdata('logged_in');
//            $userid=$session_data['id'];
//            $tfolder = FCPATH.'/journalimagetemp';
//            $from_folder = FCPATH ;*/
//            //end:mod by ANCY MATHEW for redusing the size of ppt
//            $pageno = 1;
//			$temp_image = array();
//            foreach ($projects as $k => $project) {
//                $chkCount = 0;
//                $chk = 0;
//                $this->phpppt->newslide();
//                $this->phpppt->generatelogo();
//                //start:mod by Smijith for Construction change to project
//                $pjct_nm = str_replace("Construction", "Project", $project['project_name']);
//                $this->phpppt->generateTitle($pjct_nm, date("jS M Y", strtotime($project['as_at'] . " +2 days")));
//                //end:mod by Smijith for Construction change to project
//                $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])), $pageno);
//                //start:mod by ANCY MATHEW for PPT correction
//                $chkCount = $this->assessment->get_chk_count($project['project_no']);
//                //end: mod by ANCY MATHEW for PPT correction
//                // }
//                //start:mod by ANCY MATHEW for reduce the image size
//                $indexno = 0;
//                $v = '';
//                foreach ($imgs as $img) {
//                    if ($img->project_no == $project['project_no']) {
//						/** resize photo for powerpoint **/
//						//$real_image = './' . $img->pict_file_path . $img->pict_file_name;
//
//						$ppt_image = $this->resize_image($img->pict_file_path, $img->pict_file_name);
//						$temp_image[] = $ppt_image;
//
//                        $this->phpppt->generatepicture($ppt_image, $img->pict_definition, $settings[$curr][0], $settings[$curr][1], $settings[$curr][2], $settings[$curr][3]);
//
//                        $curr++;
//                        $chk++;
//                        //start:mod by ANCY MATHEW for PPT correction
//                        if ($curr == 6 && $chk < $chkCount[0]->count && sizeof($imgs) != 6) {
//                            //end:mod by ANCY MATHEW for PPT correction
//                            $curr = 0;
//                            $this->phpppt->newslide();
//                            $this->phpppt->generatelogo();
//                            $this->phpppt->generateTitle($pjct_nm, date("dS M Y", strtotime($project['as_at'])));
//                            $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])), ++$pageno);
//                        }
//                    }
//                }
//                $curr = 0;
//                $pageno++;
//            }
//
//            if(!empty($file_name)) {
//                $fname = $file_name;
//            } else{
//                $fname = "";
//            }
//                $this->phpppt->gowrite(base_url(),$fname);
//			//now delete the temp image
//			foreach($temp_image as $img){
//				unlink($img);
//			}
//            //start:mod by ANCY MATHEW for clear the jounalimagetemp directory
//            /* $files = array_diff(scandir($to_folder,1), array('..', '.'));
//             foreach($files as $file) {
//                 $uid = explode("-",$file);
//                 if(trim($uid[0])==$userid) {
//                     unlink($to_folder . '/' . $file);
//                 }
//             }*/
//            //end:mod by ANCY MATHEW clear the jounalimagetemp directory
//        }
//
//        $session_data = $this->session->userdata('logged_in');
//        $data['username'] = $session_data['username'];
//
//        $roleid = $session_data['roleid'];
//
//        //Get project template
//        $project_arr = array(
//            array('name' => 'V1 Construction Progress', 'indent' => 0),
//            array('name' => 'Sungai Buloh Construction Progress', 'indent' => 1),
//            array('name' => 'Kampung Selamat Construction Progress', 'indent' => 1),
//            array('name' => 'Kwasa Damansara Construction Progress', 'indent' => 1),
//
//            array('name' => 'V2 Construction Progress', 'indent' => 0),
//            array('name' => 'Kwasa Sentral Construction Progress', 'indent' => 1),
//            array('name' => 'Kota Damansara Construction Progress', 'indent' => 1),
//            array('name' => 'Surian Construction Progress', 'indent' => 1),
//
//            array('name' => 'V3 Construction Progress', 'indent' => 0),
//            array('name' => 'Bandar Utama Construction Progress', 'indent' => 1),
//            array('name' => 'TTDI Construction Progress', 'indent' => 1),
//            array('name' => 'Mutiara Damansara Construction Progress', 'indent' => 1),
//
//            array('name' => 'V4 Construction Progress', 'indent' => 0),
//            array('name' => 'Phileo Damansara Construction Progress', 'indent' => 1),
//            array('name' => 'Pusat Bandar Damansara Construction Progress', 'indent' => 1),
//            array('name' => 'Semantan Construction Progress', 'indent' => 1),
//
//            array('name' => 'Depot 1 Construction Progress', 'indent' => 0),
//
//            array('name' => 'Underground Construction Progress', 'indent' => 0),
//            array('name' => 'Underground Tunnel Construction Progress', 'indent' => 1),
//            array('name' => 'Muzium Negara Construction Progress', 'indent' => 2),
//            array('name' => 'Pasar Seni Construction Progress', 'indent' => 2),
//            array('name' => 'Merdeka Construction Progress', 'indent' => 2),
//            array('name' => 'Bukit Bintang Construction Progress', 'indent' => 2),
//            array('name' => 'Tun Razak Exchange Construction Progress', 'indent' => 2),
//            array('name' => 'Cochrane Construction Progress', 'indent' => 2),
//            array('name' => 'Maluri Construction Progress', 'indent' => 2),
//
//            array('name' => 'V5 Construction Progress', 'indent' => 0),
//            array('name' => 'Taman Mutiara Construction Progress', 'indent' => 1),
//            array('name' => 'Taman Connaught Construction Progress', 'indent' => 1),
//            array('name' => 'Taman Pertama Construction Progress', 'indent' => 1),
//            array('name' => 'Taman Midah Construction Progress', 'indent' => 1),
//
//            array('name' => 'V6 Construction Progress', 'indent' => 0),
//            array('name' => 'Banda Tun Hussein Onn Construction Progress', 'indent' => 1),
//            array('name' => 'Sri Raya Construction Progress', 'indent' => 1),
//            array('name' => 'Taman Suntex Construction Progress', 'indent' => 1),
//
//            array('name' => 'V7 Construction Progress', 'indent' => 0),
//            array('name' => 'Taman Koperasi Cuepacs Construction Progress', 'indent' => 1),
//            array('name' => 'Bukit Dukung Construction Progress', 'indent' => 1),
//
//            array('name' => 'V8 Construction Progress', 'indent' => 0),
//            array('name' => 'Sungai Kantan Construction Progress', 'indent' => 1),
//            array('name' => 'Bandar Kajang Construction Progress', 'indent' => 1),
//            array('name' => 'Kajang Construction Progress', 'indent' => 1),
//
//            array('name' => 'Depot 2 Construction Progress', 'indent' => 0),
//
//            array('name' => 'MSPR 1 Construction Progress', 'indent' => 0),
//            array('name' => 'MSPR 4 Construction Progress', 'indent' => 0),
//            array('name' => 'MSPR 6 Construction Progress', 'indent' => 0),
//            array('name' => 'MSPR 8 Construction Progress', 'indent' => 0),
//            array('name' => 'MSPR 9 Construction Progress', 'indent' => 0),
//            array('name' => 'MSPR 11 Construction Progress', 'indent' => 0),
//
//
//        );
//
//        $projects = array();
//        $projects_data = $this->design->show_projtmps();
//        $projects_assigned = $this->design->show_projtmps_byid($session_data['id']);
//        //echo json_encode($projects_data); die();
//
//        $project_assigned = array();
//        foreach ($projects_assigned as $pa) {
//            $project_assigned[] = strtolower($pa->project_name);
//        }
//        //echo array_search(strtolower('V1s Construction Progress'), $project_assigned); die();
//        //echo json_encode($project_assigned); die();
//        foreach ($project_arr as $key => $pa) {
//            foreach ($projects_data as $pdata) {
//                if (strtolower($pa['name']) == strtolower($pdata->project_name) && ($session_data['roleid'] == 1 || $session_data['roleid'] == 16)) {
//                    $pdata->indent = $pa['indent'];
//					$pdata->is_disabled = false;
//                    $projects[$key] = $pdata;
//                } else if (strtolower($pa['name']) == strtolower($pdata->project_name)) {
//                    //var_dump(gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer');
//                    if (gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer') {
//                        $pdata->indent = $pa['indent'];
//						$pdata->is_disabled = false;
//                        $projects[$key] = $pdata;
//                    }
//					else {
//						//if($pa['indent'] == 0){
//							$pdata->indent = $pa['indent'];
//							$pdata->is_disabled = true;
//							$projects[$key] = $pdata;
//						//}
//					}
//                }
//            }
//        }
//        /*
//        foreach($projects_data as $k => $pdata){
//            if(!array_search($pdata->project_name,$project_arr)){
//                unset($projects_data[$k]);
//            }
//        }*/
//
//        //echo json_encode($projects); die();
//
//        //Load all record data
//        $data['projects'] = $projects;
//        $data['cpagename'] = 'reportphoto';
//        $data['labels'] = $this->securitys->get_label(7);
//        $data['labelgroup'] = $this->securitys->get_label_group(7);
//        $data['labelobject'] = $this->securitys->get_label_object(25);
//        $data['freqs'] = $this->assessment->get_current_freq();
//
//
//        $data1['username'] = $session_data['username'];
//        $data1['alerts'] = $this->alertreminder->show_alert($session_data['id']);
//        /*$data1['alertcount'] = $this->alertreminder->count_alert($session_data['id']);*/
//        $data1['alertcount'] = count($data1['alerts']);
//        $data1['reminders'] = $this->alertreminder->show_reminder($session_data['id']);
//        $data1['remindercount'] = $this->alertreminder->count_reminder($session_data['id']);
//        $data1['alabels'] = $this->securitys->get_label(22);
//        $data1['alabelobject'] = $this->securitys->get_label_object(22);
//        $data1['rlabels'] = $this->securitys->get_label(23);
//        $data1['rlabelobject'] = $this->securitys->get_label_object(23);
//
//        $this->load->view('header', $data1);
//        $this->load->view('report_photo', $data);
//        $this->load->view('footer');
//    }

    function index($offset = 0) {
        $curr = 0;
		/* Note - agaile 05/07/2016
		settings array value format array(0,0,0,0);
		first and second value is for positioning the image , 3rd and fourth value is for the description
		*/
        $settings[0] = array(10, 50, 10, 305);
        $settings[2] = array(10, 340, 10, 597);
        $settings[4] = array(10, 642, 10, 899);

        $settings[1] = array(373, 50, 373, 305);
        $settings[3] = array(373, 340, 373, 597);
        $settings[5] = array(373, 642, 373, 899);
        $freq_id = $this->input->get('freq');
        /*$projects = $this->input->get('project');*/
        /*modifications by jane*/
        $ids = $this->input->get('ids');
        $projects = array();
        if(!empty($ids)) {
            $ids_list = $this->admin->get_projects($ids);
            foreach ($ids_list as $val) {
                $projects[] = $val['template_id'];
            }
        }
        $pdate = $this->input->get('date');
        $file_name = $this->input->get('ppt_filename');

        //Requests for ppt
        if ($freq_id && $projects) {
            $pjcts_no = implode(",", $projects);
            $this->phpppt->removefirstslide();
            $imgs = $this->assessment->get_image_by_date($pdate, $pjcts_no);
			// to get the parent and child - agaile 05/07/2016
			//$parent_child=$this->admin->get_parent_child($ids);
			$split_id = explode(",", $ids);
			$parent_child = array();
			foreach($split_id as $val){
				array_push($parent_child,$this->admin->get_parent_child($val));
			}
			/*echo '<pre>';
			print_r($parent_child);
			echo '</pre>';
			exit;*/
            $projects = array();
            foreach ($imgs as $img) {	
			// Mod : Agaile
				foreach($parent_child as $kval){
					if($img->project_no == $kval[0]['template_id']){
					$projects[$img->project_no] = array('project_no' => $img->project_no, 'project_name' => $img->project_name, 'as_at' => $img->cut_off_date, 'parent' => $kval[0]['parentname'],'child' => $kval[0]['childname']);
						}
				}
				// Mod : end
              //$projects[$img->project_no] = array('project_no' => $img->project_no, 'project_name' => $img->project_name, 'as_at' => $img->cut_off_date);  
            }
            /*var_dump($projects);
			exit;*/
            //start:mod by ANCY MATHEW for redusing the size of ppt
           /* $session_data = $this->session->userdata('logged_in');
            $userid=$session_data['id'];
            $tfolder = FCPATH.'/journalimagetemp';
            $from_folder = FCPATH ;*/
            //end:mod by ANCY MATHEW for redusing the size of ppt
            $pageno = 1;
			$temp_image = array();
            foreach ($projects as $k => $project) {
                $chkCount = 0;
                $chk = 0;
                $this->phpppt->newslide();
                $this->phpppt->generatelogo();
				// START : Subtitle - AGAILE
				if($project['parent'] == 'Root Node'){
                    echo "root node";
                $pjct_nm_c = str_replace("Construction", "Project", $project['child']);
                $this->phpppt->generateTitle($pjct_nm_c, date("jS M Y", strtotime($project['as_at'] . " +2 days")));
				}
				else{
				$pjct_nm_p = str_replace("Construction", "Project", $project['parent']);
                $pjct_nm_c = str_replace("Construction", "Project", $project['child']);
				$this->phpppt->generateTitle($pjct_nm_p, date("jS M Y", strtotime($project['as_at'] . " +2 days")));
				$this->phpppt->generateSubTitle($pjct_nm_c);
					}
				// END : Subtitle - AGAILE
				
                $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])), $pageno);
                //start:mod by ANCY MATHEW for PPT correction
                $chkCount = $this->assessment->get_image_by_date_one($pdate,$project['project_no']);
               // $chkCount = $this->assessment->get_chk_count($project['project_no']);
                //end: mod by ANCY MATHEW for PPT correction
                // }
                //start:mod by ANCY MATHEW for reduce the image size
                $indexno = 0;
                $v = '';
                foreach ($imgs as $img) {
                    if ($img->project_no == $project['project_no']) {

						/** resize photo for powerpoint **/
						//$real_image = './' . $img->pict_file_path . $img->pict_file_name;

						$ppt_image = $this->resize_image($img->pict_file_path, $img->pict_file_name);
						$temp_image[] = $ppt_image;

                        $this->phpppt->generatepicture($ppt_image, $img->pict_definition, $settings[$curr][0], $settings[$curr][1], $settings[$curr][2], $settings[$curr][3]);

                        $curr++;
                        $chk++;
                        //start:mod by ANCY MATHEW for PPT correction
                        if ($curr == 6 && $chk < $chkCount[0]->count && sizeof($imgs) != 6) {

                            //end:mod by ANCY MATHEW for PPT correction
                            $curr = 0;
                            $this->phpppt->newslide();
                            $this->phpppt->generatelogo();
							// START : Subtitle - AGAILE
							if($project['parent'] == 'Root Node'){
								$pjct_nm_c = str_replace("Construction", "Project", $project['child']);
								$this->phpppt->generateTitle($pjct_nm_c, date("jS M Y", strtotime($project['as_at'] . " +2 days")));
							}
							else{
								$pjct_nm_p = str_replace("Construction", "Project", $project['parent']);
								$pjct_nm_c = str_replace("Construction", "Project", $project['child']);
								$this->phpppt->generateTitle($pjct_nm_p, date("jS M Y", strtotime($project['as_at'] . " +2 days")));
								$this->phpppt->generateSubTitle($pjct_nm_c);
								}
							// END : Subtitle - AGAILE
                            $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])), ++$pageno);
                        }
                    }
                }

                $curr = 0;
                $pageno++;
            }

            if(!empty($file_name)) {
                $fname = $file_name;
            } else{
                $fname = "";
            }
                $this->phpppt->gowrite(base_url(),$fname);
			//now delete the temp image
			foreach($temp_image as $img){
				unlink($img);
			}
            //start:mod by ANCY MATHEW for clear the jounalimagetemp directory
            /* $files = array_diff(scandir($to_folder,1), array('..', '.'));
             foreach($files as $file) {
                 $uid = explode("-",$file);
                 if(trim($uid[0])==$userid) {
                     unlink($to_folder . '/' . $file);
                 }
             }*/
            //end:mod by ANCY MATHEW clear the jounalimagetemp directory
        }

        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];

        $roleid = $session_data['roleid'];

        //Get project template
        $project_arr = array(
            array('name' => 'V1 Construction Progress', 'indent' => 0),
            array('name' => 'Sungai Buloh Construction Progress', 'indent' => 1),
            array('name' => 'Kampung Selamat Construction Progress', 'indent' => 1),
            array('name' => 'Kwasa Damansara Construction Progress', 'indent' => 1),

            array('name' => 'V2 Construction Progress', 'indent' => 0),
            array('name' => 'Kwasa Sentral Construction Progress', 'indent' => 1),
            array('name' => 'Kota Damansara Construction Progress', 'indent' => 1),
            array('name' => 'Surian Construction Progress', 'indent' => 1),

            array('name' => 'V3 Construction Progress', 'indent' => 0),
            array('name' => 'Bandar Utama Construction Progress', 'indent' => 1),
            array('name' => 'TTDI Construction Progress', 'indent' => 1),
            array('name' => 'Mutiara Damansara Construction Progress', 'indent' => 1),

            array('name' => 'V4 Construction Progress', 'indent' => 0),
            array('name' => 'Phileo Damansara Construction Progress', 'indent' => 1),
            array('name' => 'Pusat Bandar Damansara Construction Progress', 'indent' => 1),
            array('name' => 'Semantan Construction Progress', 'indent' => 1),

            array('name' => 'Depot 1 Construction Progress', 'indent' => 0),

            array('name' => 'Underground Construction Progress', 'indent' => 0),
            array('name' => 'Underground Tunnel Construction Progress', 'indent' => 1),
            array('name' => 'Muzium Negara Construction Progress', 'indent' => 2),
            array('name' => 'Pasar Seni Construction Progress', 'indent' => 2),
            array('name' => 'Merdeka Construction Progress', 'indent' => 2),
            array('name' => 'Bukit Bintang Construction Progress', 'indent' => 2),
            array('name' => 'Tun Razak Exchange Construction Progress', 'indent' => 2),
            array('name' => 'Cochrane Construction Progress', 'indent' => 2),
            array('name' => 'Maluri Construction Progress', 'indent' => 2),

            array('name' => 'V5 Construction Progress', 'indent' => 0),
            array('name' => 'Taman Mutiara Construction Progress', 'indent' => 1),
            array('name' => 'Taman Connaught Construction Progress', 'indent' => 1),
            array('name' => 'Taman Pertama Construction Progress', 'indent' => 1),
            array('name' => 'Taman Midah Construction Progress', 'indent' => 1),

            array('name' => 'V6 Construction Progress', 'indent' => 0),
            array('name' => 'Banda Tun Hussein Onn Construction Progress', 'indent' => 1),
            array('name' => 'Sri Raya Construction Progress', 'indent' => 1),
            array('name' => 'Taman Suntex Construction Progress', 'indent' => 1),

            array('name' => 'V7 Construction Progress', 'indent' => 0),
            array('name' => 'Taman Koperasi Cuepacs Construction Progress', 'indent' => 1),
            array('name' => 'Bukit Dukung Construction Progress', 'indent' => 1),

            array('name' => 'V8 Construction Progress', 'indent' => 0),
            array('name' => 'Sungai Kantan Construction Progress', 'indent' => 1),
            array('name' => 'Bandar Kajang Construction Progress', 'indent' => 1),
            array('name' => 'Kajang Construction Progress', 'indent' => 1),

            array('name' => 'Depot 2 Construction Progress', 'indent' => 0),

            array('name' => 'MSPR 1 Construction Progress', 'indent' => 0),
            array('name' => 'MSPR 4 Construction Progress', 'indent' => 0),
            array('name' => 'MSPR 6 Construction Progress', 'indent' => 0),
            array('name' => 'MSPR 8 Construction Progress', 'indent' => 0),
            array('name' => 'MSPR 9 Construction Progress', 'indent' => 0),
            array('name' => 'MSPR 11 Construction Progress', 'indent' => 0),


        );

        $projects = array();
        $projects_data = $this->design->show_projtmps();
        $projects_assigned = $this->design->show_projtmps_byid($session_data['id']);
        //echo json_encode($projects_data); die();

        $project_assigned = array();
        foreach ($projects_assigned as $pa) {
            $project_assigned[] = strtolower($pa->project_name);
        }
        //echo array_search(strtolower('V1s Construction Progress'), $project_assigned); die();
        //echo json_encode($project_assigned); die();
        foreach ($project_arr as $key => $pa) {
            foreach ($projects_data as $pdata) {
                if (strtolower($pa['name']) == strtolower($pdata->project_name) && ($session_data['roleid'] == 1 || $session_data['roleid'] == 16)) {
                    $pdata->indent = $pa['indent'];
					$pdata->is_disabled = false;
                    $projects[$key] = $pdata;
                } else if (strtolower($pa['name']) == strtolower($pdata->project_name)) {
                    //var_dump(gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer');
                    if (gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer') {
                        $pdata->indent = $pa['indent'];
						$pdata->is_disabled = false;
                        $projects[$key] = $pdata;
                    }
					else {
						//if($pa['indent'] == 0){
							$pdata->indent = $pa['indent'];
							$pdata->is_disabled = true;
							$projects[$key] = $pdata;
						//}
					}
                }
            }
        }
        /*
        foreach($projects_data as $k => $pdata){
            if(!array_search($pdata->project_name,$project_arr)){
                unset($projects_data[$k]);
            }
        }*/

        //echo json_encode($projects); die();

        //Load all record data
        $data['projects'] = $projects;
        $data['cpagename'] = 'reportphoto';
        $data['labels'] = $this->securitys->get_label(7);
        $data['labelgroup'] = $this->securitys->get_label_group(7);
        $data['labelobject'] = $this->securitys->get_label_object(25);
        $data['freqs'] = $this->assessment->get_current_freq();


        $data1['username'] = $session_data['username'];
        $data1['alerts'] = $this->alertreminder->show_alert($session_data['id']);
        /*$data1['alertcount'] = $this->alertreminder->count_alert($session_data['id']);*/
        $data1['alertcount'] = count($data1['alerts']);
        $data1['reminders'] = $this->alertreminder->show_reminder($session_data['id']);
        $data1['remindercount'] = $this->alertreminder->count_reminder($session_data['id']);
        $data1['alabels'] = $this->securitys->get_label(22);
        $data1['alabelobject'] = $this->securitys->get_label_object(22);
        $data1['rlabels'] = $this->securitys->get_label(23);
        $data1['rlabelobject'] = $this->securitys->get_label_object(23);

        $this->load->view('header', $data1);
        $this->load->view('report_photo', $data);
        $this->load->view('footer');
    }

	public function resize_image($path,$image){
		//$this->load->library('imageresize');
		$this->load->library("imageresize",array('./' . $path . $image)); //var_dump($this->imageresize); die();
		$resizer = new ImageResize(array('./' . $path . $image));
		
		$resizer->crop(338, 256);
		$ppt_image = './' . $path .'ppt_'. $image;
		$resizer->save($ppt_image);
		return $ppt_image;
	}

/*done by jane for viewing template hierarchy list*/
    public function tree_view()
    {
        if (isset($_GET['operation'])) {
            $result = array();
            switch ($_GET['operation']) {
                case 'get_node':
                    $session_data = $this->session->userdata('logged_in');
                    $res = $this->admin->get_template_hierarchy_list($session_data);
                    if (empty($res)) {
                        /*$result['status'] = "Empty";*/
                    } else {
                        foreach ($res as $row) {
                            $data1[] = $row;
                        }
                        $itemsByReference = array();

                        foreach ($data1 as $key => &$item) {
                            $itemsByReference[$item['id']] = &$item;
                            $itemsByReference[$item['id']]['children'] = array();
                            $itemsByReference[$item['id']]['data'] = new StdClass();
                        }

                        foreach ($data1 as $key => &$item)
                            if ($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
                                $itemsByReference [$item['parent_id']]['children'][] = &$item;

                        foreach ($data1 as $key => &$item) {
                            if ($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
                                unset($data1[$key]);
                        }
                    }
                    if(!empty($data1)) {
                        $new = array();
                        foreach ($data1 as $key => $val) {
                            $new[] = $val;
                        }
                        $result = $new;
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($result);
            }
        }
    }
    /*end tree_view function done by jane*/


    /*done by jane for disabling unauthorised template hierarchy list ids*/
    public function get_disable_ids(){
        $data = array();
        $session_data = $this->session->userdata('logged_in');
        $result = $this->admin->get_disable_ids($session_data);
        if(!empty($result)) {
            $ids = array();
            foreach($result as $id){
                $ids[] = $id['id'];
            }
            if ($ids) {
                if($session_data['roleid'] == 1 || $session_data['roleid'] == 16) {
                    $data['status'] = "fail";
                    $data['id'] = array();
                } else {
                    $data['status'] = "success";
                    $data['id'] = $ids;
                }
            } else {
                $data['status'] = "fail";
                $data['id'] = $ids;
            }
            echo json_encode($data);
        }
    }
    /*end get_disable_ids function done by jane*/

}
?>
