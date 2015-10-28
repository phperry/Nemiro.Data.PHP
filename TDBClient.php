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
	 * Trait of a database client.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	trait TDBClient
	{

		/**
		 * Hostname of the db server.
		 * 
		 * @var \string
		 */
		public $Host = 'localhost';

		/**
		 * Port number of the db server.
		 * 
		 * @var \int
		 */
		public $Port = 0;

		/**
		 * Database name.
		 * 
		 * @var \string
		 */
		public $Database = '';

		/**
		 * Database user name.
		 * 
		 * @var \string
		 */
		public $Username = '';

		/**
		 * User password of the database.
		 * 
		 * @var \string
		 */
		public $Password = '';

		/**
		 * SQL query to excution.
		 * 
		 * @var \string|\string[]|DBCommand|DBCommand[]
		 */
		public $Command = '';

		/**
		 * Database connection mode. Default: 1 (ConnectionMode::Auto).
		 * 
		 * 0 - manual (use with Open and Close methods);
		 * 1 - auto (open and close for each qury);
		 * 2 - smart (recomended)
		 * 
		 * @var \int
		 */
		public $ConnectionMode = ConnectionMode::Auto;

	}

}
?>