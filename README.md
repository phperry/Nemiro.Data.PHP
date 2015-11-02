### Nemiro.Data.PHP [![Latest Stable Version](https://poser.pugx.org/aleksey.nemiro/nemiro.data.php/v/stable)](https://packagist.org/packages/aleksey.nemiro/nemiro.data.php) [![Total Downloads](https://poser.pugx.org/aleksey.nemiro/nemiro.data.php/downloads)](https://packagist.org/packages/aleksey.nemiro/nemiro.data.php) [![License](https://poser.pugx.org/aleksey.nemiro/nemiro.data.php/license)](https://packagist.org/packages/aleksey.nemiro/nemiro.data.php)

**Nemiro.Data.PHP** is a small set of utility classes for working with databases **MySql** and **PostgreSQL**.

To work with the databases used five simple methods: **ExecuteNonQuery**, **ExecuteScalar**, **GetData**, **GetTable** and **GetRow**.

The classes allow you to use parameterized queries, which makes working with databases secure.

**Nemiro.Data.PHP** is licensed under the **Apache License Version 2.0**.

### Features

* Client for MySql;
* Client for PostgreSQL;
* A single interface to work with different data providers;
* Automatic control of database connections;
* Parameterized queries.

### System Requirements

* PHP 5 >= 5.3;
* MySQL >= 5.6;
* PostgreSQL >= 7.4.

**NOTE:** Working with the earlier versions just has not been tested.

### Supports

Further support and development of the project is not planned. Welcome to **.NET** ;-)

### How to use the project?

The files of the project are made in **[Visual Studio 2013](https://www.visualstudio.com/)** with the extension **[PHP Tools for Visual Studio](https://visualstudiogallery.msdn.microsoft.com/6eb51f05-ef01-4513-ac83-4c5f50c95fb5)**.

To use the classes in your own projects, it is recommended to put all the solution files in a folder **\Nemiro\Data** (corresponds to the namespace).

### How to use the classes?

#### Configuration

By default, classes use the database connection settings of the following constants:

```PHP
// MySql
define('MYSQL_DB_NAME', '%your database name here%');
define('MYSQL_DB_USER', '%your database username here%');
define('MYSQL_DB_PASSWORD', '%your database password here%');
define('MYSQL_DB_HOST', 'localhost');
define('MYSQL_DB_PORT', 3306);
define('MYSQL_DB_MODE', 2);

// PostgreSQL
define('PGSQL_DB_NAME', '%your database name here%');
define('PGSQL_DB_USER', '%your database username here%');
define('PGSQL_DB_PASSWORD', '%your database password here%');
define('PGSQL_DB_HOST', 'localhost');
define('PGSQL_DB_PORT', 5432);
define('PGSQL_DB_MODE', 1);
```

The **DB_MODE** may be one of the following:

* 0 - manual;
* 1 - auto - open and close for each request;
* 2 - smart (recomended).

You can use individual connection settings, which must be specified when you create an instance of a database client.

#### Including files

To use the database clients, you must include the following files:

```PHP
require_once './Nemiro/Data/Import.php';
```

or

```PHP
require_once './Nemiro/Data/MySql.php';
require_once './Nemiro/Data/PgSql.php';
```

#### Importing namespaces

For convenience, you can import the necessary classes in your code:

```PHP
// client for MySql
use Nemiro\Data\MySql as MySql;
// client for PostgreSQL
use Nemiro\Data\PgSql as PgSql;
// query builder
use Nemiro\Data\DBCommand as DBCommand;
```

#### Examples of use

The following example creates a simple query to select all records from the table `[messages]`.

Records obtained by the **GetTable** method, which returns an array of rows.

```PHP
// create client instance for MySql
$client = new MySql();

// create a new command
$client->Command = new DBCommand('SELECT * FROM messages');

// get table
$table = $client->GetTable();

// output the table rows
echo '<pre>';
foreach($table as $row)
{
	print_r($row);
}
echo '</pre>';
```

The following example creates a parameterized query to add records to the table `[users]`.

The query is executed by the **ExecuteScalar** method, which returns the ID of added record.

```PHP
// create client instance for MySql
$client = new MySql();

// create a new command
$client->Command = new DBCommand
(
	'INSERT INTO users (username, date_created) '.
	'VALUES (@username, @date_created)'
);

// @username and @date_created is parameters name, 
// add a values for this parameters
$client->Command->Parameters->Add('@date_created')->SetValue(date('Y-m-d H-i-s'));
$client->Command->Parameters->Add('@username')->SetValue('anyname');

// execute the command
$newId = $client->ExecuteScalar();

echo 'ID = '.$newId;
```

The following example creates multiple queries and executed by the **GetData** method, which returns an array of tables.

```PHP
// create client instance for MySql
$client = new MySql();

// create commands
$firtCommand = new DBCommand('SELECT * FROM users WHERE is_blocked = 0');

$secondCommand = new DBCommand
(
	'SELECT * FROM messages WHERE id_users IN '.
	'(SELECT id_users FROM users WHERE is_blocked = 0) AND '.
	'subject LIKE @search_subject'
);
$secondCommand->Parameters->Add('@search_subject', '%hello%');

$thirdCommand = 'SELECT * FROM files';

// etc...

// add commands to client
$client->Command = array($firtCommand, $secondCommand, $thirdCommand);

// and execute all command
$data = $client->GetData();

// output results

echo '<pre>';
foreach ($data as $table)
{
	print_r($table);
}
echo '</pre>';
```