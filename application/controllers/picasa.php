<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI

class Picasa extends CI_Controller 
{
	function __construct()
	{
   		parent::__construct();
   		$this->load->helper(array('url', 'google_helper'));
		$this->load->model('alertreminder','',TRUE);
		$this->load->model('securitys','',TRUE);
	   $this->load->model('assessment','',TRUE);
	   $this->load->model('ilyasmodel','',TRUE);
	   $this->load->model('google_tokens','',TRUE);
	}
	
	function index()
	{	/*
		//$dir = getcwd().'\application\third_party\ZendGdata-1.12.11\demos\Zend\Gdata';
		//$zend = getcwd().'\application\third_party\ZendGdata-1.12.11\library';
		$gapi = getcwd().'\application\third_party\google-api-php-client-master\src';
		//$secret = getcwd().'\secret\MYMRT-4959a2854524.p12';
		
		$clisecret = getcwd().'\secret\client_secret_980973470441-4enk169ngk9j646nbdiqfps8348tmg8u.apps.googleusercontent.com.json';
		
		set_include_path(get_include_path() . PATH_SEPARATOR . $gapi);
		
		include_once("Google\autoload.php");
		
		$token = $this->google_tokens->get_all();
		$tokenstring = json_encode($token);
		
		$client = new Google_Client();
		$client->setAuthConfigFile($clisecret);
		$client->setAccessType('offline');
		$client->addScope('http://picasaweb.google.com/data');
		echo "Setting access token";
		$client->setAccessToken($tokenstring);
		$expired = $client->isAccessTokenExpired();
		
		
		if ($expired) {
			if ($client->getRefreshToken() == null) {
				echo "Getting new refresh token";
				// No refresh token!!
				$client->setRedirectUri('http://mpxddcs.com/index.php/picasa/oauth2callback?testing=lol');
				$auth_url = $client->createAuthUrl();
				header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			} else {
				echo "Getting new access token";
				$client->refreshToken($token['refresh_token']);
				$token = json_decode($client->getAccessToken(), true);
				//var_dump($new);
				$st = $this->google_tokens->set_access_token($token['access_token'], $token['created'], $token['expires_in']);
				//var_dump($st);
			}
		} 
		
		
		$album = upload_album($token['access_token'], "Testing title ITALYYYYY");
		if ($album) {
			upload_photos_to_album($token['access_token'],$album,[]);
		}
		
		//die();
		
		//$client->authenticate($_GET['code']);
		
		//$client->setAssertionCredentials($credentials);
		/*if ($client->getAuth()->isAccessTokenExpired()) {
		  $client->getAuth()->refreshTokenWithAssertion();
		} 
		/*
		$xml = simplexml_load_file("https://picasaweb.google.com/data/feed/api/user/".$userid);
		
		foreach ($xml->entry as $x):
			//print_r($x->entry->title);
			//print_r($x->title);
		endforeach;
		
		

		
		//var_dump( file_exists("License.txt"));
		//include_once($dir.'\Photos.php');
		//phpinfo();
		die();
   		if($this->session->userdata('logged_in'))
   		{
		}
   		else
   		{
     		//If no session, redirect to login page
     		redirect('/login', 'refresh');
   		}*/
	}
	
	function oauth2callback() {
		$dir = getcwd().'\application\third_party\ZendGdata-1.12.11\demos\Zend\Gdata';
		$zend = getcwd().'\application\third_party\ZendGdata-1.12.11\library';
		$gapi = getcwd().'\application\third_party\google-api-php-client-master\src';
		$secret = getcwd().'\secret\MYMRT-4959a2854524.p12';
		$pem = getcwd().'\secret\cacert.pem';
		$clisecret = getcwd().'\secret\client_secret_980973470441-4enk169ngk9j646nbdiqfps8348tmg8u.apps.googleusercontent.com.json';
		$userid = "106498362119815035474";
		set_include_path(get_include_path() . PATH_SEPARATOR . $gapi);
		
		include_once("Google\autoload.php");
		/*
		$client_email = '980973470441-bnm474kvi8i86mmenccvgbhm6hiv7fbi@developer.gserviceaccount.com';
		$private_key = file_get_contents($secret);
		$scopes = array('http://picasaweb.google.com/data');
		$credentials = new Google_Auth_AssertionCredentials(
			$client_email,
			$scopes,
			$private_key
		);*/
		
		$client = new Google_Client();
		
		$client->setAuthConfigFile($clisecret);
		//$client->addScope('http://picasaweb.google.com/data');
		//$client->setRedirectUri('http://picasa/oauth2callback');
		
		header('Content-Type: text/plain');
		$client->authenticate($_GET['code']);
		$access_token = $client->getAccessToken();
		//$refresh_token = $client->getRefreshToken();
		//var_dump($client);
		//die();
		$new = json_decode($access_token);
				//var_dump($new);
		$st = $this->google_tokens->set_access_token($new->access_token, $new->created, $new->expires_in);
		//$st = $this->google_tokens->set_refresh_token($new->refresh_token);
		
		header('Location: /index.php/picasa/upload');
		//var_dump($access_token);
		//var_dump($refresh_token);
	}
	
	function upload() {
	
		$freq_ids = $this->input->get('freq_ids');
		//var_dump($freq_ids);
		//var_dump(explode(',', $freq_ids));
		if ((!$freq_ids)|| ($freq_ids == '')) { 
			$freq_ids = $this->google_tokens->get_temp_freq_id();
			if ($freq_ids == '') die();
		}
		//$dir = getcwd().'\application\third_party\ZendGdata-1.12.11\demos\Zend\Gdata';
		//$zend = getcwd().'\application\third_party\ZendGdata-1.12.11\library';
		$gapi = getcwd().'\application\third_party\google-api-php-client-master\src';
		//$secret = getcwd().'\secret\MYMRT-4959a2854524.p12';
		
		$clisecret = getcwd().'\secret\client_secret_980973470441-4enk169ngk9j646nbdiqfps8348tmg8u.apps.googleusercontent.com.json';
		
		set_include_path(get_include_path() . PATH_SEPARATOR . $gapi);
		
		include_once("Google\autoload.php");
		
		$token = $this->google_tokens->get_all();
		//var_dump($token);
		$tokenstring = json_encode($token);
		
		$client = new Google_Client();
		$client->setAuthConfigFile($clisecret);
		$client->setAccessType('offline');
		$client->addScope('http://picasaweb.google.com/data');
		
		echo "Setting access token";
		$client->setAccessToken($tokenstring);
		$expired = $client->isAccessTokenExpired();
		
			
		if ($expired) {
			echo "EXPIRED";
			if ($client->getRefreshToken() == null) {
				echo "Getting new refresh token";
				$this->google_tokens->set_temp_freq_id($freq_ids);
				// No refresh token!!
				$client->setRedirectUri('http://uoa.hummingsoft.com.my:9090/index.php/picasa/oauth2callback');
				$auth_url = $client->createAuthUrl();
				header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			} else {
				echo "Getting new access token";
				$client->refreshToken($token['refresh_token']);
				$token = json_decode($client->getAccessToken(), true);
				//var_dump($new);
				$st = $this->google_tokens->set_access_token($token['access_token'], $token['created'], $token['expires_in']);
				//var_dump($st);
			}
		} 
		
		
		
		header('Content-Type: text/plain');
		$q = $this->assessment->get_images_to_upload(explode(',', $freq_ids));
		$date_format = 'j-F-Y';
		$today_in_milliseconds = round(microtime(true) * 1000);
		$basepath = getcwd();
		$result = array();
		
		// Loop to generate arrays of image grouped by their album names.
		foreach ($q as $k=>$v):
			$from = date($date_format,strtotime($v->start_date));
			$to = date($date_format,strtotime($v->end_date));
			//var_dump(strtotime($from));
			$album_name = $v->album_name.' '.$from.' to '.$to;
			if (!isset($result[$album_name])) $result[$album_name] = array();
			array_push($result[$album_name], array(
				'caption' => $v->pict_definition,
				'filename' => $basepath . $v->pict_file_path . $v->pict_file_name
			));
			//var_dump($album_name);
			//$result[
		endforeach;
		
		// Loop to upload albums and pictures
		foreach ($result as $k=>$v):
			echo(PHP_EOL.'Uploading '.($k).PHP_EOL);
			$album = upload_album($token['access_token'], $k, $today_in_milliseconds);
			if ($album) {
				$status = upload_photos_to_album($token['access_token'], $album, $v);
			}
			echo "Status: ".$status['status'].PHP_EOL;
			echo $status['upload_count']." images uploaded for album".PHP_EOL;
		endforeach;
		//var_dump($result);
	}
	
	
	
}
?>