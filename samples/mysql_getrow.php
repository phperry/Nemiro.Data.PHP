<?php

# include config file
require_once 'config.php';

# include the db client classes file (use own path of the file location)
require_once '../Import.php';

# import client for MySql
use Nemiro\Data\MySql as MySql;
# import command class
use Nemiro\Data\DBCommand as DBCommand;

# best practice is to use 'try { } catch { }' blocks
try
{
	# create client instance
	$client = new MySql();

	# create command
	$client->Command = new DBCommand('SELECT * FROM users LIMIT 1');

	# execute the command and get single row
	$row = $client->GetRow();

	if ($row != NULL)
	{
		# get filed name list of the row
		$fieldsName = array_keys($row);
		echo '<table style="width:500px;">';
		echo '<tbody>';
		# output data of the row
		foreach ($fieldsName as $field)
		{
			echo '<tr>';
			echo '<td>'.$field.'</td>';
			echo '<td><input type="text" value="'.$row[$field].'" style="width:100%" /></td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}
	else
	{
		echo 'The query returned no results...';
	}
}
catch (Exception $ex)
{
	echo 'Error: '.$ex->getMessage();
}
?>