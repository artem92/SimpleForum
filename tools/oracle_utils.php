<?php
	//require_once('oracle.conf.php');
	
	function oracle_connect()
	{
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($conn = oci_connect(username,password,db)) 
		{
			//echo 'succesfully connected to orcl!';
			return $conn;
		}
		else 
		{
			$err = oci_error();
			echo 'Oracle error '.$err[text];
			return $err;
		}
	}
	function oracle_disconnect($conn)
	{
		if (OCILogoff($conn))
			;//echo succesfully disconnected from Oracle!';
		else
			echo 'Error while disconnecting from Oracle';
	}
?>