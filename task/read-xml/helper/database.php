<?php
/*
 * vérifier les différents groupes pour les bâtiments
 * Belataris Saliha est en "non subventionné"
 * M'aaddi Sarah est dans autres
 */
/*
 * rajouter un destruct à la fin des queries
 */
class database {
	protected $message;
	protected $db;
	
	//function __construct(){
	function __construct(){
		$return = new stdClass ;
        $return->error = 0;
        $return->comment = '';
		//$path	= 'isis:c:\epfc1112.fdb';
		$path	= 'zeus:j:\epfc1112Test.fdb';
		$this->db = ibase_connect($path, "sysdba", "epfccfpe");
		if(!$this->db){
			$return->error = 1;
        	$return->comment = "Impossible de se connecter : " . mysql_error();
        	echo $return->comment;
        	//return $return;
		}
		//return $return;
	}
	function createReturn ($query){
		echo $query;
		//die();
		$sth = ibase_query($this->db, $query) or die(ibase_errmsg());
	}
	/*
	 * get all - everybody
	 */
	function insertUsers($users){
		$return = new stdClass;
		$query = 'DELETE FROM W_EXT_USERS; ';
		//echo '<pre>'.print_r($user,true).'</pre>';
		foreach ($users as $user) {
			//echo '<pre>'.print_r($user,true).'</pre>';
			$query .= "INSERT INTO  
	  		W_EXT_USERS
	  		(ID,LOGIN,NAME,FIRSTNAME)
			VALUES
	  		($user->id, '$user->username','$user->name','$user->name'); ";
		}
		return $this->createReturn ($query);
	}
	/***	getdirecteurs	***/
	function getDirecteurs (){
		$return = new stdClass;
		
		$idGroup = $this->listGroup['Directeurs'];
		$query = "SELECT DISTINCT
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= " P.DIRECTEUR LIKE 'T' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/***	getSousDirecteurs	***/
	function getSousDirecteurs (){
		$return = new stdClass;
		
		$idGroup = $this->listGroup['Sous-Directeurs'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= " P.SS_DIR LIKE 'T' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * liste des professeurs mais normalement annuelle
	 * vérifier le filtre prof like F
	 */
	function getExpert(){
		$return = new stdClass;
		
		$idGroup = $this->listGroup['Expert'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "P.EXPERT LIKE 'T' AND ";
		$query .= "P.PROF_L LIKE 'F' AND ";
		$query .= "P.PROF_E LIKE 'F' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
/*
	 * liste à terminer
	 * config pour les experts
	 */
	function getExpertPedagogiqueTechnique(){
		$return = new stdClass;
		
		$idGroup = $this->listGroup['ExpertPedagogiqueTechnique'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "P.EXP_PEDAG_TECH LIKE 'T' ";
		//$query .= "P.PROF_L LIKE 'F' AND ";
		//$query .= "P.PROF_E LIKE 'F' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * $building / see $listGroup
	 */
	function getBuilding($building){
		$return = new stdClass;
		
	  $icount = 0;
	  $keyExist = array_key_exists($building, $this->listGroup);
	  if(!$building || $keyExist == false){
	    //gestion du log d'erreurs
	    return;
	  }
	  $idGroup = $this->listGroup[$building];
		$query = "SELECT
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
			/* exclus un admin qui est prof
			$query .= "P.PROF_L LIKE 'F' AND ";
			$query .= "P.PROF_E LIKE 'F' ";*/
	   		$query .= " ( P.DIRECTEUR LIKE 'T' OR ";
	   		$query .= " P.SS_DIR LIKE 'T' OR ";
	   		$query .= " P.SEC_DIR LIKE 'T' OR ";
	   		$query .= " P.EDUC_ECONOME LIKE 'T' OR ";
	   		$query .= " P.SURV_EDUC LIKE 'T' OR ";
	   		$query .= " P.EXP_PEDAG_TECH LIKE 'T' OR ";
	   		$query .= " P.NON_SUBVENTIONNE LIKE 'T' OR ";
	   		$query .= " P.AUTRES LIKE 'T' )";
		 	//$query .= "AND P.SIEGE_PRINCIPAL LIKE '".$building."' ";
		 	$query .= "AND P.SIEGE_PRINCIPAL LIKE '".$building."' ";
		 	$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * listing prfesseurs type e ou l
	 */
	function getProfesseurs ($type){
		$return = new stdClass;
	  $keyExist = array_key_exists($type, $this->listGroup);
	  if(!$type || $keyExist == false){
	    //gestion du log d'erreurs
	    return;
	  }
	  $idGroup = $this->listGroup[$type];
	  switch ($type) {
	    case 'L':
	        $where = "P.PROF_L LIKE 'T' ";
	        break;
	    case 'E':
	        $where = "P.PROF_E LIKE 'T' ";
	        break;
	  }
	  
	  $query = "SELECT 
	      p.ID_EXTRANET
	    FROM
	      PERSONNE p
	    WHERE ";
	  $query .= $where;
	  $query .= "
	        AND p.CODE_ACTIVITE IN ".$this->codeActivite."
	      AND p.EMAIL NOT LIKE '' ";
	   //$query .= " AND P.SIEGE_PRINCIPAL LIKE 'WSP' ";
	  $query .= " ORDER BY p.ID_EXTRANET ASC ";
	  return $this->createReturn ($query, $idGroup);
	}
	/*
	 * getSecretary
	 * insert manual by id user
	 * don't know if the user still exist
	 */
	function getSecretaireCentre (){
		$return = array();
		$idGroup = $this->listGroup['SecretaireCentre'];
		//1171	Cédric Vanvelthem
		$idUser = array(	
			483,
			488,
			501,
			725,
			776,
			826,
			865,
			1171,
			1305,
			1308,
			1353,	
		);
		
	    foreach($idUser as $id){
	        $row = new stdClass;
	        $row->idPers = $id;
	        $row->idGroup = $idGroup; 
	        $return[] = $row;
	        //echo '<pre>'.print_r($row,true).'</pre>';
	    }
	    return $return; 
	}
	/*
	 * get all prépensionné
	 */
	function getPrepensionne (){
		$return = array();
		
		$idGroup = $this->listGroup['Prepensionne'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "
	       	p.CODE_ACTIVITE LIKE 'DP'
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * get user in Holiday
	 */
	function getConge (){
		
		$idGroup = $this->listGroup['Conge'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "
	       	p.CODE_ACTIVITE LIKE 'C'
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	
	/*
	 * get Externe, like chronoCopy
	 */
	function getExterne(){
		$idGroup = $this->listGroup['Externe'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "P.EXTERNE LIKE 'T' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	
	/*
	 * get inscripteur
	 */
	function getInscripteur(){
		$idGroup = $this->listGroup['Inscripteur'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "P.INSCRIPTEUR LIKE 'T' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ('A' , 'AN' , 'DT', 'DP')
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * get promofor
	 */
	function getPromofor(){
		$idGroup = $this->listGroup['Promofor'];
		$query = "SELECT 
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
		$query .= "P.PROMOFOR LIKE 'T' ";
		$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
		return $this->createReturn ($query, $idGroup);
	}
	/*
	 * l'ensemble des administratifs
	 */
	function getAdministratifs(){
	  $idGroup = $this->listGroup['Administratifs'];
		$query = "SELECT
	  		p.ID_EXTRANET
			FROM
	  		PERSONNE p
			WHERE ";
			/* exclus un admin qui est prof
			$query .= "P.PROF_L LIKE 'F' AND ";
			$query .= "P.PROF_E LIKE 'F' ";*/
	   		$query .= " ( P.DIRECTEUR LIKE 'T' OR ";
	   		$query .= " P.SS_DIR LIKE 'T' OR ";
	   		$query .= " P.SEC_DIR LIKE 'T' OR ";
	   		$query .= " P.EDUC_ECONOME LIKE 'T' OR ";
	   		$query .= " P.SURV_EDUC LIKE 'T' OR ";
	   		$query .= " P.EXP_PEDAG_TECH LIKE 'T' OR ";
	   		$query .= " P.NON_SUBVENTIONNE LIKE 'T' OR ";
	   		$query .= " P.AUTRES LIKE 'T' )";
		 	$query .= "
	       	AND p.CODE_ACTIVITE IN ".$this->codeActivite."
			AND p.EMAIL NOT LIKE ''
	      	ORDER BY p.ID_EXTRANET ASC ";
	    return $this->createReturn ($query, $idGroup);
	}
	/*
	 * getservice
	 * insert manual by id user
	 * don't know if the user still exist
	 */
	function getService (){
		$idGroup = $this->listGroup['Service'];
		//521	Communication
		//Achat	1172
		$idUser = array(521,1172,1272);
	    foreach($idUser as $id){
	        $row = new stdClass;
	        $row->idPers = $id;
	        $row->idGroup = $idGroup; 
	        $return[] = $row;
	    }
	    return $return; 
	}
	/***	closing db	***/
	function close() {
		//ibase_free_result($rows);
		ibase_close();
   }
}