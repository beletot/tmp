<?php
/*
 * write sql for
 */

class writeSql{
	/*
	 * get header, table, and values
	 * not working with string value
	 */
	function getHeader($row){
		$headerSql = ' ( ';
		$header = get_object_vars($row);
		$headerSql .= implode(',', array_keys($header));
		$headerSql .= ')';
		return $headerSql;
	}
	function insert($rows){		
		$table = 'jos_directory_group_user';
		if(!$rows){return '';}
		$headerSql = $this->getHeader($rows[0]);
		$valueSql =  array();
		$sql = 'INSERT INTO `'.$table.'` '.$headerSql;
		$sql .= ' VALUES ';
		//echo '<pre>'.print_r($rows,true).'</pre>';
		foreach($rows as  $row){
			$lines = get_object_vars($row);
			$valueSql[] = ' ('.implode(',', array_values($lines)).')';
		}
		$valueSql = implode(', ', $valueSql);
		$sql .= $valueSql."; \n";
		//echo $sql;
		return $sql;
	}
}