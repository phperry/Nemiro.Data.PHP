<?php
namespace Nemiro\Data
{

	/*
	 * Copyright © Aleksey Nemiro, 2015. All rights reserved.
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
	require_once 'PGException.php';
	require_once 'PgStmt.php';

	/**
	 * Client for PostgreSQL.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 * @version			1.1 (2015-10-18) / PHP 5 >= 5.1 / PostgreSQL >= 7.4
	 * @code
	 * # include the class file (use own path of the file location)
	 * require_once './Nemiro/Data/PgSql.php';
	 * 
	 * # import class
	 * use Nemiro\Data\PgSql as PgSql;
	 * 
	 * # insert record
	 * $client = new PgSql();
	 * $client->Query = 'INSERT INTO users (username, date_created) VALUES (@username, @date_created) RETURNING id_users;';
	 * $client->Parameters->Add('@date_created', date('d.m.Y'));
	 * $client->Parameters->Add('@username', 'test');
	 * $id = $client->ExecuteScalar(); # execute and get id
	 * echo sprintf('User id: %s', $id);
	 * 
	 * # select table
	 * $client = new PgSql();
	 * $client->Query = 'SELECT * FROM users';
	 * $table = $client->GetTable();
	 * 
	 * foreach($table as $row)
	 * {
	 *   echo sprintf('%s | %s | %s', array($row['id_users'], $row['username'], $row['date_created']));
	 * }
	 * @endcode
	 */
	class PgSql implements IDBClient
	{
		use TDBClient;

		/**
		 * PostgreSQL connection instance.
		 * 
		 * @var \resource
		 */
		private $Connection;
		
		/**
		 * Initializes a new instance of the PostgreSQL with the specified parameters.
		 * 
		 * @param \string $host Hostname of the PostgreSQL server. Default: PGSQL_DB_HOST OR localhost.
		 * @param \string $username The username. Default: PGSQL_DB_USER.
		 * @param \string $password The user password. Default: PGSQL_DB_PASSWORD.
		 * @param \string $database Database name. Default: PGSQL_DB_NAME.
		 * @param \int $port Port number of the PostgreSQL server. Default: PGSQL_DB_HOST OR 5432.
		 */
		function __construct($host = NULL, $username = NULL, $password = NULL, $database = NULL, $port = NULL) 
		{
			$this->Database = $database != NULL ? $database : (defined('PGSQL_DB_NAME') ? PGSQL_DB_NAME : '');
			$this->Username = $username != NULL ? $username : (defined('PGSQL_DB_USER') ? PGSQL_DB_USER : '');
			$this->Password = $password != NULL ? $password : (defined('PGSQL_DB_PASSWORD') ? PGSQL_DB_PASSWORD : '');
			$this->Host =			$host			!= NULL	? $host			: (defined('PGSQL_DB_HOST') ? PGSQL_DB_HOST : 'localhost');
			$this->Port =			$port			!= NULL ? $port			: (defined('PGSQL_DB_PORT') ? PGSQL_DB_PORT : 5432);
			$this->ConnectionMode = defined('PGSQL_DB_MODE') ? PGSQL_DB_MODE : ConnectionMode::Auto;

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

			if ($stmt->Execute() === FALSE)
			{
				$this->ThrowException($stmt);
			}

			// get result
			$result = $stmt->AffectedRows();
			
			// close
			$stmt->Close();

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			// return result
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
			if ($stmt->Execute() === FALSE)
			{
				$this->ThrowException($stmt);
			}
			else
			{
				// get result
				if (($result = $stmt->FetchRow()) === FALSE)
				{
					$this->ThrowException($stmt);
				}
				$result = $result[0];
			}

			// close
			$stmt->Close();

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			// return result
			return $result;
		}

		/**
		 * Executes the query and returns data row.
		 * 
		 * @return \array
		 */
		public function GetRow()
		{
			$command = DBCommand::GetCommandArray($this->Command)[0];
			$result = NULL;

			// prepare
			$stmt = $this->PrepareQuery($command->CommandText, $command->Parameters);

			// execute
			if ($stmt->Execute() === FALSE)
			{
				$this->ThrowException($stmt);
			}
			else
			{
				if (($result = $stmt->FetchRow()) === FALSE)
				{
					$this->ThrowException($stmt);
				}
			}

			// close
			$stmt->Close();

			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			// return result
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
				if ($stmt->Execute() === FALSE)
				{
					$this->ThrowException($stmt);
				}
				else
				{
					if (($fetch_result = $stmt->FetchAll()) === FALSE)
					{
						$this->ThrowException($stmt);
					}
					else
					{
						$result[] = $fetch_result;
					}
				}

				// close
				$stmt->Close();
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
			try
			{
				$this->Connection = \pg_connect
				(
					sprintf
					(
						'host=%s port=%s dbname=%s user=%s password=%s', 
						$this->Host,
						$this->Port,
						$this->Database,
						$this->Username,
						$this->Password
					)
				);
			}
			catch (\Exception $ex)
			{
				throw new PGException(($msg = $ex->getMessage()) != NULL ? $msg : 'Cold not connect to database.');
			}

			if (!$this->Connection) 
			{
				throw new PGException('Cold not connect to database.');
			}
		}

		/**
		 * Closes the connection.
		 * 
		 * @return void
		 */
		public function Disconnect()
		{
			if ($this->Connection != NULL && \pg_connection_status($this->Connection) === PGSQL_CONNECTION_OK)
			{
				\pg_close($this->Connection);
			}
		}

		/**
		 * Prepares the query to execution.
		 * 
		 * @param \string $commandText The SQL query.
		 * @param DBParameterCollection $commandParameters The query parameters.
		 * 
		 * @throws PGException
		 * @throws \InvalidArgumentException if the query is empty.
		 * 
		 * @return PgStmt
		 */
		private function PrepareQuery($commandText, $commandParameters)
		{
			if ($commandText == NULL || $commandText == '')
			{
				throw new \InvalidArgumentException('Command text is required. Value can not be empty.');
			}

			// open connection
			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Connect();
			
			// create stmt
			$stmt = new PgStmt($this->Connection, $commandText, $commandParameters);

			// prepare query
			if ($stmt->Prepare() === FALSE)
			{
				$this->ThrowException($stmt);
			}

			return $stmt;
		}

		/**
		 * Throws an exception.
		 * 
		 * @param PgStmt $stmt
		 * 
		 * @throws PGException 
		 * 
		 * @return void
		 */
		private function ThrowException($stmt = NULL)
		{
			$err = pg_last_error($this->Connection);
			
			if ($stmt != NULL) $stmt->Close();
			if ($this->ConnectionMode === ConnectionMode::Auto) $this->Disconnect();

			throw new PGException($err);
		}

	}

}
?>