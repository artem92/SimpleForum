<?php 
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
	Please login with your login and password:
    <hr />
	<form action="" method="post">
    <table width="150">
    <tr>
    <td align="left">Login: </td>
    <td> <input name="login" type="text" size="13" /></td>
		
     </tr>
     <tr>
     <td align="left"> Password: </td>
     <td><input name="password" type="password" size="13" />
     </td>
     </tr>
         <tr>
         	
         
         <td align="left">
           <input name="remember" type="checkbox" value="Remember" align="left" /> Remember?
           
          </td>
          <td align="right">
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






