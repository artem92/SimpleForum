<?php
function get_user_id($login, $password)
{
require_once("tools/oracle_utils.php");
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

function show_message($conn, $post_id)
{
	$result = '<div class="post" >';
	$result .= '<div class="post-header" >User </div><hr>';
	$result .='<div class="post-content" > hello </div>';
	$result .='</div';
	return $result;
}
?>