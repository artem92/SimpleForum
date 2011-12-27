<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start(); 


require_once('forum.config');
require_once('engine.php');
if (!isset($_SESSION['user_id']));
	if (((!isset($_POST['login']) or !isset($_POST['password']))) and !guest_access)
	{
		header('Refresh: 2; URL=http://simpleforum/login.php');
		echo 'Guest users are not allowed. ';
		echo 'You will be redirected to login page in 2 sec...';
		exit;
	}
	else 
	if (!(!isset($_POST['login']) or !isset($_POST['password'])))
		$_SESSION['user_id'] = get_user_id($_POST['login'], $_POST['password']);
?>
<!-- header -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SimpleForum</TITLE>
     <link href="CSS/Default.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="width:90%; margin-left:5%; ">
    <div >
    <? show_header(); ?>
    </div>
    <div class="document" >
        <div class="left-column">
            <!-- Place your left column content here-->
            <? show_left_menu(); ?>
        </div>
        <div class="right-column">
           <!-- Place your right column content here-->
           <? show_login_window(); ?>
        </div>
        <div class="center-column">
      		<!-- Place your center column content here-->
            <?
			show_topics($_GET['branch_id']);
			?>
    	</div>
    </div>
    <div style="clear:both"></div> 
    <div>
    <? show_bottom(); ?>
    </div>
</div>


</body>
</html>