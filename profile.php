<?php
	session_start();
	require_once('/tools/oracle.conf');
	require_once('/engine.php');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<script type = "text/javascript">
			function enable_change_pw()
			{
				if ( document.getElementById('ch_true').checked == true)
				{
					document.getElementById('new_pw').disabled = false;
					document.getElementById('r_new_pw').disabled = false;
				}
				else 
				{
					document.getElementById('new_pw').disabled = true;
					document.getElementById('r_new_pw').disabled = true;
				}
			}
		</script>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Profile of user</title>
		 <link href="CSS/Default.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div style="width:90%; margin-left:5%; ">
			<div style = "border-style:groove;">
				<? show_header(); ?>
				<center><h3>Profile of user<h3/><center>
			</div>	
		<div class="document" >
			<div class="left-column">
				This is left!
				<!-- Place your left column content here-->
				<? //show_left_menu(); ?>
			</div>
			<div class="right-column">
				This is right!
			   <!-- Place your right column content here-->
			   <? //show_login_window(); ?>
			</div>
			<div class="center-column">
				<?php
					$user_id = $_GET['user_id'];	
					//$viewer = $_SESSION['user_id'];
					$viewer_id = 1;//-temporarily
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
				?>
			</div>
		</div>
		<div style="clear:both"></div> 
		<div>
			This is bottom!
		<? //show_bottom(); ?>
		</div>
	</div>
	</body>
</html> 