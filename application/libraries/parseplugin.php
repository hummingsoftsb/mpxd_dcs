<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 

NOTE: If you have certificate problems, CA certs might not be included in curl's default installation. To fix try this: http://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate#answer-31830614 

*/

require_once 'application/third_party/parseSDK/autoload.php';
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParsePush;
use Parse\ParseQuery;
use Parse\ParseInstallation;
class ParsePlugin {
	
	var $app_id = 'QgdLLD7HW1T62aepQvW08dz5giIZe8UmQnZS9MZ2';
	var $rest_key = 'N9oaz2V7nBgCkENmcUTWxa1vWUu5dWYz79Gncm4z';
	var $master_key = 'pqf36y8AxfpHqtowNwesd6hR4isUO5F0aYEZNF0i';
	
	function __construct() {
		ParseClient::initialize( $this->app_id, $this->rest_key, $this->master_key );
	}
	
	function send() {
		
		$data = array("alert" => "Hi from Push Notification!");
		
		ParsePush::send(array(
			"channel" => 'Everyone',
			"data" => $data,
			"title" => 'Testingtitle',
			"sound" => ""
		));
	}
	
	function sendMessageByUserId($user_id, $message, $data) {
		$ci =& get_instance();
		$ci->load->model('mobileapp');
		
		$installation_id = $ci->mobileapp->get_installation_id_by_user_id($user_id);
		$this->sendMessageByInstallationId($installation_id, $message, $data);
	}
	
	function sendMessageByInstallationId($installation_id, $message, $data) {
		if (!isset($data)) $data = array();
		$ci =& get_instance();
		$ci->load->model('mobileapp');
		
		$query = ParseInstallation::query();
		$query->equalTo('installationId', $installation_id);
		
		if ($installation_id) {
			$push_data = array('alert' => $message, 'data' => $data);
			ParsePush::send(array(
			  "where" => $query,
			  "data" => $push_data,
			  "sound" => ""
			));
		}
	}
}