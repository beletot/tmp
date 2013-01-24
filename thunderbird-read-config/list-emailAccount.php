<?php
$desc = "Liste les dossiers utilisés sur le serveur pour Firefox et Thunderbird";

/*
 * list email account for one user
/*
 * browser.cache.disk.capacity", 51200 -> 50mo
 * user_pref("mail.prompt_purge_threshhold", false); -> ne vérifie pas si le compactage des dossiers peut être efficace
 * user_pref("mail.purge_threshhold_mb", 10); vérifie si le compactage des dossiers peut nous faire gagner 10mo
 */ 
 /*
  * si le dossier Thunderbird ou firefox n'existe pas.
  * compter le nb de boite email
  * vérifier les carnets d'adresses
  * réaliser une boucle sur le listing des dossiers
  */
 
 /*
  * user_pref("mail.root.imap", "Q:\\bletot\\Thunderbird\\ImapMail");
user_pref("mail.root.imap-rel", "[ProfD]ImapMail");
user_pref("mail.root.none", "Q:\\bletot\\Thunderbird\\Mail");
user_pref("mail.root.none-rel", "[ProfD]Mail");
user_pref("mail.root.pop3", "Q:\\bletot\\Thunderbird\\Mail");
user_pref("mail.root.pop3-rel", "[ProfD]Mail");
  * 
  * //rechercher  l'id du dossier et réaliser un str replace sur le login
  * après changer le nom du dossier sur le serveur
  * laisser le chemin d'accès serveur dynamique, pouvoir passer de epfc01afs01 vers un autre serveur
  * mail.identity.id1
  * 
  * checking extension
  * user_pref("extensions.enabledAddons", "addressbookssync@ggbs.de:1.0.4,googlecalendartab@momo:3.9,{972ce4c6-7e08-4474-a285-3208198ce6fd}:10.0.2");
  * l'adresse est toujours celle ci
  * user_pref("extensions.googlecalendartab@momo.url", "http://calendar.google.com/a/epfc.eu/");  
  * 
  * user_pref("mail.smtpserver.smtp1.description", "beletot.epfc.eu");
user_pref("mail.smtpserver.smtp1.hostname", "smtp.gmail.com");
user_pref("mail.smtpserver.smtp1.port", 465);
user_pref("mail.smtpserver.smtp1.try_ssl", 3);
user_pref("mail.smtpserver.smtp1.username", "beletot@epfc.eu");
  * */
 
class JFolder {
	/*
	 * get content of a folder
	 * TODO utilité de $display
	 * @param	bolean		$display	to see the content folder
	 */
	public function getFolder($display) {
		//import();
		//$pathFile = '\\\EPFC01AFS01\\root\\AppData\\bletot\\Thunderbird\\prefs.js';
		$path = '\\\EPFC01AFS01\\root\\AppData';
		//not working -> $path = 'Q:\\AppData\\bletot\\Thunderbird\\';

		// Is the path a folder?
		if (!is_dir($path)) {
			echo 'Le dossier '.$path.' n\'est pas lisible';
			return false;
		}
		$filter = '.';
		$recurse = false;
		$full = false;
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX');
		$excludefilter = array('^\..*', 'Prof');
		$findfiles = false;

		// Compute the excludefilter string
		if (count($excludefilter)) {
			$excludefilter_string = '/(' . implode('|', $excludefilter) . ')/';
		} else {
			$excludefilter_string = '';
		}

		// Get the folders
		//$full, $exclude, $excludefilter_string, false

		// Sort the folders
		//copy of the function Jfolder::_items who is protected
		@set_time_limit(ini_get('max_execution_time'));

		// Initialise variables.
		$arr = array();

		// Read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false) {
			if ($file != '.' && $file != '..' && !in_array($file, $exclude) && (empty($excludefilter_string) || !preg_match($excludefilter_string, $file))) {
				// Compute the fullpath
				$fullpath = $path . '\\' . $file;
				//echo $fullpath.'<br />';

				// Compute the isDir flag
				$isDir = is_dir($fullpath);

				if (($isDir xor $findfiles) && preg_match("/$filter/", $file)) {
					// (fullpath is dir and folders are searched or fullpath is not dir and files are searched) and file matches the filter
					if ($full) {
						// Full path is requested
						$arr[] = $fullpath;
					} else {
						// Filename is requested
						$arr[] = $file;
					}
				}
				if ($isDir && $recurse) {
					// Search recursively
					if (is_integer($recurse)) {
						// Until depth 0 is reached
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse - 1, $full, $exclude, $excludefilter_string, $findfiles));
					} else {
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles));
					}
				}
			}
		}
		closedir($handle);
		asort($arr);
		if($display == true){
			JView::userFolder($arr);
		}
		return array_values($arr);
	}

}

class JFile {
	public function read($user) {
		
		if(!$user){return false;}
		$pathFile = '\\\EPFC01AFS01\\root\\AppData\\' . $user . '\\Thunderbird\\prefs.js';
		//$pathFile = '\\\EPFC01AFS01\\root\\AppData\\' . $user . '\\Firefox\\prefs.js';
		if (!is_file($pathFile)) {
			$msg = 'Nous ne parvenons pas à ouvrir le fichier de config '.$pathFile;
			echo $msg;
			exit;
		}
		$lines = file($pathFile);
		// Affiche toutes les lignes du tableau comme code HTML, avec les numéros de ligne
		//foreach ($lines as $line_num => $line) {
			//echo $line . "<br />\n";
		//}
		return $lines;
	}
}

class JConfig {
	/*array select key and value to to check
	 * 'browser.cache.disk.capacity' => 51200,
	 */
	
	var $filter;
	
	public function getLines($lines){
		foreach($lines as $line){
			$row = '';
			if($row = $this->getLine($line)){
				$rows[] = $row;
				//echo '<pre>'.print_r($row,true).'</pre>';
			}
			
		}
		//echo '<pre>'.print_r($rows,true).'</pre>';
		return $rows;
	}	
	public function getLine($line) {
		$row = new stdClass;
		$position = '';
		//$line = 'user_pref("mail.identity.id1.useremail", "beletot@epfc.eu");';
		$toReplace = array('(', '"', ',', ' ');
		//checking to find user email account
		if (preg_match('#\("mail.identity.id[0-9]+.useremail"#i', $line, $matches)) {
			//sanitase key, not really needed hear
			$key = str_replace($toReplace, "", $matches[0]);	
			$row->key = $key;
			//we ve got the key, then we need the value
			preg_match('#"[a-z.-]+@[a-z.]+"#i', $line, $value) ;
			if($value[0]){
				$row->value = $value[0];
			}else{
				$row->error = 1;
				$row->value = $line;
			}
			//$row->value = $line;
			return $row;
		}else{
			$row->error = 1;
			$row->comment = 'No email account';
		}
		return;
	}

}
class JView {
	public function header(){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-fr" lang="fr-fr" dir="ltr" >
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex" />
  <meta name="keywords" content="" />
  <meta name="rights" content="" />
  <meta name="language" content="fr-FR" />
  <meta name="generator" content="" />
  <title>Accueil</title>
	</head>
	<body>
		<?php
	}
	public function userFolder($rows){
		echo '<ul>';
		foreach($rows as $key => $row){
			echo '<li><a href="list-emailAccount.php?userId='.$key.'" >'.$row.'</a></li>';
		}
		echo '</ul>';
	}
	public function display($content){
		echo $content;
	}
	public function footer(){
		?>
	</body>
		<?php
	}
}

JView::header();
$folderList = Jfolder::getFolder(true);
@$userId = $_GET['userId'];
if(!$userId){
	exit;
}


$lines = JFile::read($folderList[$userId]);
$config = new JConfig;
//Thunderbird
/*$config->filter = array(
	'mail.identity.id1.useremail' => ''
);*/
$listConfig = $config->getLines($lines);



$content = '<div style="position:fixed;left:300px;top:0;border:1px solid #cccccc;padding:5px;margin-top:10px;">';
$content .= '<h1>'.$folderList[$userId].'</h1>';
$content.= '<pre>'.print_r($listConfig,true).'</pre>';
$content .= '</div>';
$content .= '<div style="position:fixed;width: 20%;right:50px; top: 1em;border:1px solid #cccccc;padding:5px;margin-top:10px;">'.$desc.'</div>';
JView::display($content);
JView::footer();
?>