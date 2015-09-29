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
        if ($freq_id && $projects) {
			
			$pjcts_no = implode(",",$projects);
			
            $this->phpppt->removefirstslide();

            $imgs = $this->assessment->get_image_by_date($pdate,$pjcts_no);
            $projects = array();
            foreach ($imgs as $img) {
                $projects[$img->project_no] = array('project_no' => $img->project_no, 'project_name' => $img->project_name, 'as_at' => $img->cut_off_date);
            }

            //var_dump($projects);	
            foreach ($projects as $k => $project) {
                $this->phpppt->newslide();
                $this->phpppt->generateTitle($project['project_name'], date("dS M Y", strtotime($project['as_at'])));
                foreach ($imgs as $img) {
                    //var_dump($img);
                    if ($img->project_no == $project['project_no']) {
                        $this->phpppt->generatepicture('.' . $img->pict_file_path . $img->pict_file_name, $img->pict_definition, $settings[$curr][0], $settings[$curr][1], $settings[$curr][2], $settings[$curr][3]);
						$curr++;
						if($curr == 6){
							$curr = 0;
							$this->phpppt->newslide();
							$this->phpppt->generateTitle($project['project_name'], date("dS M Y", strtotime($project['as_at'])));
						}
                    }
					
                }
				$curr = 0;
            }
            $this->phpppt->gowrite(base_url());
        }

        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];

        $roleid = $session_data['roleid'];

        //Load all record data
		$data['projects'] = $this->design->show_projtmps();
        $data['cpagename'] = 'reportphoto';
        $data['labels'] = $this->securitys->get_label(7);
        $data['labelgroup'] = $this->securitys->get_label_group(7);
        $data['labelobject'] = $this->securitys->get_label_object(25);
        $data['freqs'] = $this->assessment->get_current_freq();


        $data1['username'] = $session_data['username'];
        $data1['alerts'] = $this->alertreminder->show_alert($session_data['id']);
        $data1['alertcount'] = $this->alertreminder->count_alert($session_data['id']);
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