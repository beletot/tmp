<?php
$to = 'sehubin@epfc.eu';
if (!preg_match("#sehubin|vaberckmans|support#", $to))
		{
			echo 'VRAI';
		}
			else
		{
			echo 'FAUX';
		}
die();

/*
 * <a target="_blank" href="http://www.ulb.ac.be/enseignements/cours-preparatoires/coursprep-6.html" class="icon-external">site de l'ULB</a>
 */

/*
 * define for localhost
 */
/*define('DB_SERVER', 'localhost');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_DATABASE','extranet-1.6');*/

/*
 * define for ovh prod
 */
define('DB_SERVER', 'mysql5-3.bdb');
define('DB_USER','epfckkdq-ext');
define('DB_PASSWORD','m8Sf4Pjxg');
define('DB_DATABASE','epfckkdq-ext');

class database {
	var $db = null;
	
	/*
	 * init connection
	 */
	function __construct() {
		$this -> db = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die("erreur de connexion base 1");
		mysql_select_db(DB_DATABASE, $this -> db) or die("erreur de connexion base 2");
	}

	/*
	 * get content
	 * @return array with object
	 */
	function getContent() {
		$rows = '';	
		$result = mysql_query("SELECT `id`, `introtext`, `fulltext`	FROM `jos_content` WHERE `introtext` LIKE '%plugins/filemanager%' ");
		//exit();
		if (!$result) {
			die('Requête invalide : ' . mysql_error());
		}
		while ($row = mysql_fetch_object($result)) {
			echo '<pre>' . print_r($row, true) . '</pre>';
			$rows[] = $row;
			//fputcsv($fp, $row, ';', '"');
		}
		return $rows;
	}

	/*
	 * update table
	 */
	function updateContent($rows) {
		$count = 0;
		foreach ($rows as $row) {
			$row->introtext = mysql_real_escape_string($row->introtext);
			$query = "UPDATE `jos_content` SET `introtext` = '$row->introtext' WHERE `id` = $row->id";
			//echo $query.'<hr>';
			$result = mysql_query($query);
			if (!$result) {
				die('Requête invalide : ' . mysql_error());
			}
			$count++;
		}
		return $count;
		
	}

	function __destruct() {
		//print "Destruction de " . $this->name . "\n";
	}

}

class str {
	function replace($rows = '') {
		//vérifier pour icon html
		$toreplace = array(
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" />',
			'<img style="border: 0px; vertical-align: middle;" class="jce_icon" alt="pdf" src="../plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" />',
			'<img class="jce_icon" style="border: 0px none; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/doc_small.gif" alt="doc" />',
			'<img class="jce_icon" style="border: 0px none; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/doc_small.gif" alt="doc" width="16" height="16" />',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/html_small.gif" alt="html" />',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/def_small.gif" ',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/doc_small.gif" alt="doc" />',
			'src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/jpg_small.gif"',
			'src="../plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/jpg_small.gif"',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/doc_small.gif" alt="doc" />',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" />',
			'<img class="jce_icon" style="border: 0px none; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" />',
			'<img class="jce_icon" style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" width="16" height="16" />',
			'<img class="jce_icon" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" style="border-style: initial; border-color: initial; vertical-align: middle; padding: 10px;" />',
			'<img class="jce_icon" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" style="border-style: initial; border-color: initial; vertical-align: middle;" />',
			'<a style="border: 0px; vertical-align: middle;" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" alt="pdf" class="jce_icon"',
			'<img style="border: 0px; vertical-align: middle;" class="jce_icon" alt="pdf" src="http://extranet.epfc.eu/plugins/editors/jce/tiny_mce/plugins/filemanager/img/ext/pdf_small.gif" />'
			);
		$replaceBy = array(
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/doc.png" style="border: 0px none; vertical-align: middle;" alt="doc" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/doc.png" style="border: 0px none; vertical-align: middle;" alt="doc" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/html.png" style="border: 0px none; vertical-align: middle;" alt="html" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/html.png" style="border: 0px none; vertical-align: middle;" ',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/doc.png" style="border: 0px none; vertical-align: middle;" alt="doc" />',
			'src="http://extranet.epfc.eu/media/jce/icons/jpg.png"',
			'src="http://extranet.epfc.eu/media/jce/icons/jpg.png"',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/doc.png" style="border: 0px none; vertical-align: middle;" alt="doc" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />',
			'<a ',
			'<img class="wf_file_icon" src="http://extranet.epfc.eu/media/jce/icons/pdf.png" style="border: 0px none; vertical-align: middle;" alt="pdf" />'
			);
		foreach ($rows as $row) {
			$row -> introtext = str_replace($toreplace, $replaceBy, $row -> introtext);
			$return[] = $row;
		}
		return $return;
	}

}

$db = new database();
$rows = $db -> getContent();
if($rows){
	$update = str::replace($rows);
	//$update = $rows;
	$count = $db->updateContent($update);
	echo $count;
}else{
	echo 'nothing to change';
}


//echo '<pre>' . print_r($update, true) . '</pre>';
?>