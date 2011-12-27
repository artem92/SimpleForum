<?php
//This file contains required content to output on different pages divided by sections
//This functions actualy provide an output (echo), so called as procedures

///////////////////////////////////ALL Site section/////////////////////////////////////////////
function show_header()
{
	$str = '<div class="header">
			<a href="index.php">SimpleForum Home</a>
			<a href="tools/table_util/index.php">Admin Page</a>
		</div>';
	echo $str;
}

function show_bottom()
{
	$str = '<div class="bottom">
			'.show_stats(false).'
		</div>';
echo $str;
}

////////////////////////////////////Index section///////////////////////////////////////////////
function index_show_left()
{
	show_menu();
}
function index_show_center()
{
	show_branches();
}
function index_show_right()
{
	 show_login_window();
}

//////////////////////////////////////ViewBranch/////////////////////////////////////////////////////////
function viewbranch_show_left()
{
	show_menu();
}
function viewbranch_show_center()
{
	show_topics($_GET['branch_id']);
}
function viewbranch_show_right()
{
	 show_login_window();
}
//////////////////////////////////////ViewTopic///////////////////////////////////////////////////////////
function viewtopic_show_left()
{
	show_menu();
}
function viewtopic_show_center()
{
	show_all_messages($_GET['topic_id']);
}
function viewtopic_show_right()
{
	 show_login_window();
}


?>