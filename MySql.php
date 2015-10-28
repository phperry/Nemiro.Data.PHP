<?php
namespace Nemiro\Data
{

	/*
	 * Copyright © Aleksey Nemiro, 2007-2015. All rights reserved.
	 * 
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 * 
	 * http://www.apache.org/licenses/LICENSE-2.0
	 * 
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 */

	require_once 'ConnectionMode.php';
	require_once 'DBCommand.php';
	require_once 'IDBClient.php';
	require_once 'TDBClient.php';
	require_once 'DBParameterType.php';
	require_once 'DBParameter.php';
	require_once 'DBParameterCollection.php';

	/**
	 * Client for MySql.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2007-2009, 2015. All rights reserved.
	 * @version			2.1 (2015-10-18) / PHP 5 >= 5.3
	 * @code
	 * # include the class file (use own path of the file location)
	 * require_once './Nemiro/Data/MySql.php';
	 * 
	 * # import class
	 * use Nemiro\Data\MySql as MySql;
	 * 
	 * # insert record
	 * $client = new MySql();
	 * $client->Query = 'INSERT INTO users (username, date_created) VALUES (@username, @date_created)';
	 * $client->Parameters->Add('@date_created', date('Y-m-d H-i-s'));
	 * $client->Parameters->Add('@username', 'test');
	 * $id = $client->ExecuteScalar(); # execute and get id
	 * echo sprintf('User id: %s', $id);
	 * 
	 * # select table
	 * $client = new MySql();
	 * $client->Query = 'SELECT * FROM users';
	 * $table = $client->GetTable();
	 * 
	 * foreach($table as $row)
	 * {
	 *   echo sprintf('%s | %s | %s', array($row['id_users'], $row['username'], $row['date_created']));
	 * }
	 * @endcode
	 */
	class MySql implements IDBClient
	{
		use TDBClient;

		/**
		 * MySql connection instance.
		 * 
		 * @var \mysqli
		 */
		private $Connection;
		
		/**
		 * Initializes a new instance of the MySql with the specified parameters.
		 * 
		 * @param \string $host Hostname of the MySql server. Default: MYSQL_DB_HOST OR localhost.
		 * @param \string $username The username. Default: MYSQL_DB_USER.
		 * @param \string $password The user password. Default: MYSQL_DB_PASSWORD.
		 * @param \string $database Database name. Default: MYSQL_DB_NAME.
		 * @param \int $port Port number of the MySql server. Default: MYSQL_DB_HOST OR 3306.
		 */
		function __construct($host = NULL, $username = NULL, $password = NULL, $database = NULL, $port = NULL) 
		{
			$this->Database = $database != NULL ? $database : (defined('MYSQL_DB_NAME') ? MYSQL_DB_NAME : '');
			$this->Username = $username != NULL ? $username : (defined('MYSQL_DB_USER') ? MYSQL_DB_USER : '');
			$this->Password = $password != NULL ? $password : (defined('MYSQL_DB_PASSWORD') ? MYSQL_DB_PASSWORD : '');
			$this->Host =			$host			!= NULL	? $host			: (defined('MYSQL_DB_HOST') ? MYSQL_DB_HOST : 'localhost');
			$this->Port =			$port			!= NULL ? $port			: (defined('MYSQL_DB_PORT') ? MYSQL_DB_PORT : 3306);
			$this->ConnectionMode = defined('MYSQL_DB_MODE') ? MYSQL_DB_MODE : ConnectionMode::Auto;

			if ($this->ConnectionMode === ConnectionMode::Smart)
			{
				$this->Connect();
			}
		}

		function __destruct()
		{
			if ($this->ConnectionMode === ConnectionMode::Smart)
			{
				$this->Disconnect();
			}
		}
		
		/**
		 * Executes a SQL statement against the connection and returns the number of rows affected.
		 * 
		 * @return \int
		 */
		public function ExecuteNonQuery() 
		{
			$command = DBCommand::GetCommandArray($this->Command)[0];
			$result = 0;

			// prepare
			$stmt = $this->PrepareQuery($command->CommandText, $command->Parameters);

			// execute
			if ($stmt->execute() === FALSE)
			{
				$this->ThrowException($stmt);
			}
			else
			{
				// get result
				$result = $stmt->affected_rows;
			}
				
			// close
			$stmt->close();

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			return $result;
		}

		/**
		 * Executes the query, and returns the first column of the first row in the result set returned by the query.
		 * 
		 * @return mixed
		 */
		public function ExecuteScalar() 
		{
			$command = DBCommand::GetCommandArray($this->Command)[0];
			$result = NULL;

			// prepare
			$stmt = $this->PrepareQuery($command->CommandText, $command->Parameters);

			// execute
			if ($stmt->execute() === FALSE)
			{
				$this->ThrowException($stmt);
			}
			else
			{
				// get result
				if ($stmt->insert_id != 0)
				{
					// insert id
					$result = $stmt->insert_id;
				}
				else
				{
					// other
					if (($result = $stmt->get_result()) && ($result = $result->fetch_row()) !== FALSE)
					{
						$result = $result[0];
					}
					else
					{
						$this->ThrowException($stmt);
					}
				}
			}

			// close
			$stmt->close();

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			return $result;
		}

		/**
		 * Executes the query and returns data row.
		 * 
		 * @return \array
		 */
		public function GetRow()
		{
			$result = NULL;

			if (($result = $this->GetTable()) != NULL && count($result) > 0)
			{
				$result = $result[0];
			}

			return $result;
		}

		/**
		 * Executes the query and returns table.
		 * 
		 * @return \array[]
		 */
		public function GetTable() 
		{
			$result = $this->GetData();
			return (count($result) > 0) ? $result[0] : NULL;
		}
		
		/**
		 * Executes the query and returns DataSet.
		 * 
		 * @return \array[][]
		 */
		public function GetData() 
		{
			$commands = DBCommand::GetCommandArray($this->Command);
			$result = array();

			foreach($commands as $command)
			{
				// prepare
				$stmt = $this->PrepareQuery($command->CommandText, $command->Parameters);

				// execute
				if ($stmt->execute() === FALSE)
				{
					$this->ThrowException($stmt);
				}
				else
				{
					if (!($get_result = $stmt->get_result()) || ($get_result = $get_result->fetch_all(MYSQLI_ASSOC)) === FALSE)
					{
						$this->ThrowException($stmt);
					}
					else
					{
						$result[] = $get_result;
					}
				}

				// close
				$stmt->close();
			}

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			return $result;
		}

		/**
		 * Opens a connection.
		 * 
		 * @return void
		 */
		public function Connect()
		{
			$this->Connection = new \mysqli
			(
				$this->Host, 
				$this->Username, 
				$this->Password, 
				$this->Database, 
				$this->Port
			);

			if ($this->Connection->connect_errno) 
			{
				throw new \mysqli_sql_exception($this->Connection->connect_error);
			}
		}

		/**
		 * Closes the connection.
		 * 
		 * @return void
		 */
		public function Disconnect()
		{
			if ($this->Connection != NULL) //  && $this->Connection->ping()
			{
				$this->Connection->close();
			}
		}

		/**
		 * Prepares the query to execution.
		 * 
		 * @param \string $commandText The SQL query.
		 * @param DBParameterCollection $commandParameters The query parameters.
		 * 
		 * @throws \InvalidArgumentException if the query is empty.
		 * @throws \mysqli_sql_exception 
		 * 
		 * @return \mysqli_stmt
		 */
		private function PrepareQuery($commandText, $commandParameters)
		{
			if ($commandText == NULL || $commandText == '')
			{
				throw new \InvalidArgumentException('Command text is required. Value can not be empty.');
			}

			// open connection
			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Connect();

			// build query and parameters
			$query = $commandText;
			$parameters = new DBParameterCollection();
			$index = -1;

			$query = preg_replace_callback
			(
				'/(\@[^\s\,\;\.\)\(\{\}\[\]]+)|(\%s?)|(\?{1}?)/', 
				function($m) use ($index, $parameters, $commandParameters) {
					if ($m[0] == '%s' || $m[0] == '?')
					{
						$index++;
						$parameters->Add($commandParameters->Items[$index]);
						return '?';
					}
					else if (($parameter = $commandParameters->Get($m[0])) != NULL)
					{
						if ($index != -1)
						{
							throw new \ErrorException('Do not use explicit parameter names together with implicit.');
						}

						$parameters->Add($parameter);
						return '?';
					}
					else
					{
						return $m[0];
					}
				}, $query
			);

			// prepare
			if (($stmt = $this->Connection->prepare($query)) === FALSE)
			{
				$this->ThrowException($stmt);
			}

			// parameters
			if ($parameters != NULL && $parameters->Count() > 0)
			{
				$p = $parameters->GetValueArray();
				$types = $parameters->GetTypes();
				array_unshift($p, $types);
				if (!call_user_func_array(array($stmt, 'bind_param'), $p))
				{
					if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();
					throw new \mysqli_sql_exception('Culd not bind_param.');
				}
			}

			return $stmt;
		}

		/**
		 * Throws an exception.
		 * 
		 * @param \mysqli_stmt $stmt Instance of the stmt.
		 * 
		 * @throws \mysqli_sql_exception
		 * 
		 * @return void
		 */
		private function ThrowException($stmt = NULL)
		{
			$err = $this->Connection->error;
			if ($stmt != NULL && gettype($stmt) == 'object')
			{
				$err = $stmt->error;
				$stmt->close();
			}
			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();
			throw new \mysqli_sql_exception($err);
		}

	}

}
?>