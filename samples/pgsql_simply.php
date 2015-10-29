<?php

# include config file
require_once 'config.php';

# include the db client classes file (use own path of the file location)
require_once '../Import.php';

# import client for PostgreSQL
use Nemiro\Data\PgSql as PgSql;
# import command class
use Nemiro\Data\DBCommand as DBCommand;
# import parameters type list
use Nemiro\Data\DBParameterType as DBParameterType;

# best practice is to use 'try { } catch { }' blocks
try
{
	# create client instance
	$client = new PgSql();

	# you can specify the text of the query into the Command property
	$client->Command = 'SELECT * FROM users';

	# and execute the query by any method:
	# $client->GetRow();   # return single row
	# $client->GetTable(); # return all rows
	# $client->GetData();  # return array tables

	# for example, get all rows
	$table = $client->GetTable();
	var_dump($table);

	# you can build a query from the input parameters,
	# but you will need to check the type and value of incoming data
	# it is bad practice
	$client->Command = 'DELETE FROM users WHERE id_users = '.(int)$_GET['id'];

	# it is best to use parameterized queries
	$client->Command = new DBCommand('DELETE FROM users WHERE id_users = @id_users');
	$client->Command->Parameters->Add('@id_users', $_GET['id'], DBParameterType::Integer);
	# or
	# $client->Command->Parameters->Add('@id_users', $_GET['id']);
	# or
	# $client->Command->Parameters->Add('@id_users').SetValue($_GET['id']);
	# it is a safe solution

	# execute the query
	$affectedRows = $client->ExecuteNonQuery();

	echo 'Deleted: '.$affectedRows.'<br />';
}
catch (Exception $ex)
{
	echo 'Error: '.$ex->getMessage();
}
?>