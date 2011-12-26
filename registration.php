<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?	
	require('/tools/oracle.conf');
	require('engine.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>SimpleForum User Registration</TITLE>
<link href="CSS/Default.css" rel="stylesheet" type="text/css">
</head>
<body>   	
<div class="document">      

<div class="header">
 <center><h2>Simple forum registration</h2></center>
 <a href="/index.php"><- Come back to the start page</a>
</div>
    <div class="menu_left">
     Hello world
	</div>

    <div class="content">
	<? 
		$is_set = false;
		if (isset($_POST['username'])) {$usrnm = $_POST['username'];}
		else $usrnm = '';
		if (isset($_POST['info'])) {$info = $_POST['info'];}
		else $info = '';
		$s = '<h3>Welcome to the SimpleForum registration page! To register, enter your username and password in the fields below:</h3>
		<h4>(Note that all symbols in your username and password should be latin letters, numbers or underscores("_") in any sequence.
		Username and password should be up to 200 symbols in length, info - up to 4000)</h4>
		<br />
		<form action="'.$_SERVER['PHP_SELF'].'" method="post">
    	<table border="0">
          <tr>
            <td>Username: </td>
            <td><input name="username" type="text" size="20" value = "'.$usrnm.'"></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td><input name="password" type="password" size="20" /> </td>
          </tr>
		   <tr>
            <td>Your personal information*:</td>
            <td><textarea rows = "10" cols = "40" name = "info" class = "textarea">'.$info.'</textarea> </td>
          </tr>
        </table>
		(Fields, marked with "*", are not necessary to fill)
		<br /><br />
		<input type = "hidden" value = "1" name = "lets_submit">
		<input type = "submit" value = "Send registration request" class="button">';
		if (isset($_POST['lets_submit'])) $s = $s.'<br /><br />
		<font size = "3" color = "red">Please, fill in the necessary fields properly (according to the rules mentioned above)</font>'; 
		
		if (!is_valid_usrnm_or_pw(array($_POST['username'],$_POST['password']))) echo $s;
		
		else //actual registration - put user to the DB
		{	
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
				error_reporting(0);
				$sql = 'insert into USERS(USERNAME,PASSWORD,INFO) 
				values (\''.$username.'\',\''.$password.'\',\''.$info.'\')';
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
					else echo 'Database error appeared. Sorry, you didn\'t register.';
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
	?>
	</form>
    </div>
        </div>
</div>   


<div class="bottom">
	Hello world
</div> 	

</body>
</html>