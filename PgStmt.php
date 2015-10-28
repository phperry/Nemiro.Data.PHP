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

	/**
	 * Represents stmt for PostgreSQL.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	class PgStmt
	{
    
		private $Connection;

		/**
		 * The unique STMT name.
		 * 
		 * @var \string
		 */
		private $Name;

		/**
		 * The SQL query.
		 * 
		 * @var \string
		 */
		private $Query;

		/**
		 * The query parameters.
		 * 
		 * @var DBParameterCollection
		 */
		private $Parameters;

		/**
		 * The execution result.
		 * 
		 * @var \resource
		 */
		public $Result;

		/**
		 * Initializes a new instance of the PgStmt.
		 * 
		 * @param \resource $connection The PostgreSQL connection instance.
		 * @param \resource $stmt The PostgreSQL stmt instance.
		 * @param DBParameterCollection $params The query parameters array. Default: NULL.
		 */
		function __construct($connection, $query, $parameters = NULL)
		{
			$this->Parameters = new DBParameterCollection();
			$this->Name = uniqid();
			$this->Connection = $connection;
			$this->Result = NULL;

			// build query and parameters
			$index = -1;
			$this->Query = preg_replace_callback
			(
				'/(\@[^\s\,\;\.\)\(\{\}\[\]]+)|(\%s?)|(\?{1}?)/', 
				function($m) use ($index, $parameters) {
					if ($m[0] == '%s' || $m[0] == '?')
					{
						$index++;
						$this->Parameters->Add($parameters->Items[$index]);
						return '$'.$this->Parameters->Count();
					}
					else if (($parameter = $parameters->Get($m[0])) != NULL)
					{
						if ($index != -1)
						{
							throw new \ErrorException('Do not use explicit parameter names together with implicit.');
						}

						$this->Parameters->Add($parameter);

						return '$'.$this->Parameters->Count();
					}
					else
					{
						return $m[0];
					}
				}, $query
			);	
		}

		/*
		public function BindParam($params)
		{
			$this->Parameters = $params;
		}
		*/

		/**
		 * Submits a request to create a prepared statement with the given parameters, and waits for completion.
		 * 
		 * @return \resource
		 */
		public function Prepare()
		{
			return pg_prepare($this->Connection, $this->Name, $this->Query);
		}

		/**
		 * Sends a request to execute a prepared statement with given parameters, and waits for the result.
		 * 
		 * @return \resource
		 */
		public function Execute()
		{
			return ($this->Result = pg_execute($this->Connection, $this->Name, $this->Parameters->GetValueArray()));
		}

		/**
		 * Returns number of affected records.
		 * 
		 * @return \int
		 */
		public function AffectedRows()
		{
			return pg_affected_rows($this->Result);
		}

		/**
		 * Fetches all rows from a result as an array.
		 * 
		 * @return \array
		 */
		public function FetchAll()
		{
			return pg_fetch_all($this->Result);
		}

		/**
		 * Fetch a row as an associative array.
		 * 
		 * @return \array
		 */
		public function FetchAssoc()
		{
			return pg_fetch_assoc($this->Result);
		}

		/**
		 * Get a row as an enumerated array.
		 * 
		 * @return \array
		 */
		public function FetchRow()
		{
			return pg_fetch_row($this->Result);
		}

		/**
		 * Closes a prepared statement.
		 * 
		 * @return \resource
		 */
		public function Close()
		{
			return pg_query($this->Connection, sprintf('DEALLOCATE "%s"', $this->Name));
		}

	}

}
?>