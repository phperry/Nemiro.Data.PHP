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
	 * Represents command to a database.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	class DBCommand
	{
    
		/**
		 * Gets or sets SQL query.
		 * 
		 * @var \string
		 */
		public $CommandText;

		/**
		 * Parameters of the command.
		 * 
		 * @var DBParameterCollection
		 */
		public $Parameters;

		/**
		 * Initializes a new instance of the DBCommand class with the specified command text.
		 * 
		 * @param \string $commandText The command text (SQL query). For example: SELECT * FROM table;
		 */
		function __construct($commandText = NULL) 
		{
			$this->CommandText = $commandText;
			$this->Parameters = new DBParameterCollection();
		}
		
		/**
		 * Returns commands array.
		 * 
		 * @param \string|\string[]|DBCommand|DBCommand[] The command instance, command array or command text.
		 * @throws \UnexpectedValueException 
		 * @return DBCommand[]
		 */
		public static function GetCommandArray($command)
		{
			$commands = array();

			if (gettype($command) == 'string')
			{
				$commands[] = new DBCommand($command);
			}
			else if (gettype($command) == 'array')
			{
				foreach($command as $c)
				{
					if (gettype($c) == 'string')
					{
						$commands[] = new DBCommand($c);
					}
					else if (gettype($c) == 'object' && get_class($c) == 'Nemiro\Data\DBCommand')
					{
						$commands[] = $c;
					}
					else
					{
						throw new \UnexpectedValueException('Unexpected type of Command.');
					}
				}
			}
			else if (gettype($command) == 'object')
			{
				if (get_class($command) != 'Nemiro\Data\DBCommand') // !isset($command->CommandText)
				{
					throw new \UnexpectedValueException('Unexpected type of Command.');
				}

				$commands[] = $command;
			}
			else
			{
				throw new \UnexpectedValueException('Unexpected type of Command.');
			}

			return $commands;
		}

	}

}
?>