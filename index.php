<?php 
require_once('forum.config');
require_once('engine.php');
require_once('content.php');
insert_standart_header();


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
    <?  show_top();
	show_header(); 
	   index_show_top();
	?>
    </div>
    <div class="document" >
        <div class="left-column">
            <!-- Place your left column content here-->
            <? index_show_left(); ?>
        </div>
        <div class="right-column">
           <!-- Place your right column content here-->
           <? index_show_right(); ?>
        </div>
        <div class="center-column">
      		<!-- Place your center column content here-->
            <?
			index_show_center();
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