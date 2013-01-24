<?php
defined('_JEXEC') or die('Restricted access');
class JController {
	var $error;
	var $fileName = 'Epfc-mab.txt';
	var $localFolder = 'sql';
	var $email = 'dev@epfc.eu';
	var $mailBodyHeader = '<html>
      <head>
       <title>Mise à jour des contacts Thunderbird</title>
      </head>
      <body>';
	var $mailBodyFooter = '</body>
     </html>
     ';
	/* function */
	function display(){
		require('template.php');
		echo $content;
	}
	
	function insertUsers ($users){
		$this->error = new stdClass ;
    	$this->error->state = 0;
    	$this->error->comment = '';
		
    	$database = new database;
		$database->insertUsers($users);    	
    	
	  	if($this->error->state == 1){
	  		echo $this->error->comment;
	  	}else{
	  		echo 'No problemo';
	  	}
		return; 
	}
	function sendToFtp (){
		$headers  = 'MIME-Version: 1.0' . "\r\n";
     	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$ftpFolder = '';
	    $ftp = new ftp;
	    $upload = $ftp->store($ftpFolder, $this->localFolder, $this->fileName);
	    if($upload->error == 1){
	    	$this->error->state = 1;
	    	//echo $upload->comment;
	    	$subject = '[Thunderbird-mab] error';
	    	//$message = '<html><head><title>Un titre ici</title></head><body>';
	  		$message = 'Oups le fichier n\'a pas pu être uploadé, c\'est assez gênant. '." \n";
	  		$message .= 'Le fichier utilisé est create-sql-mab.php'." \n";
	  		//$message .= '</body></html>'; 
	  		$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
	  		mail($this->email, $subject, $mailBody, $headers);
	  		$this->error->comment = $message;
	  		throw new Exception('72');
	  		//echo $message;
	  		//return false;
	    }else{
	    	$subject = '[Thunderbird-mab] okay';
	  		$message = 'Yeah, baby le fichier a été uploadé, tu peux rentrer chez toi. '." \n";
	  		$message .= 'Bonne journée.';
	  		echo $message;
	    }
	}
	/*
     * insert sql into extranet database
     */
	//renvoi toute la page
	function getUsers(){
		$headers  = 'MIME-Version: 1.0' . "\r\n";
     	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		$subject ='';
		$message = '';
		$date = date("m.d.Y");
  		$string = 'petitevoiture';
  		$token = md5($date.$string);
  		$url = 'http://localhost/joomla-1.73/index.php?option=com_helloworld&view=users&format=xml&token='.$token;
  		//echo 'houhou';
  		// create a new cURL resource
	  	$ch = curl_init();
	
	  	// set URL and other appropriate options
	  	curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	  // grab URL and pass it to the browser
	  	//$return = curl_exec($ch);
	  	if( ! $getXml = curl_exec($ch)) {
	  		//echo 'fail curl';
	  		$this->error->state = 1;
	  		$this->error->comment = 'fail curl';
	  		throw new Exception('fail curl');
	  	}
		// Check if any error occured
		if(curl_errno($ch))
		{
		    $subject = '[Thunderbird-mab] error';
			$message .= 'Curl error: ' . curl_error($ch);
			$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
			//mail($this->email, $subject, $mailBody, $headers);
			$this->error->state = 1;
	  		$this->error->comment = $message;
	  		throw new Exception('129');
		}
		$users = simplexml_load_string($getXml);
		//echo '<pre>'.print_r($users,true).'</pre>';
		
		/*if($result == '1'){
			$subject = '[Thunderbird-mab] done';
			$message .= 'Curl okay: ';
			$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
			//mail($this->email, $subject, $mailBody, $headers);
			//echo $message;
		}else{
			$subject = '[Thunderbird-mab] error';
			$message .= 'Create mab '.$result;
			$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
			//mail($this->email, $subject, $mailBody, $headers);
			$this->error->state = 1;
	  		$this->error->comment = $message;
	  		throw new Exception('142');
		}*/
	  	// close cURL resource, and free up system resources
	  	curl_close($ch);
		return $users;
	  	//echo $message;
	}
}
?>