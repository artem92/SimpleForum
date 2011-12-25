<?php session_start();
require_once('forum.config');
require_once('engine.php');
if (get_user_id($_POST['login'], $_POST['password'])==-1 and !guest_access)
{
	header('Refresh: 2; URL=http://simpleforum/login.php');
	echo 'Guest users are not allowed. ';
	echo 'You will be redirected to login page in 2 sec...';
	exit;
}
else 
{
	$_SESSION['user_id'] = get_user_id($_POST['login'], $_POST['password']);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>SimpleForum</TITLE>
<link href="CSS/Const.css" rel="stylesheet" type="text/css">
<!-- <link href="CSS/New.css" rel="stylesheet" type="text/css"> -->
</head>
<body>
    	
<div class="document">      
<div class="header">
 Hello world
</div>


    <div class="menu_left">
     Hello world
</div>
<div>
    <div class="content">
    Session_ID:
     <? echo $_SESSION['user_id']; ?>
    
    </div>
    </div>
<div class="menu_right">
     <? require "login.inc" ?>
    </div>
 


</div>   
<div class="bottom">
Hello world
</div> 	

</body>
</html>