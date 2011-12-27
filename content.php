<?php
//This file contains required content to output on different pages divided by sections
//This functions actualy provide an output (echo), so called as procedures

///////////////////////////////////ALL Site section/////////////////////////////////////////////
function show_header()
{
	$str = '<div class="header">
			<a href="index.php">SimpleForum Home</a>
			<a href="tools/table_util/index.php">Admin Page</a>
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
	$str = '<div class="top">
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
	$str = '<div class="top">
		</div>';
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
	$str = '<div class="top">
		</div>';
	echo $str;
}

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

/////////////////////////////////////Registration///////////////////////////////////////////////////////////
function registration_show_top()
{
	$str = '<div class="top">
		</div>';
	echo $str;
}
function registration_show_left()
{
	show_menu();
}
function registration_show_center()
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
	$str = '<div class="top">
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
	$user_id = $_GET['user_id'];	
					$viewer_id = $_SESSION['user_id'];
					
					//$viewer_id = 1;//-temporarily
					//echo 'viewer/user_id: '.$viewer.'<br />';
					
					if (!isset($user_id)) echo '<center><h2>This page is not to be used directly!</h2></center>';
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
									if ($ar['PROFILE_VISIBILITY']=='private') echo 'Sorry, but personal information of this user is private!';
									else 
									{
										echo 'Username: '.$ar['USERNAME'].'<br />';
										echo 'Access level: '.$ar['ACCESS_LEVEL'].'<br />';
										echo 'Number of posts: '.$msg_num.'<br />';
										echo 'Email: '.$ar['EMAIL'].'<br />';
										echo 'Personal information: "'.$ar['INFO'].'"<br />';
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
function profile_show_right()
{
	 show_login_window();
}

?>