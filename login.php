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
	<form action="/index.php" method="post">
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
       
        </td>
     
     </table>
    </form>
</div>
</center>
</body>
</html>