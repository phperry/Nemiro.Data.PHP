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
	# create client instance
	$client = new PgSql();

	# create a new command with parameters
	$client->Command = new DBCommand('INSERT INTO users (username, date_created) VALUES (@username, @date_created) RETURNING id_users;');
	# @username and @date_created is parameters name, 
	# you can add a values for this parameters
	$client->Command->Parameters->Add('@username')->SetValue('anyname');
	$client->Command->Parameters->Add('@date_created')->SetValue(date('d.m.Y'));
	# execute the command
	$newId = $client->ExecuteScalar();

	echo 'New row added. ID = '.$newId.'<br />';

	# you can change the parameters and execute the command again
	$client->Command->Parameters['@username']->SetValue('newValue');
	$newId = $client->ExecuteScalar();

	echo 'New row added. ID = '.$newId.'<br />';

	# and again
	$client->Command->Parameters['@username']->SetValue('123');
	$client->Command->Parameters['@date_created']->SetValue(date('d.m.Y'));
	$newId = $client->ExecuteScalar();

	echo 'New row added. ID = '.$newId.'<br />';
}
catch (Exception $ex)
{
	echo 'Error: '.$ex->getMessage();
}
?>