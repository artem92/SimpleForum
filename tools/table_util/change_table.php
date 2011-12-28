<?
	require_once('forum.config');
?>

<html>
	<head>
		<link rel=StyleSheet href="style.css" type = "text/css" media=screen>
		<title>Work with table</title>
		<script type = "text/javascript">
			function create_new_elem(k)
			{
				//alert('it works!');
				//alert(k);
				var table = document.getElementById('table_id');
				var rowCount = table.rows.length;
				var cellCount = table.rows[0].cells.length;
				var row = table.insertRow(rowCount);
				
				var cell = row.insertCell(0);
				var element = document.createElement('input');
				element.setAttribute('type', 'checkbox');
				cell.appendChild(element);
				
				for (var i = 1; i<cellCount; i++)
				{
					cell = row.insertCell(i);
					element = document.createElement('input');
					element.setAttribute('type', 'text');
					cell.appendChild(element);
				}
			}
		</script>
	</head>
	<body>
		<?php
			$table = $_POST['table'];
			//echo $table;
			if (isset($table))
			{
				PutEnv('ORACLE_SID = XE');
				PutEnv('ORACLE_HOME = '.ora_home);
				PutEnv('TNS_ADMIN = '.tns_admin);
				error_reporting(0);
				if ($c = oci_new_connect(username,password,db)) 
				{
					error_reporting(E_ALL);
					//echo 'succesfully connected to orcl!';
					//OCILogoff($c);
					
					echo '<a href = "'.table_util_path.'index.php"><----- Come Back</a>';
					echo '<h2 align = "center">Work with table '.$table.'</h2>';
					
					$foreign_keys = get_foreign_keys($c,$table,$username);
					$table_columns = get_table_columns($c,$table); //get names and data types of table columns
					$check_array = array(); //array for checkboxes, will be filled after submitting
					
					
					/*foreach($table_columns as $key=>$val)
						echo $key.'='.$val.'<br />';*/
					
					//submitting posted changes to the DB
					if (isset($_POST['row_ct'])) $row_ct = $_POST['row_ct'];
					else $row_ct = 0;
					
					$table_after_submit = array();
					for ($i=0; $i<=$row_ct; $i++)
					{
						if (isset($_POST['check_'.$i])) 
						{
							$check_array[$i] = $_POST['check_'.$i];
							//echo 'check['.$i.']: '.$check_array[$i].' ;';
						}
						foreach($table_columns as $key=>$val)
						{
							if (isset($_POST[$key.'_'.$i])) 
							{
								$table_after_submit[$key][$i] = $_POST[$key.'_'.$i];
								//echo $key.'['.$i.']: '.$table_after_submit[$key][$i].' ;';
							}
						}
						if (isset($_POST['T_ROWID_'.$i]))
						{	
							$key = 'T_ROWID';
							$table_after_submit[$key][$i] = $_POST[$key.'_'.$i];
							//echo $key.'['.$i.']: '.$table_after_submit[$key][$i].' ;';
						}
						//echo '<br />';
					}
					
					if ((isset($_POST['lets_submit']))&&($_POST['lets_submit']=='ok'))
					{
						for ($i=0; $i<=$row_ct; $i++)
						{
							if (isset($check_array[$i])) 
							{
								$sql = 'delete from '.$table.' where ROWID = chartorowid(\''.$table_after_submit['T_ROWID'][$i].'\')';
							}
							else 
							{
								$sql = 'update '.$table.' set ';
								$j = 0;
								foreach ($table_after_submit as $key=>$val)
								{
									if ($key!='T_ROWID')
									{
										if ($j!=0) $sql = $sql.', ';
										
										$sql = $sql.''.$key.' = ';
										if (!is_numeric_oracle10g($table_columns[$key])) 
										{
											$sql = $sql.'\'';
											$table_after_submit[$key][$i] = str_replace('\'','\'\'',$table_after_submit[$key][$i]);
										}
										$sql = $sql.''.$table_after_submit[$key][$i];
										if (!is_numeric_oracle10g($table_columns[$key])) $sql = $sql.'\'';
										$sql = $sql.' ';
										
										$j = $j + 1;
									}
								}
								$sql = $sql.'where ROWID = chartorowid(\''.$table_after_submit['T_ROWID'][$i].'\')';
							}
							//echo $sql.'<br />';
							
							
							
							$st = oci_parse($c,$sql);
							error_reporting(0);
							if (oci_execute($st))
							{
								//success
							}
							else 
							{
								$err = oci_error($st);
								echo 'Oracle error '.$err['message'].'<br />';
							}
							oci_free_statement($st);
							error_reporting(E_ALL);
						}
						oci_commit($c);
					}
					
					echo '<table border = "2" align = "center" >';
					echo '<form action = "'.$_SERVER['PHP_SELF'].'" method = "POST">';
					echo '<tr>';
					echo '<td style ="padding: 25px;">';
					echo '<h3>Adding a new element</h3>';
					foreach ($table_columns as $key=>$val)
					{
						echo $key.': ';
						
						$name = 'new_'.$key;
						$tmp_row = false;

						foreach($foreign_keys as $r)
							if ($r['column_name'] == $key) $tmp_row = $r;
						
						if ($tmp_row!==false)
						{	
							echo '<select name = "'.$name.'">';
							
							foreach($tmp_row['table_content'] as $r)
								{
									echo '<option value ="'.$r[$key].'">'.$r[$key].' ((';
									foreach($r as $k=>$v)
										if ($k!='T_ROWID') echo '"'. $k.'": '.$v.'; ';
										echo ') from table '.$tmp_row['table_name'].')';
									echo '</option>';
								}
							echo '</select>';

						}
						else echo '<input type = "text" value = "" name = "'.$name.'">';
						echo '<br />';
					}
					echo '<input type = "hidden" name = "table" value ="'.$table.'">';
					echo '<input type = "hidden" name = "username" value = "'.$username.'">';
					echo '<input type = "hidden" name = "password" value = "'.$password.'">';
					echo '<input type = "hidden" name = "db" value = "'.$db.'">';
					echo '<input type = "hidden" name = "ora_home" value = "'.$ora_home.'">';
					echo '<input type = "hidden" name = "tns_admin" value = "'.$tns_admin.'">';
					
					echo '<input type = "submit" value = "Add a new element" class = "button">';
					echo '</td>';
					echo '</tr>';
					echo '</form>';
					echo '</table>';
					

					//adding new element
					$b = true;
					foreach($table_columns as $key=>$val)
						if (!isset($_POST['new_'.$key])) $b = false;
					if ($b)
					{
						$ct = 0;
						$sql = 'insert into '.$table.' values (';
						foreach($table_columns as $key=>$val) 
						{
							if ($ct!=0) $sql = $sql.', ';
							if (!is_numeric_oracle10g($table_columns[$key])) $sql = $sql.'\'';
							$sql = $sql.''.$_POST['new_'.$key];
							if (!is_numeric_oracle10g($table_columns[$key])) $sql = $sql.'\'';
							$sql = $sql.' ';
							$ct = $ct + 1;
						}
						$sql = $sql.')';
						//echo $sql;
					
						$st = oci_parse($c,$sql);
						error_reporting(0);
						if (oci_execute($st))
						{
							//success
						}
						else 
						{
							$err = oci_error($st);
							echo 'Oracle error '.$err['message'].'<br />';
						}
						oci_free_statement($st);
						error_reporting(E_ALL); 
					}
					
					//header of the table
					echo '<center>';
					echo '<form action = "'.$_SERVER['PHP_SELF'].'" method = "POST">';
					echo '<br />';
					echo '<table id = "table_id" border = "2" align = "center">';
					
					echo '<tr>';
					
					$keys = array_keys($table_columns);
					echo '<th>';
					echo 'Delete?';
					echo '</th>';
					foreach ($keys as $key)
					{
						echo '<th>';
						echo $key;
						echo '</th>';
					}
					echo '</tr>';
					
					
					$table_array = get_table_content($c,$table);
					$row_ct = -1;
					foreach($table_array as $row) 
					{
						$row_ct = $row_ct + 1;
						
						echo '<tr>';
						echo '<td>';
						echo '<input type = "checkbox" value = "1" name = "check_'.$row_ct.'">';
						echo '</td>';
						foreach($row as $key=>$val)
						{
							$name = $key.'_'.$row_ct;
							if ($key=='T_ROWID') echo '<input type = "hidden" value = "'.$val.'" name = "'.$name.'">';
							else 
							{
								echo '<td>';
								
								$tmp_row = false;

								foreach($foreign_keys as $r)
									if ($r['column_name'] == $key) $tmp_row = $r;
								
								if ($tmp_row!==false)
								{
									
									echo '<select name = "'.$name.'">';
									
									foreach($tmp_row['table_content'] as $r)
										if ($r[$key] == $val) $current_row = $r;
									
									echo '<option value ="'.$val.'">'.$val.' ((';
									foreach($current_row as $k=>$v)
										if ($k!='T_ROWID') echo '"'. $k.'": '.$v.'; ';
										echo ') from table '.$tmp_row['table_name'].')';
									echo '</option>';
									
									foreach($tmp_row['table_content'] as $r)
										{
											if ($r[$key]!=$val)
											{
												echo '<option value ="'.$r[$key].'">'.$r[$key].' ((';
												foreach($r as $k=>$v)
													if ($k!='T_ROWID') echo '"'. $k.'": '.$v.'; ';
													echo ') from table '.$tmp_row['table_name'].')';
												echo '</option>';
											}
										}
									echo '</select>';

								}
								else echo '<input type = "text" value = "'.$val.'" name = "'.$name.'">';
								echo '</td>';
							}
						}
						echo '</tr>';
					}
					echo '</table>';
					echo '<br />';
					echo '<input type = "hidden" name = "table" value ="'.$table.'">';
					echo '<input type = "hidden" name = "lets_submit" value ="ok">';
					echo '<input type = "hidden" name = "row_ct" value ="'.$row_ct.'">';
					echo '<input type = "hidden" name = "username" value = "'.$username.'">';
					echo '<input type = "hidden" name = "password" value = "'.$password.'">';
					echo '<input type = "hidden" name = "db" value = "'.$db.'">';
					echo '<input type = "hidden" name = "ora_home" value = "'.$ora_home.'">';
					echo '<input type = "hidden" name = "tns_admin" value = "'.$tns_admin.'">';
					echo '<input type = "submit" align = "right" value = "Submit changes (COMMIT)"
					class="button">';
					echo '</form>';
					echo '<form action = "'.$_SERVER['PHP_SELF'].'" method = "POST">';
					echo '<input type = "hidden" name = "table" value ="'.$table.'">';
					echo '<input type = "submit" align = "left" value = "Cancel changes (ROLLBACK)"
					class="button">';
					echo '</form>';
					echo '</center>';
					
				}
				else 
				{
					$err = oci_error();
					echo 'Oracle error '.$err['message'];
					error_reporting(E_ALL);
				}	
			}
			else echo 'this page is not to be used directly';
		?>
	</body>
</html>