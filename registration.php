<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?	
	require('/tools/oracle.conf');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>SimpleForum User Registration</TITLE>
<link href="CSS/Const.css" rel="stylesheet" type="text/css">
</head>
<body>   	
<div class="document">      

<div class="header">
 Hello world
</div>
    <div class="menu_left">
     Hello world
	</div>

    <div class="content">
	<? if ((!isset($_POST['lets_submit']))||(!((isset($_POST['username']))&&(isset($_POST['password']))))) {?>
    <h4>Welcome to the SimpleForum registration page!
    Here you can register your account. Please enter your username and password in the fields below:</h4>
	<br />
    <form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
    	<table border="0">
          <tr>
            <td>Username: </td>
            <td><input name="username" type="text" size="20"></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td><input name="password" type="password" size="20" /> </td>
          </tr>
		   <tr>
            <td>Your personal information*:</td>
            <td><textarea rows = '10' cols = '40' name = 'info' class = "textarea"></textarea> </td>
          </tr>
        </table>
		(Fields, marked with "*", are not necessary to fill)
		<br /><br />
		<input type = "hidden" value = "1" name = "lets_submit">
		<input type = "submit" value = "Send registration request" class="button">
	<? }
		else  if ((isset($_POST['username'],$_POST['password']))&&(!empty($_POST['username']))&&(!empty($_POST['password']))) 
		{	
			echo 'username: \''.$_POST['username'].'\' <br />';
			echo 'password: \''.$_POST['password'].'\'';
			PutEnv('ORACLE_SID = XE');
			PutEnv('ORACLE_HOME = '.ora_home);
			PutEnv('TNS_ADMIN = '.tns_admin);
			if ($c = oci_new_connect(username,password,db)) 
			{
				$info = str_replace('\'','\'\'',$_POST['info']);
				$username = str_replace('\'','\'\'',$_POST['username']);
				$password = str_replace('\'','\'\'',$_POST['password']);
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
					echo 'Oracle error '.$err['message'].'<br />';
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
	<div class="menu_right">
     Hello world
    </div>
</div>   


<div class="bottom">
	Hello world
</div> 	

</body>
</html>