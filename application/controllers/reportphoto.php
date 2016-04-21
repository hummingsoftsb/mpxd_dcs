
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
        $this->load->library(array('phpppt'));
        $this->load->helper(array('url'));
        //smijith 18/04/2016
        //$this->load->library(array('imageresize'));

    }

    function index($offset = 0) {
		$curr = 0;
		$settings[0] = array(10,38,10,295);
		$settings[2] = array(10,340,10,597);
		$settings[4] = array(10,642,10,899);
		
		$settings[1] = array(373,38,373,295);		
		$settings[3] = array(373,340,373,597);		
		$settings[5] = array(373,642,373,899);
        //var_dump($this->phpppt->t());
        //$this->phpppt->generateTitle('V4 PROJECT PROGRESS','11TH JULY 2014');
        //$this->phpppt->generatepicture('./journalimage/4629/1/13022015175343.jpg','Stressing work for LG1 in progress at span TT33R – TT32R (near KGPA)',100,30,40,310); //pic1
        //$this->phpppt->gowrite(base_url());
        //$this->assessment->update_journal_date_status($id);
        $freq_id = $this->input->get('freq');
		$projects = $this->input->get('project');
		$pdate = $this->input->get('date');

		//Requests for ppt
        if ($freq_id && $projects) {
			$pjcts_no = implode(",",$projects);
            $this->phpppt->removefirstslide();
            $imgs = $this->assessment->get_image_by_date($pdate,$pjcts_no);
            $projects = array();
            foreach ($imgs as $img) {
                $projects[$img->project_no] = array('project_no' => $img->project_no, 'project_name' => $img->project_name, 'as_at' => $img->cut_off_date);
            }
            //var_dump($projects);
			$pageno = 1;
            //$curr =0;
            foreach ($projects as $k => $project) {
                $chkCount=0;
                $chk=0;
                $this->phpppt->newslide();
				$this->phpppt->generatelogo();
                //start:mod by Smijith for Construction change to project
               $pjct_nm= str_replace("Construction","Project",$project['project_name']);
                $this->phpppt->generateTitle($pjct_nm, date("dS M Y", strtotime($project['as_at'])));
                //end:mod by Smijith for Construction change to project
                $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])),$pageno);
                    //foreach ($imgs as $img) {
                    //$chkCount++;
                    //start:mod by ANCY MATHEW for PPT correction
                        $chkCount=$this->assessment->get_chk_count( $project['project_no']);
                    //end: mod by ANCY MATHEW for PPT correction
                   // }
                        foreach ($imgs as $img) {
                        if ($img->project_no == $project['project_no']) {

                                $this->phpppt->generatepicture('./' . $img->pict_file_path . $img->pict_file_name, $img->pict_definition, $settings[$curr][0], $settings[$curr][1], $settings[$curr][2], $settings[$curr][3]);
                                $curr++;
                                $chk++;
                            //start:mod by ANCY MATHEW for PPT correction
                                if ($curr == 6 && $chk<$chkCount[0]->count ) {
                             //end:mod by ANCY MATHEW for PPT correction
                                    $curr = 0;
                                    $this->phpppt->newslide();
                                    $this->phpppt->generatelogo();
                                    $this->phpppt->generateTitle($pjct_nm, date("dS M Y", strtotime($project['as_at'])));
                                    $this->phpppt->generateFooter(date("d F Y", strtotime($project['as_at'])), ++$pageno);

                               //}
                            }
                        }

                    }
				$curr =0;
				$pageno++;
            }
            $this->phpppt->gowrite(base_url());

        }

        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];

        $roleid = $session_data['roleid'];

		//Get project template
        $project_arr = array(
            array( 'name' => 'V1 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Sungai Buloh Construction Progress' , 'indent' => 1),
            array( 'name' =>'Kampung Selamat Construction Progress' , 'indent' => 1),
            array( 'name' =>'Kwasa Damansara Construction Progress' , 'indent' => 1),

            array( 'name' =>'V2 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Kwasa Sentral Construction Progress' , 'indent' => 1),
            array( 'name' =>'Kota Damansara Construction Progress' , 'indent' => 1),
            array( 'name' =>'Surian Construction Progress' , 'indent' => 1),

            array( 'name' =>'V3 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Bandar Utama Construction Progress' , 'indent' => 1),
            array( 'name' =>'TTDI Construction Progress' , 'indent' => 1),
            array( 'name' =>'Mutiara Damansara Construction Progress' , 'indent' => 1),

            array( 'name' =>'V4 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Phileo Damansara Construction Progress' , 'indent' => 1),
            array( 'name' =>'Pusat Bandar Damansara Construction Progress' , 'indent' => 1),
            array( 'name' =>'Semantan Construction Progress' , 'indent' => 1),

            array( 'name' =>'Depot 1 Construction Progress' , 'indent' => 0),

            array( 'name' =>'Underground Construction Progress' , 'indent' => 0),
            array( 'name' =>'Underground Tunnel Construction Progress' , 'indent' => 1),
            array( 'name' =>'Muzium Negara Construction Progress' , 'indent' => 2),
            array( 'name' =>'Pasar Seni Construction Progress' , 'indent' => 2),
            array( 'name' =>'Merdeka Construction Progress' , 'indent' => 2),
            array( 'name' =>'Bukit Bintang Construction Progress' , 'indent' => 2),
            array( 'name' =>'Tun Razak Exchange Construction Progress' , 'indent' => 2),
            array( 'name' =>'Cochrane Construction Progress' , 'indent' => 2),
            array( 'name' =>'Maluri Construction Progress' , 'indent' => 2),

            array( 'name' =>'V5 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Taman Mutiara Construction Progress' , 'indent' => 1),
            array( 'name' =>'Taman Connaught Construction Progress' , 'indent' => 1),
            array( 'name' =>'Taman Pertama Construction Progress' , 'indent' => 1),
            array( 'name' =>'Taman Midah Construction Progress' , 'indent' => 1),

            array( 'name' =>'V6 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Banda Tun Hussein Onn Construction Progress' , 'indent' => 1),
            array( 'name' =>'Sri Raya Construction Progress' , 'indent' => 1),
            array( 'name' =>'Taman Suntex Construction Progress' , 'indent' => 1),

            array( 'name' =>'V7 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Taman Koperasi Cuepacs Construction Progress' , 'indent' => 1),
            array( 'name' =>'Bukit Dukung Construction Progress' , 'indent' => 1),

            array( 'name' =>'V8 Construction Progress' , 'indent' => 0),
            array( 'name' =>'Sungai Kantan Construction Progress' , 'indent' => 1),
            array( 'name' =>'Bandar Kajang Construction Progress' , 'indent' => 1),
            array( 'name' =>'Kajang Construction Progress' , 'indent' => 1),

            array( 'name' =>'Depot 2 Construction Progress' , 'indent' => 0),

            array( 'name' =>'MSPR 1 Construction Progress' , 'indent' => 0),
            array( 'name' =>'MSPR 4 Construction Progress' , 'indent' => 0),
            array( 'name' =>'MSPR 6 Construction Progress' , 'indent' => 0),
            array( 'name' =>'MSPR 8 Construction Progress' , 'indent' => 0),
            array( 'name' =>'MSPR 9 Construction Progress' , 'indent' => 0),
            array( 'name' =>'MSPR 11 Construction Progress' , 'indent' => 0),


        );
		
		$projects = array();
		$projects_data = $this->design->show_projtmps();
		$projects_assigned = $this->design->show_projtmps_byid($session_data['id']);
		//echo json_encode($projects_data); die();
		
		$project_assigned = array();
		foreach($projects_assigned as $pa){
			$project_assigned[] = strtolower($pa->project_name);
		}
		//echo array_search(strtolower('V1s Construction Progress'), $project_assigned); die();
		//echo json_encode($project_assigned); die();
		foreach($project_arr as $pa){			
			foreach($projects_data as $pdata){
				if(strtolower($pa['name']) == strtolower($pdata->project_name) && ($session_data['roleid'] == 1 || $session_data['roleid'] == 16)){
					$pdata->indent = $pa['indent'];
					$projects[] = $pdata;
				}
				else if(strtolower($pa['name']) == strtolower($pdata->project_name)){
					//var_dump(gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer');
					if(gettype(array_search(strtolower($pa['name']), $project_assigned)) == 'integer'){
						$pdata->indent = $pa['indent'];
						$projects[] = $pdata;
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
        $data1['alertcount']=count($data1['alerts']);
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

}
?>
