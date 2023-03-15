<?php

session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();


backup_tables();

/* backup the db OR just a table */
function backup_tables($tables = '*')
{
	
	//$link = mysql_connect($host,$user,$pass);
	//mysql_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysqli_query($GLOBALS['conn'], 'SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	$return = '';
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysqli_query($GLOBALS['conn'], 'SELECT * FROM '.$table);
		$num_fields = mysqli_num_fields($result);
		//$return = '';
		//$return.= 'DROP TABLE '.$table.';';
		//$return.= '';
		$row2 = mysqli_fetch_row(mysqli_query($GLOBALS['conn'], 'SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
	echo"<script>
	alert('Database Backup successfully done.');
	</script>";
	redirect('index.php?resp=addsucc');
}
?>