<?php
require_once('engine_ex.php');
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
	$viewerinfo = get_user_info($_SESSION['user_id']);
	$userinfo = get_user_info($msg['USER_ID']);
	$result .= '<div class="post-header">'.
	'User '.'<a href="profile.php?user_id='.$userinfo['USER_ID'].'">'.$userinfo['USERNAME'].'</a>'.' posted on '.$msg['MSG_TIME'];
	if ($msg['USER_ID'] == $_SESSION['user_id']||$viewerinfo['ACCESS_LEVEL']=='admin')
	{
		$result .= '<a href="viewtopic.php?topic_id='.$_GET['topic_id'].'&action=delete&msg_id='.$message_id.'"><img src="res/delete_item.gif" width="16" height="16" longdesc="res/delete_item.gif" />delete message</a>';
	}
	$result .=' </div><div class="post-content" > '.$msg['MSG_TEXT'].' </div>';
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
		$s = '';
		$i = 1;
		foreach($_GET as $key=>$val)
		{
			if ($i==1) $s .='?'.$key.'='.$val;
			else $s .='&'.$key.'='.$val; 
			$i+=1;
		}
			//echo $s;
		$str .= 'Please login with your login and password:
			<hr />
			<form action="'.$_SERVER['PHP_SELF'].$s.'" method="post">
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
		if ($row = oci_fetch_assoc($st))
			return $row;
		else return false;
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
	$viewerinfo = get_user_info($_SESSION['user_id']);

	$statement = oci_parse($conn, $sql);
	if (oci_execute($statement))
	{
		echo '<table class = "viewtopics">';
		echo '<tr> <td class="cell"> Topic </td> <td class="cell"> Author</td><td class = "cell">Replies</td><td class = "cell">Last message</td>';
		echo '<td> delete?</td>';
		echo '</tr>';
		
		
		while($row = oci_fetch_assoc($statement))
		{
			echo '<tr> <td class="cell">';
			echo '<div class="topic">';
			echo '<a href="/viewtopic.php?topic_id='.$row['TOPIC_ID'].'">'.$row['TOPIC_NAME'].'</a>';
			echo "</div>";	
			echo '</td class="cell">';
			
			$sql = 'alter session set nls_date_format = \'DD/MM/YYYY HH24:MI\'';
			$st = oci_parse($conn,$sql);
			if (oci_execute($st))
			{
				//success
			}
			
			
			$in_var = $row['TOPIC_ID'];
			
			$sql = 'begin get_author_by_topic_id(:in_topic_id,:out_author,:out_author_id); end;';
			$st = oci_parse($conn,$sql);
			oci_bind_by_name($st,':in_topic_id',$in_var); 
			oci_bind_by_name($st,':out_author',$out_author,256);
			oci_bind_by_name($st,':out_author_id',$out_author_id,-1,SQLT_INT);
			$r_author = oci_execute($st);
			$err_author = oci_error($st);
			
			$sql = 'begin get_info_by_topic_id(:in_topic_id,:out_posts_num,:out_max_user,:out_max_user_id,:out_max_date); end;';
			$in_var = $row['TOPIC_ID'];
			$st = oci_parse($conn,$sql);
			oci_bind_by_name($st,':in_topic_id',$in_var); 
			oci_bind_by_name($st,':out_posts_num',$out_posts_num,-1,SQLT_INT);
			oci_bind_by_name($st,':out_max_user',$out_max_user,256);
			oci_bind_by_name($st,':out_max_user_id',$out_max_user_id,-1,SQLT_INT);
			oci_bind_by_name($st,':out_max_date',$out_max_date,256);
			$r = oci_execute($st);
			$err = oci_error($st);
			
			echo '<td class="cell">';
			if ($r_author) echo '<a href="/profile.php?user_id='.$out_author_id.'">'.$out_author.'</a>';
			else echo $err_author['message'];
			
			echo '</td>';
			
			echo '<td class="cell">';
			if ($r) echo $out_posts_num;
			//else echo $err['message'];
			else echo '-';
			echo '</td>';
			

			echo '<td class="cell">';
			if ($r) echo '<a href="/profile.php?user_id='.$out_max_user_id.'">'.$out_max_user.'</a> <'.$out_max_date.'>';
			//else echo $err['message'];
			else echo '-';
			echo '</td>'; 
			
			if ($row['USER_ID'] == $_SESSION['user_id']||$viewerinfo['ACCESS_LEVEL']=='admin')
			{
				echo '<td class="cell"> <a href="viewbranch.php?branch_id='.$_GET['branch_id'].'&action=delete&topic_id='.$row['TOPIC_ID'].'"><img src="res/delete_item.gif" width="16" height="16" longdesc="res/delete_item.gif" /></a> </td>';
			}
			else echo '<td class="cell"></td>';
			
			echo '</tr>';
				
		}
		echo '</table>';
	}
	else 
	{
		$err = oci_error($c);
		echo 'Oracle error '.$err['message'].'<br />';
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
	//echo '<table border="1" width="100%"> ';
	while($row = oci_fetch_assoc($statement))
	{
		show_message($row['MSG_ID']);
	}
	//echo " </table>";
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
		unset($_POST['lets_post']); //if we reload page, new post will not be added
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
			//echo 'succesfully connected';
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

function show_profile()
{
	$user_id = $_GET['user_id'];	
	$viewer_id = $_SESSION['user_id'];
	
	//$viewer_id = 1;//-temporarily
	//echo 'viewer/user_id: '.$viewer.'<br />';
	
	if (!isset($user_id)) echo '<center><h4>This page is not to be used directly!</h4></center>';
	else 
	{
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
				
				$sql_t = 'select count(*) as ct from MESSAGES where USER_ID = '.$user_id;
				$st_t = oci_parse($c,$sql_t);
				$r_t = oci_execute($st_t,OCI_NO_AUTO_COMMIT);
				if ($r_t)
				{
					$ar_t = oci_fetch_assoc($st_t);
					$msg_num = $ar_t['CT'];
				}
				else
				{
					$msg_num = '(database error occured, was not possible to find number of posts)';
				}
				
				
				
				if ($user_id!=$viewer_id)
				{
					if ($ar['PROFILE_VISIBILITY']=='private') echo '<h4>Sorry, but personal information of this user is private!</h4>';
					else 
					{
						echo '<h4>Username: '.$ar['USERNAME'].'</h4>';
						echo '<h4>Access level: '.$ar['ACCESS_LEVEL'].'</h4>';
						echo '<h4>Number of posts: '.$msg_num.'</h4>';
						echo '<h4>Email: '.$ar['EMAIL'].'</h4>';
						echo '<h4>Personal information: "'.$ar['INFO'].'"</h4>';
					}
				}
				else
				{	
					echo '<h4>Username: '.$ar['USERNAME'].'</h4>';
					echo '<h4>Access level: '.$ar['ACCESS_LEVEL'].'</h4>';
					echo '<h4>Number of posts: '.$msg_num.'</h4>';
					
					if (isset($_POST['lets_submit']))
					{
						if (isset($_POST['email'])) $email = $_POST['email'];
						else $email = '';
						
						if (isset($_POST['info'])) $info = $_POST['info'];
						else $info = '';
						
						if ($_POST['profile_visibility']=='yes') $p_v_yes = 'checked = "yes"';
						else $p_v_no = 'checked = "yes"';
					}
					else 
					{
						$email = $ar['EMAIL'];
						$info = $ar['INFO'];
						if ($ar['PROFILE_VISIBILITY']=='public') $p_v_yes = 'checked = "yes"';
						else $p_v_no = 'checked = "yes"';
					}
					
					$s = '<h3>Here you can change your personal information, email and password :</h3>
					<h4>(Note that all symbols in your username and password should be latin letters, numbers or underscores("_") in any sequence.
					Username, password and Email should be up to 200 symbols in length, info - up to 4000)</h4>
					<br />
					<form action="'.$_SERVER['PHP_SELF'].'?user_id='.$user_id.'" method="post">
					<table border="0">
					  <tr>
						<td>Change password? </td>
						<td>
							<input name="change_pw" id = "ch_true" type="radio" value = "yes" onclick = "enable_change_pw();"
							checked = "yes">Yes<br />
							<input name="change_pw" id = "ch_false" type="radio" value = "no" onclick = "enable_change_pw();">No
						</td>
					  </tr>
					  <tr>
						<td>New password: </td>
						<td><input id = "new_pw" name="new_password" type="password" size="20"></td>
					  </tr>
					  <tr>
						<td>Repeat new password:</td>
						<td><input id = "r_new_pw" name="repeat_new_password" type="password" size="20" ></td>
					  </tr>
					  <tr>
						<td>Old password:</td>
						<td><input name="old_password" type="password" size="20" > </td>
					  </tr>
					  <tr>
						<td>Do you let other users to watch your profile?</td>
						<td>
							<input name="profile_visibility" type="radio" value = "yes" '.$p_v_yes.'>Yes <br />
							<input name="profile_visibility" type="radio" value = "no" '.$p_v_no.'>No
						</td>
					  </tr>
					  <tr>
						<td>Email*: </td>
						<td><input name="email" type="text" size="20" value = "'.$email.'"></td>
					  </tr>
					   <tr>
						<td>Your personal information*: </td>
						<td><textarea rows = "10" cols = "40" name = "info" class = "textarea">'.$info.'</textarea> </td>
					  </tr>
					</table>
					(Fields, marked with "*", are not necessary to fill)
					<br /><br />
					<input type = "hidden" value = "1" name = "lets_submit">
					<center><input type = "submit" value = "Submit changes" class="button"></center>';
					
					if (isset($_POST['lets_submit']))
					{
						if ($_POST['old_password']!=$ar['PASSWORD']) 
						$s = $s.'<br /><br />
						<font size = "3" color = "red">
						You entered wrong old password. Changes weren\'t applied
						</font>';
						
						if (($_POST['change_pw'] == 'yes')
						&&($_POST['new_password']!=$_POST['repeat_new_password']))
							$s = $s.'<br /><br />
						<font size = "3" color = "red">
						You didn\'t repeat the password correctly. Changes weren\'t applied
						</font>';
						
						if (($_POST['old_password']==$ar['PASSWORD'])&&(($_POST['change_pw'] != 'yes')||
						(($_POST['change_pw'] == 'yes')&&
						(is_valid_usrnm_or_pw(array($_POST['new_password'],$_POST['repeat_new_password'])))&&
						($_POST['new_password']==$_POST['repeat_new_password']))))
						{
							$info = str_replace('\'','\'\'',$_POST['info']);
							if (isset($_POST['email'])) $email = str_replace('\'','\'\'',$_POST['email']);
							else $email = '';
							$new_password = $_POST['new_password'];
							if($_POST['profile_visibility']=='yes') $p_v = 'public';
							else $p_v = 'private';
							
							$sql_t = 'update USERS set ';
							
							if ($_POST['change_pw']=='yes') $sql_t = $sql_t.'PASSWORD = \''.$new_password.'\',';
							
							$sql_t = $sql_t.
							'INFO = \''.$info.'\',
							EMAIL = \''.$email.'\',
							PROFILE_VISIBILITY = \''.$p_v.'\'
							where USER_ID = '.$user_id;
							//echo $sql_t;
							$st_t = oci_parse($c,$sql_t);
							$r_t = oci_execute($st_t,OCI_COMMIT_ON_SUCCESS);
							if ($r_t)
							{
								$s = $s.'<br /><br /><font size = "3" color = "green">
								Changes applied successfully!
								</font>';
							}
							else
							{
								$s = $s.'<br /><br /><font size = "3" color = "red">
								Database error occured, changes weren\'t applied
								</font>';
							}
						}
					}
					echo $s;
					echo '</form>';
				}
			}
			else 
			{
				$err = oci_error($st);
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
	}
}
function show_registration()
	{
	 
			if (isset($_POST['username'])) {$usrnm = $_POST['username'];}
			else $usrnm = '';
			if (isset($_POST['info'])) {$info = $_POST['info'];}
			else $info = '';
			if (isset($_POST['email'])) {$email = $_POST['email'];}
			else $email = '';
			$p_v_yes = '';
			$p_v_no = '';
			if (isset($_POST['profile_visibility'])) 
			{
				if ($_POST['profile_visibility']=='yes')
					$p_v_yes = 'checked = "yes"';
				else $p_v_no = 'checked = "yes"';
			}
			else $p_v_yes = 'checked = "yes"';
		$s = '<h3>Welcome to the SimpleForum registration page! To register, enter your username and password in the fields below:</h3>
		<h4>(Note that all symbols in your username and password should be latin letters, numbers or underscores("_") in any sequence.
		Username, password and Email should be up to 200 symbols in length, info - up to 4000)</h4>
		<br />
		<form action="'.$_SERVER['PHP_SELF'].'" method="post">
		<table border="0">
		  <tr>
			<td>Username: </td>
			<td><input name="username" type="text" size="20" value = "'.$usrnm.'"></td>
		  </tr>
		  <tr>
			<td>Password:</td>
			<td><input name="password" type="password" size="20" > </td>
		  </tr>
		  <tr>
			<td>Repeat password:</td>
			<td><input name="repeat_password" type="password" size="20" > </td>
		  </tr>
		  <tr>
			<td>Do you let other users to watch your profile?</td>
			<td>
				<input name="profile_visibility" type="radio" value = "yes" '.$p_v_yes.'>Yes <br />
				<input name="profile_visibility" type="radio" value = "no" '.$p_v_no.'>No
			</td>
		  </tr>
		  <tr>
			<td>Email*: </td>
			<td><input name="email" type="text" size="20" value = "'.$email.'"></td>
		  </tr>
		   <tr>
			<td>Your personal information*: </td>
			<td><textarea rows = "10" cols = "40" name = "info" class = "textarea">'.$info.'</textarea> </td>
		  </tr>
		</table>
		(Fields, marked with "*", are not necessary to fill)
		<br /><br />
		<input type = "hidden" value = "1" name = "lets_submit">
		<input type = "submit" value = "Send registration request" class="button">';
		if (isset($_POST['lets_submit'])) $s = $s.'<br /><br />
		<font size = "3" color = "red">Please, fill in the necessary fields properly (according to the rules mentioned above)</font>'; 
		
		if ((!is_valid_usrnm_or_pw(array($_POST['username'],$_POST['password'],$_POST['repeat_password'])))||
		($_POST['password']!=$_POST['repeat_password'])) echo $s;
		
		else //actual registration - put user to the DB
		{	
			//echo $_POST['profile_visibility'];
			//echo 'username: \''.$_POST['username'].'\' <br />';
			//echo 'password: \''.$_POST['password'].'\'';
			PutEnv('ORACLE_SID = XE');
			PutEnv('ORACLE_HOME = '.ora_home);
			PutEnv('TNS_ADMIN = '.tns_admin);
			if ($c = oci_new_connect(username,password,db)) 
			{
				$info = str_replace('\'','\'\'',$_POST['info']);
				$username = str_replace('\'','\'\'',$_POST['username']);
				$password = str_replace('\'','\'\'',$_POST['password']);
				if ($_POST['profile_visibility']=='yes') $p_v = 'public';
				else $p_v = 'private';
				if (isset($_POST['email'])) $email = str_replace('\'','\'\'',$_POST['email']);
				
				error_reporting(0);
				$sql = 'insert into USERS(USERNAME,PASSWORD,INFO,EMAIL,PROFILE_VISIBILITY) 
				values (\''.$username.'\',\''.$password.'\',\''.$info.'\',\''.$email.'\',\''.$p_v.'\')';
				//echo $sql;
				$st = oci_parse($c,$sql);
				$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
				if ($r)
				{
					//success
					echo '<h3>You registered successfully!</h3>';
				}
				else 
				{
					$err = oci_error($st);
					if ($err['code']==1) echo 'Sorry, but this username is used already.';
					else echo 'Database error appeared. Sorry, you didn\'t register. ';
					//echo $err['message'];
					echo '<br /><a href = "'.$_SERVER['PHP_SELF'].'">Try again</a>';
				}
				oci_free_statement($st);
				error_reporting(E_ALL);
				
			}
			else 
			{
				$err = oci_error($c);
				echo 'Oracle error '.$err['message'].'<br />';
			}

		};
            
	}

///////////////////////////////////////////  from table util ///////////////////////////////////////////////////
//gets all values from table associatively
function get_table_content($c,$table)
{
	$ar = array();
	$sql = 'select t.*,rowidtochar(t.rowid) as t_rowid from '.$table.' t';
	$st = oci_parse($c,$sql);
	error_reporting(0);
	if (oci_execute($st))
	{
		while ($row = oci_fetch_assoc($st))
		{
			$ar[] = $row;
		}
	}
	else 
	{
		$err = oci_error($st);
		echo 'Oracle error '.$err['message'];
	}
	oci_free_statement($st);
	error_reporting(E_ALL);
	return $ar;
}

//function returns associative array with names of "native" tables and columns
//for foreign keys of given table
function get_foreign_keys($c,//connection
$table,//table name
$username)
{
	$res_ar = array();
	$sql = 'select B.TABLE_NAME,
	B.COLUMN_NAME
	from SYS.ALL_CONSTRAINTS A,
	SYS.ALL_CONS_COLUMNS B
	where A.OWNER = \''.$username.'\'
	and B.OWNER = \''.$username.'\'
	and A.R_CONSTRAINT_NAME = B.CONSTRAINT_NAME
	and A.TABLE_NAME = \''.$table.'\'';
	$stmt = oci_parse($c,$sql);
	error_reporting(0);
	if (oci_execute($stmt))
	{
		while ($row = oci_fetch_assoc($stmt)) 
		{
			$tb_content = get_table_content($c,$row['TABLE_NAME']);
			$res_ar[] = array('column_name' => $row['COLUMN_NAME'],'table_name' => $row['TABLE_NAME'],'table_content'=>$tb_content);	
		}
	}
	else 
	{
		$err = oci_error($stmt);
		echo 'Oracle error '.$err['message'];
	}
	oci_free_statement($stmt);
	error_reporting(E_ALL);
	/*if (count($res_ar)>0) */return $res_ar;
	/*else return false;*/
}


//gets all columns and their datatypes  
function get_table_columns($c,$table)
{
	$ar = array();
	$sql = 'select column_name,data_type from user_tab_columns where table_name = \''.$table.'\'';
	$st = oci_parse($c,$sql);
	error_reporting(0);
	if (oci_execute($st))
	{
		while ($row = oci_fetch_assoc($st))
		{
			$key = $row['COLUMN_NAME'];
			$val = $row['DATA_TYPE'];
			$ar[$key] = $val;
		}
	}
	else 
	{
		$err = oci_error($st);
		echo 'Oracle error '.$err['message'];
	}
	oci_free_statement($st);
	error_reporting(E_ALL);
	return $ar;
}

//checks if the string contains numeric oracle 10g datatype
function is_numeric_oracle10g($s)
{
	$ret = false;
	if (($s == 'NUMBER')||($s == 'DECIMAL')||($s == 'DEC')||
	($s == 'INTEGER')||($s == 'INT')||($s == 'SMALLINT')||
	($s == 'FLOAT')||($s == 'DOUBLE')||($s == 'REAL'))
		$ret = true;
	return $ret;
}	
///////////////////////////////////////////  /from table util ///////////////////////////////////////////////////	
	
function show_adminpage()
{
	if (isset($_SESSION['user_id'])) 
	{
		$viewer_id = $_SESSION['user_id'];
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			$sql = 'select ACCESS_LEVEL from USERS where  USER_ID = '.$viewer_id;
			$st = oci_parse($c,$sql);
			if (oci_execute($st,OCI_NO_AUTO_COMMIT))
			{
				$ar = oci_fetch_assoc($st);
				$access_level = $ar['ACCESS_LEVEL'];
				if ($access_level=='admin')
				{
					//echo 'success!';
					echo '<h3>Welcome to the admin page!</h3>';
					echo '<h4>Here you can use SQL text area to work with database manually, or choose table to work with it using GUI. 
					Tables are: USERS, BRANCHES, TOPICS, MESSAGES.</h4>';
					
					echo '<form action = "change_table.php" method = "POST">
						<h3> Select the table to work with it </h3>
						<select name = "table">
						<option value = "USERS">USERS</option>
						<option value = "MESSAGES">MESSAGES</option>
						<option value = "BRANCHES">BRANCHES</option>
						<option value = "TOPICS">TOPICS</option>
						<option value = "OBJECTS">OBJECTS</option>
						</select>
						<input type = "hidden" name = "admin_id" value = "'.$_SESSION['user_id'].'">
						<input type = "submit" value = "OK" style = "width : 5em" >
						</form>';
					
					echo '<h3>Or use SQL text area to work with database manually:</h3>';
					
					echo '
					<table>
						<tr>
							<td>
								<form action = '.$_SERVER[PHP_SELF] .' method = "POST">
									<h4> (SQL statements should not end with a semi-colon (";"). PL/SQL statements should end with a semi-colon (";").) </h4>
									<textarea rows = "10" cols = "70" name = "sql" id = "sql">'.$_POST['sql'].'</textarea>
									<br />
									<input type = "checkbox" name = "to_commit" value = "true">commit after executing
									<br /><br />
									<input type = "submit" value = "Execute" style = "height: 5em; width: 10em;">
								</form>
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
					</table>
					</center>';

					$to_commit = $_POST['to_commit'];
					$sql = $_POST['sql'];
					if (isset($sql)) 
					{
						$st = oci_parse($c,$sql);
						error_reporting(0);
						$r = oci_execute($st,OCI_NO_AUTO_COMMIT);
						if ($r)
						{
							//echo 'command ran succesfully';
							//oci_commit($st);
							
							if (oci_statement_type($st)=='SELECT')
							{
								$row = oci_fetch_assoc($st);
								if ($row)
								{
									echo '<br />';
									echo '<table border = "2" align = "center">';
									echo '<tr>';
									$keys = array_keys($row);
									foreach ($keys as $key)
									{
										echo '<th>';
										echo $key;
										echo '</th>';
									}
									echo '</tr>';
									echo '<tr>';
										foreach($row as $val)
										{
											echo '<td>';
											echo $val;
											echo '</td>';
										}
									echo '</tr>';
									while ($row = oci_fetch_assoc($st))
									{
										echo '<tr>';
										foreach($row as $val)
										{
											echo '<td>';
											echo $val;
											echo '</td>';
										}
										echo '</tr>';
									}
									echo '</table>';
								}
							}
							if ($to_commit == 'true') oci_commit($c);
						}
						else 
						{
							$err = oci_error($st);
							echo 'Oracle error '.$err['message'];
						}
						oci_free_statement($st);
						error_reporting(E_ALL);
					}
				}
				else
				{
					echo '<h4>This page is for administrators of the forum only!</h4>';
				}
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
	else echo '<h4>This page is for administrators of the forum only!</h4>';
}

function show_all_guestbook_msgs()
{
	
	/*$viewerinfo = get_user_info($_SESSION['user_id']);*/
	PutEnv('ORACLE_SID = XE');
	PutEnv('ORACLE_HOME = '.ora_home);
	PutEnv('TNS_ADMIN = '.tns_admin);
	
	error_reporting(0);
	
	if ($conn = oci_new_connect(username,password,db)) 
	{
		$sql = 'select * from GUESTBOOK';
		$statement = oci_parse($conn, $sql);
		if (oci_execute($statement))
		{
		while($msg = oci_fetch_assoc($statement))
		{
			 $result = '<div class="message" >';
			 $result .= '<div class="post-header">'.
			 'User \''.$msg['GUEST_NAME'].'\' posted on '.$msg['GUEST_MSG_TIME'];
			 
			 /*if ($viewerinfo['ACCESS_LEVEL']=='admin')
			 $result .= '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&guest_msg_id='.$msg['GUEST_MSG_ID'].'"><img src="res/delete_item.gif" width="16" height="16" longdesc="res/delete_item.gif" />delete message</a>';*/
			  
			 $result .=' </div>';
			 $result .='<div class="post-content" > '.$msg['GUEST_MSG_TEXT'].' </div>';
			 $result .='</div>';
			 echo $result;
		}	
		}
		else 
		{
			$err = oci_error($st);
			echo $err['message'].'<br />';
		}
	}
	else 
	{
		$err = oci_error($c);
		echo $err['message'].'<br />';
	}
	
	error_reporting(E_ALL);
}

function show_guestbook()
{
	echo '<h3>Welcome to the SimpleForum guestbook!</h3> 
	<h4>Here you can leave your message to let us know what\'s wrong (or fine) with the forum, so we can improve our service.</h4>';
	
	if ((is_valid_message($_POST['message']))&&
	(is_valid_usrnm_or_pw($_POST['guestname']))&&
	(isset($_POST['lets_submit'])))
	{	
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		
		error_reporting(0);
		
		if ($conn = oci_new_connect(username,password,db)) 
		{
			$sql = 'insert into GUESTBOOK (GUEST_NAME,GUEST_MSG_TEXT) values (\''.
			$_POST['guestname'].'\',\''.
			$_POST['message'].'\')';
			echo $sql;
			$statement = oci_parse($conn, $sql);
			if (oci_execute($statement,OCI_COMMIT_ON_SUCCESS))
			{
				//success!
			}	
			else 
			{
				$err = oci_error($statement);
				echo $err['message'].'<br />';
			}
		}
		else 
		{
			$err = oci_error($c);
			echo $err['message'].'<br />';
		}
		
		error_reporting(E_ALL);
	}
	show_all_guestbook_msgs();
	
	echo '<h4>(Note, that your name should consist only of latin letters, 
	numbers and underscores ("_") in any sequence. A reply should not be empty.)</h4>';
	echo '<form action = '.$_SERVER['PHP_SELF'].' method = "POST">
		<table border="0">
		<tr>
			<td>Your name: </td>
			<td><input name="guestname" type="text" size="20" ></td>
		</tr>
		<tr>
			<td>Your reply: </td>
			<td><textarea rows = "10" cols = "40" name = "message" class = "textarea"></textarea> </td>
		</tr>
		</table>
		<input type = "submit" name = "lets_submit" value = "Leave a reply">
		</form>';
}

function delete_message()
{
	if (isset($_GET['action'])&&isset($_GET['msg_id'])&&$_GET['action']=='delete')
	{
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			//echo 'succesfully connected';
			$sql = 'delete from messages where msg_id='.$_GET['msg_id'];
			$st = oci_parse($c,$sql);
			$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
			if ($r)
			{
				//success
				echo '<h3>Message was deleted successfully!</h3>';
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

function delete_topic()
{
	if (isset($_GET['action'])&&isset($_GET['topic_id'])&&$_GET['action']=='delete')
	{
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			//echo 'succesfully connected';
			$sql = 'delete from messages where topic_id='.$_GET['topic_id'];
			$st = oci_parse($c,$sql);
			$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
			$sql = 'delete from topics where topic_id='.$_GET['topic_id'];
			$st = oci_parse($c,$sql);
			$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
			if ($r)
			{
				//success
				echo '<h3>Topic was deleted successfully!</h3>';
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