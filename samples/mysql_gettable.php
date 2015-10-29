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
	$client->Command = new DBCommand('SELECT * FROM users LIMIT @limit');
	$client->Command->Parameters->Add('@limit', 10);

	# execute the command and get table
	$table = $client->GetTable();

	if (count($table) > 0)
	{
		# output columns of the table
		$columns = array_keys($table[0]);
		echo '<table>';
		echo '<thead><tr>';
		foreach ($columns as $column)
		{
			echo '<td>'.$column.'</td>';
		}
		echo '</tr></thead>';

		# output the table rows
		echo '<tbody>';
		foreach ($table as $row)
		{
			echo '<tr>';
			foreach ($row as $field)
			{
				echo '<td>'.(isset($field) ? $field : 'NULL').'</td>';
			}
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