<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resetpassword extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->helper(array('form','url'));
	}

	public function index()
	{
		$vcode=$this->input->get('qcode');
		$result = $this->user->chkcode($vcode);
		if($result)
		{
			foreach($result as $row)
			{
			$data['id']=$row->user_id;
			}
		} else {
			$data['id']="invalid";
		}
		$this->load->view('front_header');
		$this->load->view('front_reset',$data);
		$this->load->view('front_footer');

	}

	function update()
	{
		$this->load->library('form_validation');

		   $this->form_validation->set_rules('newpass', 'New Password', 'trim|required|xss_clean|matches[retypepass]');

		   $this->form_validation->set_rules('retypepass', 'Retype New Password', 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('newpass'),'msg1'=>form_error('retypepass')));
		}
		else
		{
			//Reset Password and Go To Login Page
			$id=$this->input->post('uid');
			$data=md5($this->input->post('newpass'));
			$result = $this->user->pass_reset($data,$id);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}
}
?>