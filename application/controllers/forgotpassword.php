<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgotpassword extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('user','',TRUE);
		//$this->load->library('email');
		$this->load->library(array('email','swiftmailer'));
		$this->load->helper(array('form','url'));
	}
	
	public function index()
	{
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
		$data['message']=$message;
		
		$this->load->view('front_header');
		$this->load->view('front_forgot',$data);
		$this->load->view('front_footer');
	}

	function verify_user()
	{
		$this->load->library('form_validation');

		if ( ! $this->input->post('email'))
		{
			$this->form_validation->set_rules('username', 'Username or E-mail Id', 'trim|required|xss_clean|callback_check_database');
		}
		else
		{
			$this->form_validation->set_rules('username', 'Username', '');
		}
		
		// If no username, email is required
		if ( ! $this->input->post('username'))
		{
		   $this->form_validation->set_rules('email', 'Username or E-mail Id', 'trim|required|xss_clean|callback_check_database');
		}
		else
		{
		   $this->form_validation->set_rules('email', 'Email Address', 'valid_email');
		}

		if($this->form_validation->run() == FALSE)
		{
			//Field validation failed.  User redirected to forgot password page
			$this->index();
		}
		else
		{
			//Go to Forgot Password
			$sess_array = array('message' => "Reset Password link has been sent to your email address.");
			$this->session->set_userdata('message', $sess_array);
			redirect('forgotpassword', 'refresh');
		}
	}

	function check_database()
	{
		//Field validation succeeded.  Validate against database
		$email = $this->input->post('email');
		$username = $this->input->post('username');

		if($username!="" || $email!="")
		{
			//query the database
			$result = $this->user->chkuser($username,$email);

			if($result)
			{
				foreach($result as $row)
				{
					$id=$row->user_id;
					$user_full_name=$row->user_full_name;
					$username=$row->user_name;
					$email=$row->email_id;
				}
				$cdate1=date('Y-m-d h:i:s');
				$cdate=date('Y-m-d');
				$hstring=$id."_".$user_full_name."_".$email."_".$cdate1;
				$hkey=hash_hmac('ripemd160', $hstring, 'secret');
				$data = array('hash_key' => $hkey,'user_id' => $id,'entry_date' => $cdate);

				//insert the value in reset password table
				$result = $this->user->add_reset($data);
				$this->swiftmailer->reset_password_front($user_full_name, $username, $email, $hkey);
				/*$this->email->from('test@hummingsoft.com.my', 'MPXD');
					$this->email->to($email);
					$url="http://remote.hummingsoft.com.my:8080/resetpassword?qcode=".$hkey;
					$message="Dear ".$user_full_name.", <br>Please click the following link in order to reset your username ".$username." login password <a href=".$url.">Click Me</a><br>";
					$this->email->subject('MPXD Data Capture System Login Reset Detail');
					$this->email->message($message);

					$this->email->send();
				*/

					//echo $this->email->print_debugger();

				return true;
			}
			else
			{
				$this->form_validation->set_message('check_database', 'Invalid Username or Email');
				return false;
			}
		}
	}
}
?>