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
	
	function writeSql (){
		$this->error = new stdClass ;
    	$this->error->state = 0;
    	$this->error->comment = '';
		
    	$database = new database;
    	$output = 'TRUNCATE TABLE `jos_directory_group_user`; '." \n";
    	
    	$writeSql = new writeSql;
		
		$returnGetEverybody = $database->getEverybody();
		$output .= $writeSql->insert($returnGetEverybody);
		
    	$returnGetAdministratifs = $database->getAdministratifs();
		$output .= $writeSql->insert($returnGetAdministratifs);
		
		$returnGetDirecteurs = $database->getDirecteurs();
		$output .= $writeSql->insert($returnGetDirecteurs);
		
		$returnGetSousDirecteurs = $database->getSousDirecteurs();
		$output .= $writeSql->insert($returnGetSousDirecteurs);

		$returnGetExpert = $database->getExpert();
		$output .= $writeSql->insert($returnGetExpert);
		
		$returnGetExpertPedagogiqueTechnique = $database->getExpertPedagogiqueTechnique();
		$output .= $writeSql->insert($returnGetExpertPedagogiqueTechnique);
		
		// building
		$returnGetBuilding = $database->getBuilding('CAM');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('CR1');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('CR2');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('BREL');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('STA');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('ST3');
		$output .= $writeSql->insert($returnGetBuilding);
		$returnGetBuilding = $database->getBuilding('WSP');
		$output .= $writeSql->insert($returnGetBuilding);
		
		$returnGetProfesseursL = $database->getProfesseurs('L');
		$output .= $writeSql->insert($returnGetProfesseursL);
		$returnGetProfesseursE = $database->getProfesseurs('E');
		$output .= $writeSql->insert($returnGetProfesseursE);
		
    	//ne pas utiliser / véronique
    	//$output .= $database->getBuilding('NIV');
		//$output .= $database->getProfesseurs('L')." \n";
		//$output .= $database->getProfesseurs('E')." \n";
			//$output .= $database->getBuilding('SJO')." \n";
		
		$returnGetSecretaireCentre = $database->getSecretaireCentre();
		$output .= $writeSql->insert($returnGetSecretaireCentre);
		$returnGetPrepensionne = $database->getPrepensionne();
		$output .= $writeSql->insert($returnGetPrepensionne);
		$returnGetConge = $database->getConge();
		$output .= $writeSql->insert($returnGetConge);
		
		//dev
		/*$output .= $database->getSecretaireCentre();
		$output .= $database->getPrepensionne();
		$output .= $database->getConge();*/
		
		/*$returnGetExterne = $database->getExterne();
		$output .= $writeSql->insert($returnGetExterne);*/
		$returnGetPromofor = $database->getPromofor();
		$output .= $writeSql->insert($returnGetPromofor);
		$returnGetService = $database->getService();
		$output .= $writeSql->insert($returnGetService);
		
		
    	//need to change the database
    	//$output .= $database->getExterne();
    	//$output .= $database->getInscripteur();
    	/*$output .= $database->getPromofor();
    	$output .= $database->getService();*/
		
		/*** write output into a txt file ***/
	  	$filePath = $this->localFolder.DS.$this->fileName;
	  	$writeFile = file_put_contents($filePath, $output);
	  	
	  	if(!$writeFile){
	  		$this->error->state = 1;
	  		$message = 'Oups le fichier n\'a pas pu être créé, c\'est assez gênant. '." \n";
	  		$message .= 'Le fichier utilisé est create-sql-mab.txt'." \n";
			$this->error->comment .= $message;
			throw new Exception('62');
	  	}
	  	if($this->error->state == 1){
	  		echo $this->error->comment;
	  	}else{
	  		echo 'No problemo';
	  	}
		return $output; 
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
	function insertSql(){
		$headers  = 'MIME-Version: 1.0' . "\r\n";
     	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		$subject ='';
		$message = '';
		$date = date("m.d.Y");
  		$string = 'petitevoiture';
  		$token = md5($date.$string);
  		$url = 'http://extranet.epfc.eu/index.php?option=com_mab&format=raw&token='.$token;
  		//echo 'houhou';
  		// create a new cURL resource
	  	$ch = curl_init();
	
	  	// set URL and other appropriate options
	  	curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	  // grab URL and pass it to the browser
	  	//$return = curl_exec($ch);
	  	if( ! $result = curl_exec($ch)) {
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
			mail($this->email, $subject, $mailBody, $headers);
			$this->error->state = 1;
	  		$this->error->comment = $message;
	  		throw new Exception('129');
		}
		if($result == '1'){
			$subject = '[Thunderbird-mab] done';
			$message .= 'Curl okay: ';
			$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
			mail($this->email, $subject, $mailBody, $headers);
			//echo $message;
		}else{
			$subject = '[Thunderbird-mab] error';
			$message .= 'Create mab '.$result;
			$mailBody = $this->mailBodyHeader.$message.$this->mailBodyFooter;
			mail($this->email, $subject, $mailBody, $headers);
			$this->error->state = 1;
	  		$this->error->comment = $message;
	  		throw new Exception('142');
		}
	  	// close cURL resource, and free up system resources
	  	curl_close($ch);
	  	//echo $message;
	}
}
?>