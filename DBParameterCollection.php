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
	 * Represents a collections of the query parameters.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	class DBParameterCollection implements \ArrayAccess
	{
    
		/**
		 * Gets or sets parameters array.
		 * 
		 * @var \array
		 */
		public $Items = array();

		public function __construct()
		{
		}

		/**
		 * Adds a new parameter to collection.
		 * 
		 * @param \string|DBParameter $name The name of the parameter. For example: @field_name OR ? OR %s (only one style for one collection).
		 * @param mixed $value The parameter value. Default: NULL.
		 * @param \string|DBParameterType $type The parameter type. You can use enums from the DBParameterType class. Default: DBParameterType::String.
		 * 
		 * @return DBParameter Returns instance of the added parameter.
		 */
		public function Add($name, $value = NULL, $type = NULL)
		{
			if ($this->Items == NULL) $this->Items = array();

			$t = gettype($name);

			if ($t == 'string')
			{
				$this->Items[] = new DBParameter($name, $value, $type);
			}
			else if ($t == 'object')
			{
				$this->Items[] = $name;
			}
			else
			{
				throw new \UnexpectedValueException(sprintf('Type %s is not supported. Expected string or DBParameter instance.', $t));
			}

			return end($this->Items); // $this->Items[count($this->Items) - 1];
		}

		/**
		 * Clears the collection.
		 * 
		 * @return void
		 */
		public function Clear()
		{
			unset($this->Items);
			$this->Items = array();
		}

		/**
		 * Returns types of parameters for bind_param.
		 * 
		 * @return \string
		 */
		public function GetTypes()
		{
			if ($this->Items == NULL) return '';

			$result = '';

			foreach ($this->Items as $item)
			{
				$result .= $item->Type;
			}

			return $result;
		}
		
		/**
		 * Return data type array of the parameters.
		 * 
		 * @return \string[]
		 */
		public function GetTypeArray()
		{
			if ($this->Items == NULL) return array();

			$result = array();

			foreach ($this->Items as $item)
			{
				$result[] = $item->Type;
			}

			return $result;
		}

		/**
		 * Return value array of the parameters.
		 * 
		 * @return \string[]
		 */
		public function GetValueArray()
		{
			if ($this->Items == NULL) return array();

			$result = array();

			foreach ($this->Items as $item)
			{
				$result[] = &$item->Value;
			}

			return $result;
		}

		/**
		 * Returns parameters count.
		 * 
		 * @return \int
		 */
		public function Count()
		{
			return $this->Items != NULL ? count($this->Items) : 0;
		}

		/**
		 * Checks the collection contains an element with the specified name or not.
		 * 
		 * @param mixed $parameterName Parameter name to search.
		 * 
		 * @return \bool
		 */
		public function Contains($parameterName)
		{
			return $this->Get($parameterName) != NULL;
		}

		/**
		 * Gets a parameter by name.
		 * 
		 * @param mixed $parameterName Parameter name to search.
		 * 
		 * @return DBParameter
		 */
		public function Get($parameterName)
		{
			if ($this->Items == NULL || $parameterName == NULL || $parameterName == '') return NULL;

			foreach($this->Items as $item) {
				if ($item->Name == $parameterName) 
				{
					return $item;
				}
			}

			return NULL;
		}

		
		#region ArrayAccess Members

		/**
		 * Whether a offset exists
		 * Whether or not an offset exists.
		 *
		 * @param mixed $offset An offset to check for.
		 *
		 * @return \bool
		 */
		function offsetExists($offset)
		{
			return isset($this->Items[$offset]);
		}

		/**
		 * Offset to retrieve
		 * Returns the value at specified offset.
		 *
		 * @param mixed $offset The offset to retrieve.
		 *
		 * @return DBParameter
		 */
		function offsetGet($offset)
		{
			if(is_int($offset))
			{
				return isset($this->Items[$offset]) ? $this->Items[$offset] : $this->Get($offset);
			}
			else
			{
				return $this->Get($offset);
			}
		}

		/**
		 * Offset to set
		 * Assigns a value to the specified offset.
		 *
		 * @param mixed $offset The offset to assign the value to.
		 * @param mixed $value The value to set.
		 *
		 * @return void
		 */
		function offsetSet($offset, $value)
		{
			$this->Items[$offset] = $value;
		}

		/**
		 * Offset to unset
		 * Unsets an offset.
		 *
		 * @param mixed $offset The offset to unset.
		 *
		 * @return void
		 */
		function offsetUnset($offset)
		{
			unset($this->Items[$offset]);
		}

		#endregion
	}

}
?>