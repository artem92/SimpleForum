<?php
require_once("tools/oracle_utils.php");
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
			if ($row = oci_fetch_assoc($st)) 
				return $row['USER_ID'];
			else return '-1';
		}	
		else 
			return 'oracle error';
	}
		else return '-1';
}

//Returns assoc 
function get_message($message_id)
{
	$conn = oracle_connect();
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
//returns message div with id specified
function show_message($message_id)
{
	$msg = get_message($message_id);
	$result = '<div class="message" >';
	$result .= '<div class="post-header" >User '.$msg['USER_ID'].' posted on '.$msg['MSG_TIME'].'</div> <hr>';
	$result .='<div class="post-content" > '.$msg['MSG_TEXT'].' </div>';
	$result .='</div>';
	
	echo $result;
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

//Shows div with login window or profile
function show_login_window()
{
	$str = ' <div class="profile-auth">';
	if ($_SESSION['user_id']<0)
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
		}
	$str .= '</div>';
	echo $str;
}

//Shows header
function show_header()
{
	$str = '<div class="header">
			Hello world
		</div>';
echo $str;
}

function show_left_menu()
{
	$str = '<div class="menu_left">
            Hello world
        </div>';
//DISCLAIMER - do not tabify end of heredoc
echo $str;
}

function show_content()
{
	$str = '<div class="message">'.'Session_ID: '
	.$_SESSION["user_id"].'</div>';
	echo $str;
}

function show_bottom()
{
	$str = '<div class="bottom"> Bottom </div>';
	echo $str;
}


?>