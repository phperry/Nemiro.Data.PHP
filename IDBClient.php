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
	 * Interface of a database client.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	interface IDBClient
	{
    
		/**
		 * Executes a SQL statement against the connection and returns the number of rows affected.
		 * 
		 * @return \int
		 */
		public function ExecuteNonQuery();

		/**
		 * Executes the query, and returns the first column of the first row in the result set returned by the query.
		 * 
		 * @return mixed
		 */
		public function ExecuteScalar();

		/**
		 * Executes the query and returns data row.
		 * 
		 * @return \array
		 */
		public function GetRow();

		/**
		 * Executes the query and returns table.
		 * 
		 * @return \array[]
		 */
		public function GetTable();

		/**
		 * Executes the query and returns DataSet (array of tables).
		 * 
		 * @return \array[][]
		 */
		public function GetData();

		/**
		 * Opens a database connection.
		 * 
		 * @return void
		 */
		public function Connect();

		/**
		 * Closes the database connection.
		 * 
		 * @return void
		 */
		public function Disconnect();

	}

}