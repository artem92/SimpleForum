<?php
	//configuration
	require_once('../../forum.config');

?>
			
<html>
	<head>
		<title> Lab work 7 </title>
	</head>
	<body>
		<center>
			<h2> Lad work 7 </h2>
			<h4> (variant 9, by student Kachanovskyy) </h4>
			<table>
				<tr>
					<td>
						<form action = <? echo $_SERVER[PHP_SELF] ?> method = 'POST'>
							<h3> Enter SQL command to the text field </h3>
							<h4> (SQL statements should not end with a semi-colon (";"). PL/SQL statements should end with a semi-colon (";").) </h4>
							<!--<input type = 'text' class = 'inputtext'>-->
							<textarea rows = '10' cols = '70' name = 'sql' id = 'sql'><? echo $_POST['sql'] ?></textarea>
							<br />
							<input type = 'checkbox' name = 'to_commit' value = 'true'>commit after executing
							<br /><br />
							<input type = 'submit' value = 'Execute' style = 'height: 5em; width: 10em;'>
						</form>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
			</table>
		</center>
		<?php
			PutEnv('ORACLE_SID = XE');
			PutEnv('ORACLE_HOME = '.ora_home);
			PutEnv('TNS_ADMIN = '.tns_admin);
			if ($c = oci_new_connect(username,password,db)) 
			{
				//echo 'succesfully connected to orcl!';
				//OCILogoff($c);
				$to_commit = $_POST['to_commit'];
				$sql = $_POST['sql'];
				if (isset($sql)) 
				{
					$st = oci_parse($c,$sql);
					error_reporting(0);
					$r = oci_execute($st,OCI_NO_AUTO_COMMIT);
					if ($r)
					{
						//echo 'command ran succesfully';
						//oci_commit($st);
						
						if (oci_statement_type($st)=='SELECT')
						{
							$row = oci_fetch_assoc($st);
							if ($row)
							{
								echo '<br />';
								echo '<table border = "2" align = "center">';
								echo '<tr>';
								$keys = array_keys($row);
								foreach ($keys as $key)
								{
									echo '<th>';
									echo $key;
									echo '</th>';
								}
								echo '</tr>';
								echo '<tr>';
									foreach($row as $val)
									{
										echo '<td>';
										echo $val;
										echo '</td>';
									}
								echo '</tr>';
								while ($row = oci_fetch_assoc($st))
								{
									echo '<tr>';
									foreach($row as $val)
									{
										echo '<td>';
										echo $val;
										echo '</td>';
									}
									echo '</tr>';
								}
								echo '</table>';
							}
						}
						if ($to_commit == 'true') oci_commit($c);
					}
					else 
					{
						$err = oci_error($st);
						echo 'Oracle error '.$err['message'];
					}
					oci_free_statement($st);
					error_reporting(E_ALL);
				}
			}
			else 
			{
				$err = oci_error($c);
				echo 'Oracle error '.$err['message'];
			}
			
			
			
		?>
		<form action = <? echo table_util_path.'change_table.php' ?> align = 'center' method = 'POST'>
			<h3> Or select the table to work with it </h3>
			<select name = 'table'>
			<?php
				$st = oci_parse($c,'select * from user_tables');
				if (oci_execute($st,OCI_DEFAULT))
				{
					$row = oci_fetch_assoc($st);
					if ($row)
					{
						$val = $row['TABLE_NAME'];
						echo '<option value = '.$val.'>'.$val.'</option>';
						while ($row = oci_fetch_assoc($st))
						{
							$val = $row['TABLE_NAME'];
							echo '<option value = '.$val.'>'.$val.'</option>';
						}
					}
				}
				else 
				{
					$err = oci_error();
					echo 'Oracle error '.$err['message'];
				}
			?>
			</select>
			<input type = "hidden" name = "username" value = "<? echo strtoupper(username) ?>">
			<input type = "hidden" name = "password" value = "<? echo password ?>">
			<input type = "hidden" name = "db" value = "<? echo db ?>">
			<input type = "hidden" name = "ora_home" value = "<? echo ora_home ?>">
			<input type = "hidden" name = "tns_admin" value = "<? echo tns_admin ?>">
			<input type = 'submit' value = 'OK' style = 'width : 5em' >
		</form>
	</body>
</html>