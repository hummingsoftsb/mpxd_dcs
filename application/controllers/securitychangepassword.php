<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Securitychangepassword extends CI_Controller
{
	function __construct()
	{
	   parent::__construct();
	   $this->load->model('user','',TRUE);
	   $this->load->model('securitys','',TRUE);
	   $this->load->model('alertreminder','',TRUE);
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"16");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			// Config setup for Pagination
			$config['base_url'] = base_url().'index.php/changepassword/index';


			//Load all record data
			$data['cpagename']='securitychangepassword';
			$data['labels']=$this->securitys->get_label(16);
			$data['labelgroup']=$this->securitys->get_label_group(16);
			$data['labelobject']=$this->securitys->get_label_object(16);
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
			$this->load->view('security_changepass', $data);
			$this->load->view('footer');
		}
		else
		{
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(27);
		$label1=$this->securitys->get_label_object_name(28);
		$label2=$this->securitys->get_label_object_name(29);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('oldpass', $label, 'trim|required|xss_clean|alpha_numeric_spaces_special|callback_chkoldpass');
		$this->form_validation->set_rules('newpass', $label1, 'trim|required|xss_clean|alpha_numeric_spaces_special|matches[renewpass]');
		$this->form_validation->set_rules('renewpass', $label2, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('oldpass'),'msg1'=>form_error('newpass'),'msg2'=>form_error('renewpass')));
		}
		else
		{
			$username=$this->input->post('username');
			$data=array('pwd_txt'=>md5($this->input->post('newpass')),'change_pwd_opt' => '0');

			$sess_array1=array('cpass' => '0');
			$this->session->set_userdata('cpass',$sess_array1);
			//query the database
			$result = $this->securitys->Changepwd_user($username,$data);

			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
	function chkoldpass()
	{
		$label=$this->securitys->get_label_object_name(27);
		$username=$this->input->post('username');
		$oldpass=md5($this->input->post('oldpass'));

		$result = $this->securitys->chkoldpass_user($username, $oldpass);
		if($result)
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('chkoldpass', 'Invalid '.$label);
			return false;
		}
	}

}
?>