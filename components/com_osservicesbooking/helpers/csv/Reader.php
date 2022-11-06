<?php
/**
 * @author Nicolas BUI <nbui@wanadoo.fr>
 * 
 * Copyright: 2002 Vitry sur Seine/FRANCE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
class Reader
{
	function Reader()
	{
		
	}
	
	function read(){}
	function ready(){}
	function close(){}
	function skip($counter=1){}
	function reset(){}
   
   	function is( &$object )
   	{
   		return is_subclass_of( $object, __CLASS__ );
   	}
}
?>