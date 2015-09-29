<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('make_login_redirect'))
{
	function make_login_redirect($u) {
		return 'login?back='.base64_encode($u);
	}
}






