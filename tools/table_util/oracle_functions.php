<?php
	
	//gets all values from table associatively
	function get_table_content($c,$table)
	{
		$ar = array();
		$sql = 'select t.*,rowidtochar(t.rowid) as t_rowid from '.$table.' t';
		$st = oci_parse($c,$sql);
		error_reporting(0);
		if (oci_execute($st))
		{
			while ($row = oci_fetch_assoc($st))
			{
				$ar[] = $row;
			}
		}
		else 
		{
			$err = oci_error($st);
			echo 'Oracle error '.$err['message'];
		}
		oci_free_statement($st);
		error_reporting(E_ALL);
		return $ar;
	}
	
	//function returns associative array with names of "native" tables and columns
	//for foreign keys of given table
	function get_foreign_keys($c,//connection
	$table,//table name
	$username)
	{
		$res_ar = array();
		$sql = 'select B.TABLE_NAME,
		B.COLUMN_NAME
		from SYS.ALL_CONSTRAINTS A,
		SYS.ALL_CONS_COLUMNS B
		where A.OWNER = \''.$username.'\'
		and B.OWNER = \''.$username.'\'
		and A.R_CONSTRAINT_NAME = B.CONSTRAINT_NAME
		and A.TABLE_NAME = \''.$table.'\'';
		$stmt = oci_parse($c,$sql);
		error_reporting(0);
		if (oci_execute($stmt))
		{
			while ($row = oci_fetch_assoc($stmt)) 
			{
				$tb_content = get_table_content($c,$row['TABLE_NAME']);
				$res_ar[] = array('column_name' => $row['COLUMN_NAME'],'table_name' => $row['TABLE_NAME'],'table_content'=>$tb_content);	
			}
		}
		else 
		{
			$err = oci_error($stmt);
			echo 'Oracle error '.$err['message'];
		}
		oci_free_statement($stmt);
		error_reporting(E_ALL);
		/*if (count($res_ar)>0) */return $res_ar;
		/*else return false;*/
	}
	
	
	//gets all columns and their datatypes  
	function get_table_columns($c,$table)
	{
		$ar = array();
		$sql = 'select column_name,data_type from user_tab_columns where table_name = \''.$table.'\'';
		$st = oci_parse($c,$sql);
		error_reporting(0);
		if (oci_execute($st))
		{
			while ($row = oci_fetch_assoc($st))
			{
				$key = $row['COLUMN_NAME'];
				$val = $row['DATA_TYPE'];
				$ar[$key] = $val;
			}
		}
		else 
		{
			$err = oci_error($st);
			echo 'Oracle error '.$err['message'];
		}
		oci_free_statement($st);
		error_reporting(E_ALL);
		return $ar;
	}

	//checks if the string contains numeric oracle 10g datatype
	function is_numeric_oracle10g($s)
	{
		$ret = false;
		if (($s == 'NUMBER')||($s == 'DECIMAL')||($s == 'DEC')||
		($s == 'INTEGER')||($s == 'INT')||($s == 'SMALLINT')||
		($s == 'FLOAT')||($s == 'DOUBLE')||($s == 'REAL'))
			$ret = true;
		return $ret;
	}
	
?>