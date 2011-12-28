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
function show_adminpage_path()
{
	return '<a href="index.php">Index</a>&nbsp;>&nbsp;'.'<a href="adminpage.php">Admin Page</a>';
}


function show_add_topic()
{
	if (isset($_SESSION['user_id']))
	{
		echo '<form action ="'.$_SERVER['PHP_SELF'].'?branch_id='.$_GET['branch_id'].'" method = "POST">';
		echo 'Add new topic:';
		echo '<textarea rows = "1" cols = "80" name = "topic_name" class = "textarea"></textarea>';
		echo '<br />';
		echo '<input type = "hidden" name = "lets_post" value = "true">';
		echo '<input type = "submit" value = "Start topic">';
		echo '</form>';
	}
	else 
	{
		echo '<h4>You can\'t start topics, as you\'re a guest.</h4><br />';;
	}
}

function show_add_branch()
{
	
	if (isset($_SESSION['user_id']))
	{
		$user = get_user_info($_SESSION['user_id']);
		if ($user['ACCESS_LEVEL']!='admin')
			;//echo '<h4>You can\'t start topics, as you\'re not admin.</h4><br />';
		else
		{
			echo '<form action ="'.$_SERVER['PHP_SELF'].'" method = "POST">';
			echo 'Add new branch:';
			echo '<textarea rows = "1" cols = "80" name = "branch_name" class = "textarea"></textarea>';
			echo '<br />';
			echo '<input type = "hidden" name = "lets_post" value = "true">';
			echo '<input type = "submit" value = "Create branch">';
			echo '</form>';
		}	
	}
	else 
	{
		;//echo '<h4>You can\'t start topics, as you\'re a guest.</h4><br />';
	}
}

function is_valid_topic($s) //to check if string, entered as a topic, is valid to post
{
	$b = array();
	$ret = true;
	if (!((isset($s))&&(strlen($s)!=preg_match_all('/\s/',$s,$b)))) $ret=false;
	return $ret;
}

function is_valid_branch($s) //to check if string, entered as a topic, is valid to post
{
	$b = array();
	$ret = true;
	if (!((isset($s))&&(strlen($s)!=preg_match_all('/\s/',$s,$b)))) $ret=false;
	return $ret;
}

function get_branch_obj($branch_id)
{
	$sql = 'select * from BRANCHES where BRANCH_ID='.$branch_id;
	echo $sql;
	PutEnv('ORACLE_SID = XE');
	PutEnv('ORACLE_HOME = '.ora_home);
	PutEnv('TNS_ADMIN = '.tns_admin);
	if ($c = oci_new_connect(username,password,db)) 
	{
		//echo 'succesfully connected';
		$st = oci_parse($c,$sql);
		$r = oci_execute($st);
		if ($r)
		{
			$row = oci_fetch_assoc($st);
			$sql = "select ID from OBJECTS where NAME='".$row['BRANCH_NAME']."'";
			echo $sql;
			$st = oci_parse($c,$sql);
			$r = oci_execute($st);
			$row = oci_fetch_assoc($st);
			if ($row)
				return $row['ID'];
			else 
				return 'NOTFOUND';
			
			
		}
		else 
		{
			$err = oci_error($st);
			echo 'Oracle error '.$err['message'].'<br />';
		}
	}
	else 
	{
		$err = oci_error($c);
		echo 'Oracle error '.$err['message'].'<br />';
	}
}

function add_topic()
{
	
	if ((isset($_POST['lets_post']))
	&&(is_valid_topic($_POST['topic_name'])))
	{	
		$topic_name = $_POST['topic_name'];
		$user_id = $_SESSION['user_id'];
		$branch_id= $_GET['branch_id'];
		$sql = 'insert into TOPICS(TOPIC_NAME,USER_ID,BRANCH_ID)	values (\''.
		$topic_name.'\','.
		$user_id.','.
		$branch_id.')';
		//echo $sql;
		
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			//echo 'succesfully connected';
			$st = oci_parse($c,$sql);
			$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
			if ($r)
			{
				//success
				echo '<h3>Your topic added successfully!</h3>';
			}
			else 
			{
				$err = oci_error($st);
				echo 'Oracle error '.$err['message'].'<br />';
			}
		}
		else 
		{
			$err = oci_error($c);
			echo 'Oracle error '.$err['message'].'<br />';
		}
		
		$name = $_POST['topic_name'];
		$pid= get_branch_obj($_GET['branch_id']);
		$sql = 'insert into OBJECTS(ID, NAME,PID)	values (OBJECTS_SEQ.nextval,\''.
		$name.'\','.
		$pid.')';
		//echo $sql;
			
		$st = oci_parse($c,$sql);
		$r = oci_execute($st,OCI_COMMIT_ON_SUCCESS);
		if (!$r)
		{
			$err = oci_error($st);
			echo 'Oracle error '.$err['message'].'<br />';
		}		
	}
}

function add_branch()
{
	
	if ((isset($_POST['lets_post']))
	&&(is_valid_branch($_POST['branch_name'])))
	{	
		$branch_name = $_POST['branch_name'];
		$sql = 'insert into BRANCHES(BRANCH_ID, BRANCH_NAME)	values (BRANCHES_SEQ.nextval,\''.$branch_name.'\')';
		PutEnv('ORACLE_SID = XE');
		PutEnv('ORACLE_HOME = '.ora_home);
		PutEnv('TNS_ADMIN = '.tns_admin);
		if ($c = oci_new_connect(username,password,db)) 
		{
			//echo 'succesfully connected';
			$st = oci_parse($c,$sql);
			$r = oci_execute($st);
			if ($r)
			{
				//success
				oci_commit($c);
				echo '<h3>Your branch added successfully!</h3>';
			}
			else 
			{
				$err = oci_error($st);
				echo 'Oracle error '.$err['message'].'<br />';
			}
		}
		else 
		{
			$err = oci_error($c);
			echo 'Oracle error '.$err['message'].'<br />';
		}
	}
}

function show_sitemap()
{
	echo "<h4> Forum Map </h4>";
	$conn = oracle_connect();
	$query = "SELECT NAME, LEVEL
				FROM OBJECTS
				START WITH pid IS NULL
				CONNECT BY PRIOR id = pid
				ORDER SIBLINGS BY NAME";
	$st = oci_parse($conn, $query);
	if (oci_execute($st))
	{
		while ($row = oci_fetch_assoc($st))
		{
			for ($i=0; $i<4*$row['LEVEL']; $i++) 
				echo '&nbsp;';
			echo $row['NAME'];
			echo '<br/ >';
		}
	}	
	else 
		return 'oracle error';
}



?>