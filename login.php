<?php session_start();
require("/tools/oracle_connect.php");
$conn = oracle_connect();
if (isset($_POST['login']) && isset($_POST['password']))
{
	$login = $_POST['login'];
    $password = $_POST['password'];
	$query = "SELECT id
            FROM users
            WHERE login='".$login."' AND password='".$password."'";
	$st = oci_parse($conn, $query);
	oci_execute($st);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Simple Forum login page</title>
<link href="CSS/Const.css" rel="stylesheet" type="text/css" />
</head>

<body>
<center>
<div class="login_window" align="center">
	Please login with your login and password:
    <hr />
	<form action="" method="post">
    <table width="270">
    <tr>
    <td align="left">Login: </td>
    <td> <input name="login" type="text" size="20" /></td>
		
     </tr>
     <tr>
     <td align="left"> Password: </td>
     <td><input name="password" type="password" size="20" />
     </td>
     </tr>
         <tr>
         	
         
         <td align="left">
           <input name="remember" type="checkbox" value="Remember" align="left" /> Remember?
           
          </td>
          <td align="right">
          <input name="submit2" type="reset" value="Clear"/>
           <input name="submit" type="submit" value="Submit" />
     	  </td>
     	</tr>
        <tr>
        <td colspan="2">
        <?
		if (isset($_POST['login']) && isset($_POST['password'])) 
		if ($row = oci_fetch_assoc($st)) 
		{
			$_SESSION['user_id'] = $row['id'];
			echo '<meta http-equiv="refresh" content="0; url=/index.php">';
		}	
		else 
		{
			echo('Login/Password not found. Try again...');
			//echo '<meta http-equiv="refresh" content="3; url=/login.php">';
		}
		?>
        </td>
     
     </table>
    </form>
</div>
</center>
</body>
</html>







