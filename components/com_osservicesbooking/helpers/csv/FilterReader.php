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
require_once( dirname( __FILE__ ). '/Reader.php' );

class FilterReader extends Reader
{
   var $reader = NULL;
	
	function FilterReader( &$reader )
	{
		parent::Reader();
		if ( Reader::is( $reader ) )
      		$this->reader =& $reader;
	}
	
	function read()
   	{
      return $this->reader->read();
   	}
	
   	function ready()
   	{
		return $this->reader->ready();
   	}
	
   	function close()
   	{
      	$this->reader->close();
   	}
	
   	function skip( $counter = 1 )
   	{
      	$this->reader->skip($counter);
   	}
	
   	function reset()
   	{
      	$this->reader->reset();
   	}
   
   	function is(&$object)
   	{
      	return is_subclass_of( $object, __CLASS__ );
   	}
}
?>