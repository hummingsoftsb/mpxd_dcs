<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



if ( ! function_exists('upload_album'))
{
	// Function to upload album, will return the id of the album
    function upload_album($access_token, $title, $timestamp="1152255600000")
    {
		//var_dump($access_token);
		$userid = "106498362119815035474";
		$pem = getcwd().'\secret\cacert.pem';
		
		$ci = & get_instance();
        $data = "<entry xmlns='http://www.w3.org/2005/Atom'
			xmlns:media='http://search.yahoo.com/mrss/'
			xmlns:gphoto='http://schemas.google.com/photos/2007'>
		  <title type='text'>$title</title>
		  <summary type='text'></summary>
		  <gphoto:location></gphoto:location>
		  <gphoto:access>public</gphoto:access>
		  <gphoto:timestamp>$timestamp</gphoto:timestamp>
		  <media:group>
			<media:keywords></media:keywords>
		  </media:group>
		  <category scheme='http://schemas.google.com/g/2005#kind'
			term='http://schemas.google.com/photos/2007#album'></category>
		</entry>";
		
		//$at = json_decode($client->getAccessToken());
		$url = 'https://picasaweb.google.com/data/feed/api/user/'.$userid.'?access_token='.$access_token;
		//$url = 'https://android.googleapis.com/gcm/send';
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', "Content-length: ". strlen($data), "Connection: close"));
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_CAINFO, $pem );
		curl_setopt( $ch, CURLOPT_VERBOSE, true );
		$result = curl_exec($ch);

		if ($result === FALSE) {
			printf("cUrl error (#%d): %s<br>\n", curl_errno($ch), htmlspecialchars(curl_error($ch)));
			curl_close($ch);
			return FALSE;
		}
		curl_close($ch);
		  
		$xml = simplexml_load_string($result);
		
		/*foreach ($xml->entry as $x):
			var_dump($x);
		endforeach;*/
		return end(explode('/',$xml->id));
		
    }
	
	// Uploads multiple photos into album
	function upload_photos_to_album ($access_token, $albumid, $array_of_photos) {
		$userid = "106498362119815035474";
		$basepath = getcwd();
		$pem = $basepath.'\secret\cacert.pem';
		
		$url = "https://picasaweb.google.com/data/feed/api/user/$userid/albumid/$albumid?access_token=$access_token";
		
		/*$array_of_photos = array(
			array(
			"caption" => "This cat is angry",
			"filename" => $basepath."/journalimage/4533/7/13022015085256.jpg",
			),
			array(
			"caption" => "This doge is wonderful",
			"filename" => $basepath."/journalimage/4836/20/1430129684934.jpg",
			)
		);*/
		$count = 0;
		foreach ($array_of_photos as $ap):
		
		$filename = str_replace('/','\\',$ap['filename']);
		$finfo = finfo_open();
		$fileinfo = finfo_file($finfo, $filename, FILEINFO_MIME);
		finfo_close($finfo);
		
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		
		$data = "Media multipart posting
--END_OF_PART
Content-Type: application/atom+xml

<entry xmlns='http://www.w3.org/2005/Atom'>
  <title>".$ap['caption']."</title>
  <summary>".$ap['caption']."</summary>
  <category scheme='http://schemas.google.com/g/2005#kind'
    term='http://schemas.google.com/photos/2007#photo'/>
</entry>
--END_OF_PART
Content-Type: $fileinfo

".$contents."
--END_OF_PART--
";
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/related; boundary="END_OF_PART"', "Content-length: ". strlen($data), "MIME-version: 1.0", "Connection: close"));
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_CAINFO, $pem );
		curl_setopt( $ch, CURLOPT_VERBOSE, true );
		$result = curl_exec($ch);

		if ($result === FALSE) {
			printf("cUrl error (#%d): %s<br>\n", curl_errno($ch), htmlspecialchars(curl_error($ch)));
			curl_close($ch);
			return FALSE;
		}
		curl_close($ch);
		
		$xml = simplexml_load_string($result);
		if (isset($xml->id) && ($xml->id != "")) {
			$count++;
		}
		// To see all links like https://picasaweb.google.com/data/entry/api/user/106498362119815035474/albumid/6193090497884091281/photoid/6193090819465003138
		// print_r($xml->id);
		endforeach;
		
	return array("status"=>true, "upload_count"=>$count);
	}
}