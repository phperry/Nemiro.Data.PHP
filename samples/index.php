<?php

# include config file
require_once 'config.php';

# include the db client classes file (use own path of the file location)
require_once '../Import.php';

# import client classes
use Nemiro\Data\MySql as MySql;
use Nemiro\Data\PgSql as PgSql;
# import command class
use Nemiro\Data\DBCommand as DBCommand;

# use five simple methods for working with databases:
# ExecuteNonQuery, ExecuteScalar, GetData, GetTable and GetRow.

#region MySql

# create client instance for MySql
$client = new MySql();

#region parametized queries and no parametized

# create a new command with parameters
$client->Command = new DBCommand('INSERT INTO users (username, date_created) VALUES (@username, @date_created)');
# @username and @date_created is parameters name, 
# you can add a values for this parameters
$client->Command->Parameters->Add('@date_created')->SetValue(date('Y-m-d H-i-s'));
$client->Command->Parameters->Add('@username')->SetValue('anyname');
# execute the command
$newId = $client->ExecuteScalar();

echo 'Added a new row. ID = '.$newId.'<br />';

# you can change the parameters and execute the command again
$client->Command->Parameters['@username']->SetValue('newValue');
$newId = $client->ExecuteScalar();

echo 'Added a new row. ID = '.$newId.'<br />';

# you can create command from string,
# but without parameters
$client->Command = 'DELETE FROM users WHERE id_users = 1';
$affectedRows = $client->ExecuteNonQuery();

echo 'Deleted: '.$affectedRows.'<br />';

#endregion
#region get a table

$client->Command = 'SELECT * FROM users';
$table = $client->GetTable();

# output the table rows
echo '<pre>';
foreach($table as $row)
{
	print_r($row);
}
echo '</pre>';

#endregion
#region get a single row

$client->Command = 'SELECT * FROM users LIMIT 1';
$row = $client->GetRow();

# output the table rows
if ($row != NULL)
{
	var_dump($row);
}
else
{
	echo 'The query returned no results.<br />';
}

#endregion
#region multiple results (DataSet)

$firtCommand = new DBCommand('SELECT * FROM users WHERE email LIKE @email_search');
$firtCommand->Parameters->Add('@email_search', '%@kbyte.ru');

$secondCommand = new DBCommand('SELECT * FROM messages WHERE id_users IN (SELECT id_users FROM users WHERE email LIKE @email_search)');
$secondCommand->Parameters->Add('@email_search', '%@kbyte.ru');

$thirdCommand = 'SELECT * FROM stat';

# etc...

# create command
$client->Command = array($firtCommand, $secondCommand, $thirdCommand);

# and execute all command
$data = $client->GetData();

# in the output will be an array with the results of each command

echo '<pre>';
foreach ($data as $table)
{
	print_r($table);
}
echo '</pre>';

#endregion
#region errors

# use blocks try {} catch {} to trap an error

try
{
	$client->Command = new DBCommand('ВЫБРАТЬ ВСЁ ИЗ [ТАБЛИЦА123] ГДЕ имя = @имя'); # invalid query
	$client->Command->Parameters->Add('@имя')->SetValue('test');
	$client->ExecuteNonQuery();
} 
catch (Exception $ex)
{
	echo 'Error: '.$ex->getMessage();
}

#endregion

#endregion
#region  PostgreSQL

# create client instance for PostgreSQL
$pg_client = new PgSql();

#region parametized queries and no parametized

# create a new command with parameters
$pg_client->Command = new DBCommand('INSERT INTO users (username, date_created) VALUES (@username, @date_created) RETURNING id_users;');
# @username and @date_created is parameters name, 
# you can add a values for this parameters
$pg_client->Command->Parameters->Add('@date_created')->SetValue(date('d.m.Y'));
$pg_client->Command->Parameters->Add('@username')->SetValue('test345');
# execute the command
$newId = $pg_client->ExecuteScalar();

echo 'Added a new row. ID = '.$newId.'<br />';

# you can change the parameters and execute the command again
$pg_client->Command->Parameters['@username']->SetValue('newValue123');
$newId = $pg_client->ExecuteScalar();

echo 'Added a new row. ID = '.$newId.'<br />';

# you can create command from string,
# but without parameters
$pg_client->Command = 'DELETE FROM users WHERE id_users = 1';
echo 'Deleted: '.$pg_client->ExecuteNonQuery().'<br />';

#endregion
#region get a table

$pg_client->Command = 'SELECT * FROM users';
$table = $pg_client->GetTable();

# output the table rows
echo '<pre>';
foreach($table as $row)
{
	print_r($row);
}
echo '</pre>';

#endregion
#region get a single row

$pg_client->Command = 'SELECT * FROM users LIMIT 1';
$row = $pg_client->GetRow();

# output the table rows
if ($row != NULL)
{
	var_dump($row);
}
else
{
	echo 'The query returned no results.<br />';
}

#endregion
#region multiple results (DataSet)

$firtCommand = new DBCommand('SELECT * FROM users WHERE username LIKE @username');
$firtCommand->Parameters->Add('@username', '%test%');

$secondCommand = new DBCommand('SELECT * FROM users');
# etc...

# create command
$pg_client->Command = array($firtCommand, $secondCommand);

# and execute all command
$data = $pg_client->GetData();

# in the output will be an array with the results of each command

echo '<pre>';
foreach ($data as $table)
{
	print_r($table);
}
echo '</pre>';

#endregion

#endregion

?>