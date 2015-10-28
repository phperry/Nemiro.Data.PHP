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
	 * The connection mode list.
	 * 
	 * @author			Aleksey Nemiro <aleksey@nemiro.ru>
	 * @copyright		© Aleksey Nemiro, 2015. All rights reserved.
	 */
	abstract class ConnectionMode
	{
    
		/**
		 * Manual open and close.
		 * 
		 * @return \int
		 */
		const Manual = 0;

		/**
		 * Automatic open and close for each qury.
		 * 
		 * @return \int
		 */
		const Auto = 1;

		/**
		 * Smart open and close (recomended).
		 * 
		 * @return \int
		 */
		const Smart = 2;

	}

}
?>