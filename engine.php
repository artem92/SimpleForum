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
		{
			return 'oracle error';
		}
	}
	else return '-1';
}

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

function show_message($message_id)
{
	$msg = get_message($message_id);
	$result = '<div class="message" >';
	$result .= '<div class="post-header" >User '.$msg['USER_ID'].' posted on '.$msg['MSG_TIME'].'</div> <hr>';
	$result .='<div class="post-content" > '.$msg['MSG_TEXT'].' </div>';
	$result .='</div>';
	
	echo $result;
}

function show_left_menu()
{
	$result = '<div class="menu-left" >';
	$result .= 'Left</div> ';
	echo $result;
}

function show_login_window()
{
	$str = ' <div class="profile-auth">';
	if ($_SESSION['user_id']<0 or !isset($_SESSION['user_id']) )
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
//Shows header
function show_header()
{
	$str = '<div class="header">
			Hello world
		</div>';
echo $str;
}
function show_bottom()
{
	$str = '<div class="bottom">
			Hello world
		</div>';
echo $str;
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


function show_branches()
{
	$sql = 'select * from branches';
	$conn = oracle_connect();
	error_reporting(0);
	$statement = oci_parse($conn, $sql);
	oci_execute($statement);
	echo '<table border="1" width="100%"> ';
	while($row = oci_fetch_assoc($statement))
	{
		echo "<tr>";
			echo "<td>";
			echo '<a href="/viewbranch.php?branch_id='.$row['BRANCH_ID'].'">'.$row['BRANCH_NAME'].'</a>';
			echo "</td>";
		echo "</tr>";
	}
	echo " </table>";
	error_reporting(E_ALL);
}



?>