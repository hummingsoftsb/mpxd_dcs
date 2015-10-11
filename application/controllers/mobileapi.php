<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mobileapi extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model('user','',TRUE);
		$this->load->model('mobileapp','',TRUE);
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('assessment','',TRUE);
		$this->load->helper(array('form','url'));
		$this->load->library('parseplugin');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST'); 
		header('Access-Control-Allow-Headers: value="Origin, X-Requested-With, Content-Type, Accept"'); 
		//header('content-type: application/json; charset=utf-8');
	}

	public function index()
	{
		//$this->ParsePlugin->send();
		
	}
	
	public function broadcast(){
		$this->parseplugin->send();
		echo "Sent push notification to 'Everyone'";
	}
	
	public function logout() {
		$session_id = $this->input->post('session_id');
		if (!$session_id) return;
		$status = $this->mobileapp->invalidate_session_by_session_id($session_id);
		return $status;
	}
	
	public function checksession() {
		$session_id = $this->input->post('session_id');
		if (!$session_id) return $this->not_logged_in();
		$user = $this->mobileapp->get_user_by_sessionid($session_id);
		if ((sizeOf($user) < 1) || ($user[0]->session_valid != 1)) return $this->not_logged_in();
	}
	
	
	public function data() {
		
		$session_id = $this->input->post('session_id');
		$old_md5checksum = $this->input->post('md5checksum');
		//$session_id = 'a7b6aca5ac829342edacd9961bc8eea8';
		//$old_md5checksum = '83699f04355a60c1536b3253cfe50b96';
		if (!$session_id) return $this->not_logged_in();
		$user = $this->mobileapp->get_user_by_sessionid($session_id);
		if ((sizeOf($user) < 1) || ($user[0]->session_valid != 1)) return $this->not_logged_in();
		$user_id = $user[0]->user_id;
		
		$data = $this->compiled($user_id);
		$encoded = json_encode($data);
		$md5checksum = md5($encoded);
		
		if ((isset($old_md5checksum) && ($old_md5checksum != ''))  && ($md5checksum == $old_md5checksum)) {
			// Collision! Data is latest, no need to update.
			echo json_encode(array(
				'st' => 1,
				'versionWas' => 'latest'
			));
		} else {
			// Update is needed
			$data['md5checksum'] = $md5checksum;
			$data['st'] = 1;
			$data['versionWas'] = 'not-latest';
			echo json_encode($data);
		}
	}
	
	
	
	public function get_old_md5checksum() {
		//$string = json_encode($this->compiled(1));
		//$string = json_encode('Hello World!');
		//echo md5($string);
	}
	
	
	
	
	public function set_installation_id() {
		$session_id = $this->input->post('session_id');
		$installation_id = $this->input->post('installation_id');
		//var_dump(isset($session_id));die();
		$result = array('st' => 0);
		if ($session_id && $installation_id) {
			$r = json_encode($this->mobileapp->set_installation_id($session_id, $installation_id));
			if ($r) {
				$result['st'] = 1;
			} else {
			}
		} else {
		}
		echo json_encode($result);
	}
	
	public function compiled($user_id) {
		$alerts = $this->alertreminder->show_alert($user_id);
		$reminders = $this->alertreminder->show_reminder($user_id);
		
		$result = array(
			'alerts' => $alerts,
			'reminders' => $reminders,
			'projects' => $this->assess($user_id),
			'lookups' => $this->lookups(),
			'uoms' => $this->uoms()
		);
		
		return $result;
	}
	
	// Lookups
	public function lookups() {
		$result = array();
		foreach($this->assessment->show_lookup_details() as $lookup) {
			if (!isset($result[$lookup->data_set_id])) {
				$result[$lookup->data_set_id] = array();
			}
			array_push($result[$lookup->data_set_id], array(
				'data_set_detail_id' => $lookup->data_set_detail_id,
				'lk_data' => $lookup->lk_data,
				'lk_value' => $lookup->lk_value
			));
		}
		return $result;
	}
	
	// Populate UOMs
	public function uoms() {
		$result = array();
		foreach($this->assessment->show_uom_details() as $uom) {
			$result[$uom->uom_id] = $uom;
		}
		return $result;
	}
	
	public function assess($userid) {
		$search = '';
		$offset = 0;
		$perpage = 10000;
		//$userid = 1;
		$roleid = 3;
		$current_week = Date('W');
		
		$result = array();
		
		//var_dump($this->assessment->get_journal_details_all(784));
		//die();
		
		$project_list = $this->assessment->show_pjde($search,$offset,$perpage,$userid,$roleid);
		foreach ($project_list as $project) {
			
			// Initialize project array for the first time only
			if (!isset($result[$project->project_no]['journals'])) { 
				$result[$project->project_no]['project_no'] = $project->project_no;
				$result[$project->project_no]['project_name'] = $project->project_name;
				$result[$project->project_no]['journals'] = array();
			}
			
			$data_attributes = $this->assessment->get_journal_details_all($project->journal_no);
			$dependency = json_decode($this->assessment->get_journal_dependency($project->journal_no)[0]->dependency, true);
			
			$jda = array();
			foreach ($data_attributes as $da) {
				array_push($jda, $da);
			}
			// Append journals in the project array
			// REMEMBER THAT JOURNALS HAVE DATA ATTRIBUTES TOO. AFTER INITIALIZED ONLY THE DATA ATTRIBUTES WILL GO TO DATA ENTRIES.
			// CREATE A MODEL TO GET JOURNAL DATA ATTRIBUTE FROM  journal_detail
			
			
			$result[$project->project_no]['journals'][$project->journal_no] = array(
				'journal_no' => $project->journal_no,
				'journal_name' => $project->journal_name,
				'dependency' => $dependency,
				'data_entries' => array(),
				'data_attributes' => $jda
			);
			
			
			$data_entries = $this->assessment->show_pjde_id($project->journal_no);
			foreach($data_entries as $entry) {
			
				// Count is the number of data attributes currently assigned to the data entry
				$count=$this->assessment->get_journal_data_entry_detail($entry->data_entry_no);
				
				// Append valid data entry (check with current week and data entry status)
				if ($entry->data_entry_status_id==1 && $entry->frequency_period<=$current_week) {
					// echo json_encode($entry).'<br/>';
					$entry_detail = $this->assessment->show_journal_data_entry($entry->data_entry_no)[0];
					
					$data_entry_no = $entry->data_entry_no;
					$frequency_detail_name = $entry->frequency_detail_name;
					$frequency_period = $entry->frequency_period;
					$is_image = $entry_detail->is_image;
					$dependency = $entry_detail->dependency;
					$data_attributes = [];
					
					// If the count is more than 0, journal is initialized with its data attributes.
					if ($count != 0) $data_attributes = $this->assessment->show_journal_data_entry_detail($entry->data_entry_no);
					//var_dump($data_attributes);
					$result[$project->project_no]['journals'][$project->journal_no]['data_entries'][$entry->data_entry_no] = array(
						'data_entry_no' => $data_entry_no,
						'data_entry_images' => $this->assessment->show_journal_data_entry_picture($data_entry_no),
						'frequency_detail_name' => $frequency_detail_name,
						'frequency_period' => $frequency_period,
						'count' => $count,
						'is_image' => $is_image,
						'dependency' => $dependency,
						'data_attributes' => $data_attributes
					);
				}
			}
			
			// Remove the journals that does not have a data entry ready.
			if (sizeOf($result[$project->project_no]['journals'][$project->journal_no]['data_entries']) < 1) {
				unset($result[$project->project_no]['journals'][$project->journal_no]);
			}
		}
		
		// Remove projects that does not have any journals
		foreach ($result as $r) {
			if (sizeOf($r['journals']) < 1) unset($result[$r['project_no']]);
		}
		
		
		//echo json_encode($result);
		return $result;
		
		//var_dump(Date('W'));
	
	}
	
	public function update_data_entry() {
		$session_id = $this->input->post('session_id');
		if (!$session_id) return $this->not_logged_in();
		$user = $this->mobileapp->get_user_by_sessionid($session_id);
		if ((sizeOf($user) < 1) || ($user[0]->session_valid != 1)) return $this->not_logged_in();
		$user_id = $user[0]->user_id;
		
		
		$data_entry_no = $this->input->post('data_entry_no');
		echo $this->input->post('');
		
		//$data_attributes = $this->input->post('attributes');
		
		
		//$this->assessment->update_journal_data_entry_detail($data_entry_no, $attbid, $value, $user_id);
		
	}
	
	public function add_image($data_entry_no, $userid) {
		$id = $data_entry_no;
		if (!is_dir('journalimage')) {
            mkdir('./journalimage', 0777, true);
        }
        if (!is_dir('journalimage/'.$id)) {
            mkdir('./journalimage/'.$id, 0777, true);
        }
        if (!is_dir('journalimage/'.$id.'/'.$userid)) {
            mkdir('./journalimage/'.$id.'/'.$userid, 0777, true);
        }
		//Configure
		//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
		$config['upload_path'] = 'journalimage/'.$id.'/'.$userid.'/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']=date('dmYHis');
		//load the upload library
		$this->load->library('upload', $config);

	    $this->upload->initialize($config);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('imagedesc', 'Image Description', 'trim|required|xss_clean|max_length[500]');
		$message = array();
		if($this->form_validation->run() == FALSE)
		{
			$message['st'] = 0;
			$message['error'] = 'Please fix the image description';
		}
		else
		{
			//if not successful, set the error message
			if (!$this->upload->do_upload('imagefile'))
			{
				$message['st'] = 0;
				$message['error'] = 'Unable to save image in server';
			}
			else
			{
				$filedetails=$this->upload->data();
				
				//resize the image
				$this->load->library("imageresize",array($config['upload_path'].$filedetails['file_name']));
				$this->imageresize->crop(800, 600);
				$this->imageresize->save($config['upload_path'].$filedetails['file_name']);
				//
				
				$data = array('data_entry_no' => $id,'pict_file_name' => $filedetails['file_name'],'pict_file_path' => '/journalimage/'.$id.'/'.$userid.'/','pict_definition' => $this->input->post('imagedesc'),'pict_user_id' => $userid,'data_source' => '1');
				$this->assessment->add_journal_data_entry_picture($data);
				$this->assessment->add_seq_journal_data_entry_picture($id);
				$message['st'] = 1;
				$message['error'] = '';
			}
		}
		echo json_encode($message);
	}
	
	
	
	
	/***
	Helper functions
	***/
	
	private function error($e) {
		echo json_encode(array('st'=>0, 'error'=>$e));
	}
	
	private function not_logged_in() {
		header("HTTP/1.1 401 Unauthorized");
		return $this->error('Not logged in');
	}
}
