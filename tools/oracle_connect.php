<?php 
function oracle_connect()
{
	PutEnv('ORACLE_SID = XE');
	PutEnv('ORACLE_HOME = C:\xe\app\oracle\product\10.2.0\server');
	PutEnv('TNS_ADMIN = C:\xe\app\oracle\product\10.2.0\server\NETWORK\ADMIN');
	if ($conn = oci_connect('melhior','pass','localhost/XE')) 
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