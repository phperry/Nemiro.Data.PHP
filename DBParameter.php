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
	 * Represents a query parameter.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	class DBParameter
	{
    
		/**
		 * Gets or sets name of the parameter.
		 * 
		 * @var \string
		 */
		public $Name;

		/**
		 * Gets or sets type of the parameter. Default: DBParameterType::String.
		 * 
		 * @var \string
		 */
		public $Type;

		/**
		 * Gets or sets value of the parameter.
		 * 
		 * @var mixed
		 */
		public $Value;

		/**
		 * Initializes a new instance of the DBParameter class with the specified parameters.
		 * 
		 * @param \string $name The parameter name.
		 * @param mixed $value The parameter value. Default: NULL.
		 * @param \string $type The parameter type. Default: DBParameterType::String.
		 * @throws \InvalidArgumentException 
		 */
		public function __construct($name, $value = NULL, $type = NULL)
		{
			if ($name == NULL || $name == '')
			{
				throw new \InvalidArgumentException('Parameter name is required.');
			}

			if ($type == NULL)
			{
				$type = DBParameterType::String;
			}

			$this->Name = $name;
			$this->Type = $type;
			$this->Value = $value;
		}

		/**
		 * Sets the parameter value to the current instance.
		 * 
		 * @param mixed $value The value to be set.
		 */
		public function SetValue($value)
		{
			$this->Value = $value;
		}

	}

}
?>