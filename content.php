<?php
//This file contains required content to output on different pages divided by sections
//This functions actualy provide an output (echo), so called as procedures

///////////////////////////////////ALL Site section/////////////////////////////////////////////
function show_header()
{
	$str = '<div class="header">
			<a href="index.php">SimpleForum Home</a>
			<a href="adminpage.php">Admin Page</a>
			<a href="registration.php">Registration</a>
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
function index_show_top()
{
	$str = '<div class="top">'.show_index_path().'
		</div>';
	echo $str;
}


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
function viewbranch_show_top()
{
	$str = '<div class="top">';
	$str.=	show_branch_path($_GET['branch_id']).'</div>';
	echo $str;
}

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
function viewtopic_show_top()
{
	$str = '<div class="top">'.show_topic_path($_GET['topic_id']).'</div>';
	echo $str;
}

function viewtopic_show_left()
{
	show_menu();
}

function viewtopic_show_center()
{
	if (!isset($_GET['topic_id']))
	echo '<h2>This page is not to be used directly!</h2>';
	else
	{
		add_message();
		show_all_messages($_GET['topic_id']);
		show_add_message();
	}
}

function viewtopic_show_right()
{
	 show_login_window();
}

/////////////////////////////////////Registration///////////////////////////////////////////////////////////
function registration_show_top()
{
	$str = '<div class="top">'.show_registration_path().'
		</div>';
	echo $str;
}
function registration_show_left()
{
	show_menu();
}

function registration_show_center()
	{
     	show_registration();       
	}
function registration_show_right()
{
	 show_login_window();
}

//////////////////////////////////////Profile///////////////////////////////////////////////////////////
function profile_js_header()
{
	echo '
	<script type = "text/javascript">
			function enable_change_pw()
			{
				if ( document.getElementById(\'ch_true\').checked == true)
				{
					document.getElementById(\'new_pw\').disabled = false;
					document.getElementById(\'r_new_pw\').disabled = false;
				}
				else 
				{
					document.getElementById(\'new_pw\').disabled = true;
					document.getElementById(\'r_new_pw\').disabled = true;
				}
			}
		</script>
	';
}

function profile_show_top()
{
	$str = '<div class="top">'.show_profile_path().'
		</div>';
	echo $str;
}

function profile_show_header()
{
	$str = '<div class="header">
			<a href="index.php">SimpleForum Home</a>
			<a href="tools/table_util/index.php">Admin Page</a>
			<center><h3>Profile of user<h3/><center>
		</div>';
	echo $str;
}

function profile_show_left()
{
	show_menu();
}
function profile_show_center()
{
	show_profile();
}
function profile_show_right()
{
	 show_login_window();
}


////////////////////////////////////Index section///////////////////////////////////////////////
function adminpage_show_top()
{
	$str = '<div class="top">'.show_adminpage_path().'
		</div>';
	echo $str;
}


function adminpage_show_left()
{
	show_menu();
}
function adminpage_show_center()
{
	show_branches();
}
function adminpage_show_right()
{
	 show_login_window();
}


?>