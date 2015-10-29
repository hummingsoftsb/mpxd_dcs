<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Journalvalidationnonp extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('assessment','',TRUE);
   	   $this->load->model('securitys','',TRUE);
	   $this->load->model('alertreminder','',TRUE);
	   $this->load->model('ilyasmodel','',TRUE);
        $this->load->model('agailemodel','',TRUE);
	}

    /**
     * @param int $offset
     */
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
			$userid=$session_data['id'];

			$roleid=$session_data['roleid'];
			$roleperms=$this->securitys->show_permission_object_data($roleid,"4");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="journalvalidationnonp" && $_SERVER['QUERY_STRING']=="")
			{
				$this->session->unset_userdata('selectrecord');
				$this->session->unset_userdata('searchrecord');
			}
			if($this->session->userdata('message'))
			{
				$messagehrecord=$this->session->userdata('message');
				$message=$messagehrecord['message'];
				$this->session->unset_userdata('message');
			}
			else
			{
				$message='';
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

            /* Added Comments by Agaile on 24/10/2015 */
            /* Pagination class by CodeIgniter is used here */

			// Config setup for Pagination

            // Set base_url for every links
			$config['base_url'] = base_url().'index.php/journalvalidationnonp/index';
            // Set total rows in the result set you are creating pagination for.
			$config['total_rows'] = $this->assessment->totalvalde($search,$userid,$roleid);
			if($this->session->userdata('selectrecord'))
			{
                // Number of items you intend to show per page.
				$selectrecord=$this->session->userdata('selectrecord');
				$config['per_page'] = $selectrecord['selectrecord'];
			}
			else
			{
                // Number of items you intend to show per page.
				$config['per_page'] = 10;
			}
            //The pagination function automatically determines which segment of your URI contains the page number. If you need something different you can specify it.
			$config['uri_segment'] = 3;
            //Set that how many number of pages you want to view.

             /* Start Agaile */
            // Re-configured the condition check Agaile 24/10/2015
            $total_row = $this->agailemodel->get_count($search,$offset,$config['per_page'],$userid,$roleid);
            if($total_row > 10) {
                $config["num_links"] = 1;
            }
            else{
                $config["num_links"] = 0;
                $config['display_pages'] = FALSE;
                $config['last_link'] = FALSE;
                $config['next_link'] = FALSE;
            }

            /* End Agaile */
            // To initialize "$config" array and set to pagination library.
			$this->pagination->initialize($config);
			$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


			//Load all record data
			$data['records'] = $this->ilyasmodel->get_journals_validate($search,$offset,$config['per_page'],$userid,$roleid);
			//$data['records'] = $this->assessment->show_valde($search,$offset,$config['per_page'],$userid);

            // commented on 24/10/2015 by Agaile bcoz its showing the wrong no of rows
			//$data['totalrows'] = $config['total_rows'];

            $data['totalrows'] =$total_row;

			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='journalvalidationnonp';
			$data['labels']=$this->securitys->get_label(4);
			$data['labelgroup']=$this->securitys->get_label_group(4);
			$data['labelobject']=$this->securitys->get_label_object(24);
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
			$data1['username'] = $session_data['username'];
			$data1['alerts']=$this->alertreminder->show_alert($session_data['id']);
			$data1['alertcount']=$this->alertreminder->count_alert($session_data['id']);
			$data1['reminders']=$this->alertreminder->show_reminder($session_data['id']);
			$data1['remindercount']=$this->alertreminder->count_reminder($session_data['id']);
			$data1['alabels']=$this->securitys->get_label(22);
			$data1['alabelobject']=$this->securitys->get_label_object(22);
			$data1['rlabels']=$this->securitys->get_label(23);
			$data1['rlabelobject']=$this->securitys->get_label_object(23);

			$this->load->view('header', $data1);
			$this->load->view('assess_journalvalidationnonp', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			redirect('login', 'refresh');
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
}
?>