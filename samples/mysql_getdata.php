<?php

# include config file
require_once 'config.php';

# include the db client classes file (use own path of the file location)
require_once '../Import.php';

# import client for MySql
use Nemiro\Data\MySql as MySql;
# import command class
use Nemiro\Data\DBCommand as DBCommand;
# import parameters type list
use Nemiro\Data\DBParameterType as DBParameterType;

# best practice is to use 'try { } catch { }' blocks
try
{
	# create a new client instance
	$client = new MySql();

	# create commands
	$firtCommand = new DBCommand('SELECT * FROM users LIMIT @from, @to');
	$firtCommand->Parameters->Add('@to', 20, DBParameterType::Integer);
	$firtCommand->Parameters->Add('@from', 10, DBParameterType::Integer);

	$secondCommand = new DBCommand
	(
		'SELECT m.* FROM messages AS m '.
		'INNER JOIN (SELECT id_users FROM users LIMIT @users_from, @users_to) AS u '.
		'ON m.id_users = u.id_users '.
		'LIMIT @messages_limit'
	);
	$secondCommand->Parameters->Add('@messages_limit', 50, DBParameterType::Integer);
	$secondCommand->Parameters->Add('@users_from', $firtCommand->Parameters['@from']->Value, DBParameterType::Integer);
	$secondCommand->Parameters->Add('@users_to', $firtCommand->Parameters['@to']->Value, DBParameterType::Integer);

	$thirdCommand = 'SELECT * FROM stat';

	# set commands to client instance
	$client->Command = array($firtCommand, $secondCommand, $thirdCommand);

	# execute the command and get DataSet
	$data = $client->GetData();

	if (count($data) > 0)
	{
		# read tables
		$index = 1;
		foreach ($data as $table)
		{
			echo '<h2>Table #'.$index.'</h2>';
			$index++;
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