<?
	require_once('/tools/oracle.conf');
	require_once('/engine.php');
	
	$user_id = $_GET['user_id'];
	
	//$viewer = $_SESSION['user_id'];
	$viewer = $user_id;//-temporarily
	//echo 'viewer/user_id: '.$viewer.'<br />';
	
	
	PutEnv('ORACLE_SID = XE');
	PutEnv('ORACLE_HOME = '.ora_home);
    PutEnv('TNS_ADMIN = '.tns_admin);
	if ($c = oci_new_connect(username,password,db))
	{
		error_reporting(0);
		$sql = 'select * from USERS where USER_ID = '.$user_id;
		$st = oci_parse($c,$sql);
		$r = oci_execute($st,OCI_NO_AUTO_COMMIT);
		if ($r)
		{
			$ar = oci_fetch_assoc($st);
			//foreach ($ar as $key=>$val) echo $key.': '.$val.'<br />';
			if (!isset($user_id)) echo 'This page is not to be used directly!';
			else 
			{
			}
		}
		else 
		{
			$err = oci_error($c);
			echo $err['message'];
		}
		oci_free_statement($st);
		error_reporting(E_ALL);
	}	
	else
	{
		//$err = oci_error($c);
		echo 'Could not connect to database';
	}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Profile of user <?// echo  ?></title>
     <link href="CSS/Default.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="width:90%; margin-left:5%; ">
    <div style = "border-style:groove;">
		<? show_header(); ?>
		<center><h3>Profile of user<h3/><center>
    </div>
    <div class="document" >
        <div class="left-column">
			This is left!
            <!-- Place your left column content here-->
            <? //show_left_menu(); ?>
        </div>
        <div class="right-column">
			This is right!
           <!-- Place your right column content here-->
           <? //show_login_window(); ?>
        </div>
        <div class="center-column">
			This is center!
      		<!-- Place your center column content here-->
            <?
			//show_message(1);
			?>
    	</div>
    </div>
    <div style="clear:both"></div> 
    <div>
		This is bottom!
    <? //show_bottom(); ?>
    </div>
</div>


</body>
</html> 