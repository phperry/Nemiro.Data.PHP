<?php

# include config file
require_once 'config.php';

# include the db client classes file (use own path of the file location)
require_once '../Import.php';

# import client for PostgreSQL
use Nemiro\Data\PgSql as PgSql;
# import command class
use Nemiro\Data\DBCommand as DBCommand;

# best practice is to use 'try { } catch { }' blocks
try
{
	# create client instance with connection settings from config.php
	$client = new PgSql();

	# create client instance with specifed connection settings
	$client2 = new PgSql('localhost', 'test', '5QKfHhB323EM', 'test_db', 5432);
	
	# create command
	$command = new DBCommand('SELECT * FROM users LIMIT @limit');
	$command->Parameters->Add('@limit', 10);

	# set command to client
	$client->Command = $command;

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

	# set command to client2
	$client2->Command = $command;

	# set a new value to the parameter (for example)
	$command->Parameters['@limit']->SetValue(5);

	# execute the command and get table
	$table = $client2->GetTable();

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