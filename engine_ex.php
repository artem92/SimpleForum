<?
require_once('forum.config');
require_once('engine.php');
require_once('content.php');

function get_branch_info($branch_id)
{
	$conn = oracle_connect();
	$query = "SELECT *
              FROM branches
              WHERE  branch_id = ".$branch_id;
	$st = oci_parse($conn, $query);
	if (oci_execute($st))
	{
		$row = oci_fetch_assoc($st); 
		return $row;
	}	
	else 
		return 'oracle error';
}

function show_branch_path($branch_id)
{
	$branch = get_branch_info($branch_id);
	return '<a href="index.php">Index</a>&nbsp;>&nbsp;'.'<a href="viewbranch.php?branch_id='.$branch_id.'"> '.$branch['BRANCH_NAME'].'</a>';
}

function get_topic_info($topic_id)
{
	$conn = oracle_connect();
	$query = "SELECT *
              FROM topics
              WHERE  topic_id = ".$topic_id;
	$st = oci_parse($conn, $query);
	if (oci_execute($st))
	{
		$row = oci_fetch_assoc($st); 
		return $row;
	}	
	else 
		return 'oracle error';
}

function show_topic_path($topic_id)
{
	$topic = get_topic_info($topic_id);
	$branch = get_branch_info($topic['BRANCH_ID']);
	return '<a href="index.php">Index</a>&nbsp;>&nbsp;'.'<a href="viewbranch.php?branch_id='.$branch['BRANCH_ID'].'"> '.$branch['BRANCH_NAME'].'</a>&nbsp;>&nbsp;<a href="viewtopic.php?topic_id='.$topic['TOPIC_ID'].'">'.$topic['TOPIC_NAME'].'</a>';
}

function show_index_path()
{
	return '<a href="index.php">Index</a>';
}

function show_profile_path()
{
	return '<a href="index.php">Index</a>&nbsp;>&nbsp;'.'<a href="profile.php">Profile</a>';
}


function show_registration_path()
{
	return '<a href="index.php">Index</a>&nbsp;>&nbsp;'.'<a href="registration.php">Registration</a>';
}


?>