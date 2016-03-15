<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Securityuser extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('email');
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
			$roleperms=$this->securitys->show_permission_object_data($roleid,"14");
			foreach ($roleperms as $roleperm):
				$viewperm=$roleperm->view_opt;
				$addperm=$roleperm->add_opt;
				$editperm=$roleperm->edit_opt;
				$delperm=$roleperm->del_opt;
			endforeach;
			if($viewperm==0)
				redirect('/home','refresh');

			if($this->uri->uri_string()=="securityuser" && $_SERVER['QUERY_STRING']=="")
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

			// Config setup for Pagination
			$config['base_url'] = base_url().'index.php/securityuser/index';
			$config['total_rows'] = $this->securitys->totaluser($search);
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
			$data['records'] = $this->securitys->show_user($search,$offset,$config['per_page']);
			$data['totalrows'] = $config['total_rows'];
			$data['mpage'] = $config['per_page'];
			$data['page']= $page+1;
			$data['selectrecord']=$config['per_page'];
			$data['searchrecord']=$search;
			$data['cpagename']='securityuser';
			$data['labels']=$this->securitys->get_label(14);
			$data['labelgroup']=$this->securitys->get_label_group(14);
			$data['labelobject']=$this->securitys->get_label_object(14);
			$data['roles']=$this->securitys->show_roles();
			$data['addperm']=$addperm;
			$data['editperm']=$editperm;
			$data['delperm']=$delperm;
			$data['message']=$message;
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
			$this->load->view('security_user', $data);
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
		$label=$this->securitys->get_label_object_name(18);
		$label1=$this->securitys->get_label_object_name(19);
		$label2=$this->securitys->get_label_object_name(20);
		$label3=$this->securitys->get_label_object_name(21);
		$label4=$this->securitys->get_label_object_name(22);
		$label5=$this->securitys->get_label_object_name(24);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('Email', $label1, 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('role', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('development', $label3, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('username', $label4, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->input->post('chklock')=="on")
		{
			$this->form_validation->set_rules('lockcount', $label5, 'trim|required|is_natural|xss_clean');
		}

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('name'),'msg1'=>form_error('Email'),'msg2'=>form_error('role'),'msg3'=>form_error('development'),'msg4'=>form_error('username'),'msg5'=>form_error('lockcount')));
		}
		else
		{
			$username=$this->input->post('username');
			$email=$this->input->post('Email');

			if($this->securitys->add_check_user($username,$email)==0)
			{
				if($this->input->post('chkpass')=="on")
				{
					$chkpass="1";
				}
				else
				{
					$chkpass="0";
				}
				if($this->input->post('chklock')=="on")
				{
					$lockcount=$this->input->post('lockcount');
				}
				else
				{
					$lockcount="0";
				}
				$pass=strtotime("now");
				$pass1=md5($pass);
				$fullname=$this->input->post('name');
				$data = array('user_name' => $username,'user_full_name' => $fullname,'user_type' => '2','sec_role_id' => $this->input->post('role'),'email_id' => $email,'dept_name' => $this->input->post('development'),'change_pwd_opt' => $chkpass,'lock_by_pwd' => $lockcount,'user_status'=>'1','pwd_txt'=>$pass1);

				//query the database
				$result = $this->securitys->add_user($data);

				$this->email->from('test@hummingsoft.com.my', 'MPXD');
				$this->email->to($email);
			    $message="Dear ".$fullname.", <br>Please find the Login Details <br>URL : http://remote.hummingsoft.com.my:8080/<br>Username : ".$email."<br>Password : ".$pass;
				$this->email->subject('MPXD Data Capture System Login Detail');
				$this->email->message($message);
				$this->email->send();

				$sess_array = array('message' =>  $this->securitys->get_label_object(14)." Added Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Successs'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Username or Email already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>''));
			}
		}
	}

	function update()
	{
		$label=$this->securitys->get_label_object_name(18);
		$label1=$this->securitys->get_label_object_name(19);
		$label2=$this->securitys->get_label_object_name(20);
		$label3=$this->securitys->get_label_object_name(21);
		$label4=$this->securitys->get_label_object_name(22);
		$label5=$this->securitys->get_label_object_name(24);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name1', $label, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('Email1', $label1, 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('role1', $label2, 'trim|required|xss_clean');
		$this->form_validation->set_rules('development1', $label3, 'trim|required|alpha_numeric_spaces_special|xss_clean');
		$this->form_validation->set_rules('username1', $label4, 'trim|required|alpha_numeric_spaces_special|xss_clean');

		if($this->input->post('chklock1')=="on")
		{
			$this->form_validation->set_rules('lockcount1', $label5, 'trim|required|is_natural|xss_clean');
		}

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('name1'),'msg1'=>form_error('Email1'),'msg2'=>form_error('role1'),'msg3'=>form_error('development1'),'msg4'=>form_error('username1'),'msg5'=>form_error('lockcount1')));
		}
		else
		{
			$id=$this->input->post('userid');
			$username=$this->input->post('username1');
			$emailid=$this->input->post('Email1');

			if($this->securitys->update_check_user($id,$username,$emailid)==0)
			{
				if($this->input->post('chkpass1')=="on")
				{
					$chkpass="1";
				}
				else
				{
					$chkpass="0";
				}
				if($this->input->post('chklock1')=="on")
				{
					$lockcount=$this->input->post('lockcount1');
				}
				else
				{
					$lockcount="0";
				}
				$data = array('user_name' => $username,'user_full_name' => $this->input->post('name1'),'sec_role_id' => $this->input->post('role1'),'email_id' => $emailid,'dept_name' => $this->input->post('development1'),'change_pwd_opt' => $chkpass,'lock_by_pwd' => $lockcount);

				//query the database
				$result = $this->securitys->update_user($id,$data);
				$sess_array = array('message' => $this->securitys->get_label_object(14)."  Updated Successfully");
				$this->session->set_userdata('message', $sess_array);
				echo json_encode(array('st'=>1, 'msg' => 'Success'));
			}
			else
			{
				echo json_encode(array('st'=>0, 'msg' => "Username or Email already exists",'msg1'=>'','msg2'=>'','msg3'=>'','msg4'=>'','msg5'=>'','msg6'=>'','msg7'=>''));
			}
		}
	}

	function reset()
	{
		$label=$this->securitys->get_label_object_name(25);
		$label1=$this->securitys->get_label_object_name(26);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pass', $label, 'trim|required|xss_clean|matches[cpass]');
		$this->form_validation->set_rules('cpass', $label1, 'trim|required|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('st'=>0, 'msg' => form_error('pass'),'msg1'=>form_error('cpass')));
		}
		else
		{
			$id=$this->input->post('resetid');
			$data = array('pwd_txt' => md5($this->input->post('pass')),'no_pwd_attempt' => '0');

			//query the database
			$result = $this->securitys->update_user($id,$data);
			$sess_array = array('message' => $label." reset successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
	}

	function delete()
	{
		$id=$this->input->post('id');
		if($this->securitys->delete_check_user($id)==0)
		{
			//query the database
			$result = $this->securitys->delete_user($id);
			$sess_array = array('message' => $this->securitys->get_label_object(14)." Deleted Successfully");
			$this->session->set_userdata('message', $sess_array);
			echo json_encode(array('st'=>1, 'msg' => 'Success'));
		}
		else
		{
			$sess_array = array('message' => "Cannot delete ".$this->securitys->get_label_object(14).", Assigned to ".$this->securitys->get_label_object(6));
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
}
?>