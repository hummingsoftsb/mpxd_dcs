<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//load the helper
$ci = &get_instance();
$ci->load->helper('form');

$is_mobile = ($ci->input->post('ismobile') == 1);

$id = $ci->input->post('dataentryno1');

$picvalcomment = trim($ci->input->post('val_comment'));
$seq_no = $ci->input->post('seq_no');
// $pic_vali_comment = $picvalcomment;
if (!empty($picvalcomment)) {
    $pic_vali_comment = $picvalcomment;
} else {
    $pic_vali_comment = "";
}
$session_data = $ci->session->userdata('logged_in');
//if ($is_mobile) $userid = $user_id;
$userid = $session_data['id'];

if (!is_dir('journalimage')) {
    mkdir('./journalimage', 0777, true);
}
if (!is_dir('journalimage/' . $id)) {
    mkdir('./journalimage/' . $id, 0777, true);
}
if (!is_dir('journalimage/' . $id . '/' . $userid)) {
    mkdir('./journalimage/' . $id . '/' . $userid, 0777, true);
}
//Configure
//set the path where the files uploaded will be copied. NOTE if using linux, set the folder to permission 777
/*$config['upload_path'] = 'journalimage/'.$id.'/'.$userid.'/';

$config['allowed_types'] = 'gif|jpg|png';
$config['file_name']=date('dmYHis');

*/

//var_dump(APPPATH);
header('content-type: application/json');
$ci->load->library('uploadhandler', array(
    'upload_dir' => 'journalimage/' . $id . '/' . $userid . '/',
    'upload_url' => 'journalimage/' . $id . '/' . $userid . '/'
));
//$ci->uploadhandler->set_upload_path('journalimage/'.$id.'/'.$userid.'/');

/*var_dump($_FILES);
var_dump($_POST);
var_dump($_GET);
var_dump($ci->uploadhandler);*/

$file = $_FILES['file'];
$filename = $file['name'];
$filesize = $file['size'];
$filtered_name = str_replace(".", "_", $filename);


if ($is_mobile) {
    $descname = 'description';
} else {
    $descname = 'imagedesc_' . $filtered_name . '_' . $filesize;
}
//var_dump($descname, $file);

// replaced the description with regular expression :Agaile 14/12/2015
//$description = trim($ci->input->post($descname));
$str = $ci->input->post($descname);
$description = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $str)));


//var_dump($_POST, $_FILES, $descname, $description);


/*foreach ($ci->uploadhandler->image_objects as $k=>$v):
    $filepath = $k;
endforeach;*/
//var_dump($ci->uploadhandler);
$file_path = $ci->uploadhandler->get_upload_path();
$actual_file_pathname = $ci->uploadhandler->get_path_and_name();
$exploded_path = explode('/', $actual_file_pathname);
$actual_filename = $exploded_path[sizeOf($exploded_path) - 1];
$is_error = isset($ci->uploadhandler->response['file'][0]->error);

// Check uploaded names for duplicates
if ($is_mobile) {
    $result = $ci->assessment->check_image_unique($filename);

    if (sizeOf($result) > 0) {
        // Update description first before quitting
        $data = array(
            'pict_definition' => $description
        );
        $ci->assessment->update_journal_data_entry_picture($data, $result[0]->data_entry_pict_no);
        echo json_encode(array(
            "files" => array(array("error" => "Duplicate image"))));
        return;
    }
}

//var_dump($ci->uploadhandler, $actual_file_pathname);
//die();
//var_dump($actual_filename);
//die();

$ci->load->library('form_validation');
$ci->form_validation->set_rules($descname, 'Image Description', 'trim|required|xss_clean|max_length[500]');

// File was probably not a recognized as image by gd, should err.
if ($actual_file_pathname == "") {
    echo json_encode(array(
        "files" => array(array("error" => "Unrecognised file type"))));
} // File was erred by uploadhandler.
else if ($is_error) {
    echo json_encode($ci->uploadhandler->response);
} // Error caused by image description
else if ($ci->form_validation->run() == FALSE) {
    //echo json_encode(array('st'=>0, 'msg' => '','msg1'=>form_error('imagedesc')));
    $sess_array = array(/*'message' => "Upload failed.".form_error('imagedesc'),"type" => 0,*/
        "files" => array(array("error" => "Image description error"))
    );
    //$ci->session->set_userdata('message', $sess_array);
    unlink($actual_file_pathname);
    echo json_encode($sess_array);
    //redirect('/journaldataentryadd?jid='.$id,'refresh');
} // Success
else {
    //$filedetails=$ci->upload->data();

    //resize the image
    $ci->load->library("imageresize", array($actual_file_pathname));
    $ci->imageresize->crop(800, 600);
    $ci->imageresize->save($actual_file_pathname);
    //
    if (!empty($pic_vali_comment)) {
        $data = array('data_entry_no' => $id, 'pict_seq_no' => $seq_no, 'pict_file_name' => $actual_filename, 'pict_file_path' => $file_path, 'pict_definition' => $description, 'pict_user_id' => $userid, 'data_source' => '1', 'pict_validate_comment' => $pic_vali_comment);
    } else {
        $data = array('data_entry_no' => $id, 'pict_seq_no' => $seq_no, 'pict_file_name' => $actual_filename, 'pict_file_path' => $file_path, 'pict_definition' => $description, 'pict_user_id' => $userid, 'data_source' => '1');
    }
    if ($is_mobile) $data['unique_id_mobile'] = $filename;
    if (!empty($data['pict_seq_no'])) {
        $ci->assessment->add_journal_data_entry_picture($data);
    } else {
        $ci->assessment->add_journal_data_entry_picture($data);
        $ci->assessment->add_seq_journal_data_entry_picture($id);
    }
    /*$result=$ci->assessment->show_journal_data_entry_picture($id);
    $value='';
    foreach($result as $row)
    {
        $value .=$row->pict_seq_no.','.$row->pict_file_path.','.$row->pict_file_name.','.$row->pict_definition.','.$row->data_entry_no.',777,';
    }
    echo json_encode(array('st'=>1, 'msg' => 'Success','imgval'=>$value));*/
    //$sess_array = array('message' => "Picture Attached to the Journal","type" => 1);
    //$ci->session->set_userdata('message', $sess_array);
    $response = $ci->uploadhandler->response;
    $response['file'][0]->description = $description;
    echo json_encode($response);
    //redirect('/journaldataentryadd?jid='.$id,'refresh');
}
