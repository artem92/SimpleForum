<?php
function oracle_connect()
{
	PutEnv('ORACLE_SID = XE');
	PutEnv('ORACLE_HOME = '.ora_home);
	PutEnv('TNS_ADMIN = '.tns_admin);
	if ($conn = oci_connect(username,password,db))
		return $conn;
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

function get_user_id($login, $password)
{
	
	$conn = oracle_connect();
	if (isset($login) and isset($password))
	{
		$query = "SELECT user_id
				  FROM users
			      WHERE username='".$login."' AND password='".$password."'";
		if ($st = oci_parse($conn, $query) and oci_execute($st))
		{
			$row = oci_fetch_assoc($st);
			if (isset( $row)) 
			{
				$var = $row['USER_ID'];
				return $var;
			}
			else return '-1';
		}	
		else 
			return 'oracle error';
	}
	else return '-1';
}

function get_message($message_id)
{
	$conn = oracle_connect();
	$sql = 'alter session set nls_date_format = \'DD/MM/YYYY HH24:MI\'';
	$st = oci_parse($conn,$sql);
	if (oci_execute($st))
	{
		//success
	}
	else
	{
		$err = oci_error($c);
		echo 'Oracle error '.$err['message'].'<br />';
	}
	
	
	$query = "SELECT *
              FROM messages
              WHERE  msg_id = ".$message_id;
	$st = oci_parse($conn, $query);
	if (oci_execute($st))
	{
		$row = oci_fetch_assoc($st); 
		return $row;
	}	
	else 
		return 'oracle error';
}

function show_message($message_id)
{
	$msg = get_message($message_id);
	$result = '<div class="message" >';
	$userinfo = get_user_info($msg['USER_ID']);
	$result .= '<div class="post-header">'.
	'<div class="post-header-right" >User '.'<a href="profile.php?user_id='.$userinfo['USER_ID'].'">'.$userinfo['USERNAME'].'</a>'.' posted on '.$msg['MSG_TIME'].'</div> </div>';
	$result .='<div class="post-content" > '.$msg['MSG_TEXT'].' </div>';
	$result .='</div>';
	echo $result;
}

function show_left_menu()
{
	$result = '<div class="menu-left" >';
	$result .= 'Menu';
	$result .= show_branches(false);  //disable direct output in show_branches
	$result .= '</div> ';
	echo $result;
}

function insert_standart_header()
{
session_start();
if (isset($_GET['action']))
{
	if ($_GET['action']=='logout')
	{
		session_unset();
	}
}
require_once('forum.config');
require_once('engine.php');
require_once('content.php');
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
}

function show_login_window()
{
	$str = ' <div class="profile-auth">';
	if (!isset($_SESSION['user_id']))
		{ 
		$str .= 'Please login with your login and password:
			<hr />
			<form action="index.php" method="post">
			<table width="150">
				<tr><td align="left">Login: </td>
					<td><input name="login" type="text" size="13" /></td></tr>
					
				<tr><td align="left"> Password: </td>
					 <td><input name="password" type="password" size="13" /></td></tr>
				<tr><td align="left" colspan="2">
					<input name="remember" type="checkbox" value="Remember" align="left" /> Remember?
						<input name="submit" type="submit" value="Submit" />
						</td></tr>
				<tr><td align="left" colspan="2">
				<a href="registration.php">Not Registered?</a>
				
				</td> </tr>
			</table>
			</form> ';
		}
	else
		{
			$info = get_user_info($_SESSION['user_id']);
			$str .= "Hello, ".$info['USERNAME']; //using oracle style names (fetched from database)
			$str .= '<form action="index.php" method="get"><input name="action" type="submit" value="logout" /></form>';
			$str .= '<a href="profile.php?user_id='.$_SESSION['user_id'].'">Profile</a>';
			
		}
	$str .= '</div>';
	echo $str;
}
//returns user info(assoc array) for user with id specified
function get_user_info($user_id)
{
	$conn = oracle_connect();
	$query = "SELECT *
              FROM users
              WHERE  user_id = ".$user_id;
	if ($st = oci_parse($conn, $query) and oci_execute($st))
	{
		$row = oci_fetch_assoc($st); 
		return $row;
	}	
	else 
		return 'oracle error';
}

function is_valid_usrnm_or_pw($s) //to check if string, entered to username or password field, is correct
{
	$b = array();
	$ret = true;
	foreach ($s as $sub_s)
	if (!((isset($sub_s))&&(strlen($sub_s)==preg_match_all('/\w/',$sub_s,$b))&&($sub_s!=''))) $ret=false;
	return $ret;
}

function open_statement($sql)
{
	$conn = oracle_connect();
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	return $statement;
}

function draw_table($sql)
{
	$conn = oracle_connect();
	error_reporting(0);
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	echo "<table border='1'> <tr>";
	for ($i=1; $i < oci_num_fields($statement)+1; $i++)
	{
		echo "<td>";
		echo oci_field_name($statement, $i);
		echo "</td>";
	}
	oci_num_rows($statement);
	while($row = oci_fetch_row($statement))
	{
		echo "<tr>";
		for ($j=0; $j < oci_num_fields($statement); $j++)
		{
			echo "<td>";
			echo $row[$j];
			echo "</td>";
		}	
		echo "</tr>";
	}
	echo "</tr> </table>";
	error_reporting(E_ALL);
}

function show_branches($out=true)
{
	$sql = 'select * from branches';
	$conn = oracle_connect();
	error_reporting(0);
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	while($row = oci_fetch_assoc($statement))
	{
		if ($out)
			echo '<div class="branch"><a href="/viewbranch.php?branch_id='.$row['BRANCH_ID'].'">'.$row['BRANCH_NAME'].'</a></div>';
		else
			$result .= '<div class="branch"><a href="/viewbranch.php?branch_id='.$row['BRANCH_ID'].'">'.$row['BRANCH_NAME'].'</a></div>';
	}
	if (!$out)
		return $result;
	error_reporting(E_ALL);
}

function show_topics($branch_id)
{
	$sql = 'select * from topics where branch_id='.$branch_id;
	$conn = oracle_connect();
	error_reporting(0);
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	while($row = oci_fetch_assoc($statement))
	{
		
			echo '<div class="topic">';
			echo '<a href="/viewtopic.php?topic_id='.$row['TOPIC_ID'].'">'.$row['TOPIC_NAME'].'</a>';
			echo "</div>";
		
	}
	error_reporting(E_ALL);
}
function show_all_messages($topic_id)
{
	$sql = 'select * from messages where topic_id='.$topic_id;
	$conn = oracle_connect();
	error_reporting(0);
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	echo '<table border="1" width="100%"> ';
	while($row = oci_fetch_assoc($statement))
	{
		show_message($row['MSG_ID']);
	}
	echo " </table>";
	error_reporting(E_ALL);
}
function show_stats($out = true)
{
	$sql = 'select count(*) from messages';
	$st = open_statement($sql);
	$row = oci_fetch_assoc($st);
	//$str = '<div>';
	$str = 'Total messages :'.$row['COUNT(*)'];
	//$str .= '</div>';
	if ($out) echo $str;
	else return $str;
}

function show_menu()
{
	$result = '<div class="menu-left" >';
	$result .= 'Menu';
	$result .= show_branches(false);  //disable direct output in show_branches
	$result .= '</div> ';
	echo $result;
}

function show_add_message()
{
	if (isset($_SESSION['user_id']))
	{
		echo '<center>';
		echo '<form action ="'.$_SERVER['PHP_SELF'].'?topic_id='.$_GET['topic_id'].'" method = "POST">';
		echo '<h4>Enter your message:</h4>';
		echo '<textarea rows = "10" cols = "60" name = "msg_text" class = "textarea"></textarea>';
		echo '<br />';
		echo '<input type = "hidden" name = "lets_post" value = "true">';
		echo '<input type = "submit" value = "Post message">';
		echo '</form>';
		echo '</center>';
	}
	else 
	{
		echo '<h4>You can\'t leave messages, as you\'re a guest.</h4><br />';;
	}
}

function is_valid_message($s) //to check if string, entered as a message, is valid to post
{
	$b = array();
	$ret = true;
	if (!((isset($s))&&(strlen($s)!=preg_match_all('/\s/',$s,$b)))) $ret=false;
	return $ret;
}

function add_message()
{
	
	if ((isset($_POST['lets_post']))
	&&(is_valid_message($_POST['msg_text'])))
	{	
		$msg_text = $_POST['msg_text'];
		$user_id = $_SESSION['user_id'];
		$topic_id = $_GET['topic_id'];
		$sql = 'insert into MESSAGES(MSG_TEXT,USER_ID,TOPIC_ID)	values (\''.
		$msg_text.'\','.
		$user_id.','.
		$topic_id.')';
		//echo $sql;
		
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			echo 'succesfully connected';
			$st = oci_parse($c,$sql);
			$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
			if ($r)
			{
				//success
				echo '<h3>Your messege added successfully!</h3>';
			}
			else 
			{
				$err = oci_error($st);
				echo 'Oracle error '.$err['message'].'<br />';
			}
		}
		else 
		{
			$err = oci_error($c);
			echo 'Oracle error '.$err['message'].'<br />';
		}
	}
}
?>